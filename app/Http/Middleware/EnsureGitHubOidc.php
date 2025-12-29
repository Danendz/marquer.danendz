<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGitHubOidc
{
    public function handle(Request $request, Closure $next): Response
    {
        $jwt = $this->bearerToken($request);
        if (!$jwt) return response('Missing bearer token', 401);

        [$h, $p, $s] = array_pad(explode('.', $jwt), 3, null);
        if (!$h || !$p || !$s) return response('Invalid JWT format', 401);

        $header = json_decode($this->b64urlDecode($h), true) ?? [];
        $payload = json_decode($this->b64urlDecode($p), true) ?? [];

        if (($payload['iss'] ?? null) !== 'https://token.actions.githubusercontent.com') {
            return response('Invalid issuer', 401);
        }

        $expectedAud = config('github_oidc.audience');
        $aud = $payload['aud'] ?? null;
        if (is_array($aud)) {
            if (!in_array($expectedAud, $aud, true)) return response('Invalid audience', 401);
        } else {
            if ($aud !== $expectedAud) return response('Invalid audience', 401);
        }

        if (($payload['repository'] ?? null) !== config('github_oidc.repository')) {
            return response('Invalid repository', 403);
        }

        if (($payload['ref'] ?? null) !== config('github_oidc.ref')) {
            return response('Invalid ref', 403);
        }

        $now = time();
        if (isset($payload['nbf']) && $now < (int)$payload['nbf']) return response('Token not active', 401);
        if (isset($payload['exp']) && $now >= (int)$payload['exp']) return response('Token expired', 401);

        $kid = $header['kid'] ?? null;
        if (!$kid) return response('Missing kid', 401);

        $jwk = $this->findJwkByKid($kid);
        if (!$jwk) return response('Unknown kid', 401);

        if (!$this->verifyRs256($h . '.' . $p, $s, $jwk)) {
            return response('Bad signature', 401);
        }

        return $next($request);
    }

    private function bearerToken(Request $request): ?string
    {
        $auth = $request->header('Authorization', '');
        if (!str_starts_with($auth, 'Bearer ')) return null;
        return trim(substr($auth, 7));
    }

    private function b64urlDecode(string $s): string
    {
        $s = strtr($s, '-_', '+/');
        $pad = strlen($s) % 4;
        if ($pad) $s .= str_repeat('=', 4 - $pad);
        return base64_decode($s) ?: '';
    }

    private function findJwkByKid(string $kid): ?array
    {
        $jwksUrl = 'https://token.actions.githubusercontent.com/.well-known/jwks';
        $json = cache()->remember('github_oidc_jwks', 3600, fn() => @file_get_contents($jwksUrl) ?: '');
        $jwks = json_decode($json, true);

        foreach (($jwks['keys'] ?? []) as $key) {
            if (($key['kid'] ?? null) === $kid) return $key;
        }
        return null;
    }

    private function verifyRs256(string $signingInput, string $sigB64, array $jwk): bool
    {
        $pem = $this->jwkToPem($jwk);
        if (!$pem) return false;

        $sig = $this->b64urlDecode($sigB64);
        return openssl_verify($signingInput, $sig, $pem, OPENSSL_ALGO_SHA256) === 1;
    }

    private function jwkToPem(array $jwk): ?string
    {
        if (($jwk['kty'] ?? null) !== 'RSA' || empty($jwk['n']) || empty($jwk['e'])) return null;

        $n = $this->b64urlDecode($jwk['n']);
        $e = $this->b64urlDecode($jwk['e']);

        $rsaPubKey = $this->asn1Seq($this->asn1Int($n) . $this->asn1Int($e));

        $algId = $this->asn1Seq(
            $this->asn1Oid("\x2a\x86\x48\x86\xf7\x0d\x01\x01\x01") .
            "\x05\x00"
        );

        $spki = $this->asn1Seq($algId . $this->asn1BitString($rsaPubKey));

        return "-----BEGIN PUBLIC KEY-----\n" .
            chunk_split(base64_encode($spki), 64, "\n") .
            "-----END PUBLIC KEY-----\n";
    }

    private function asn1Len(int $len): string
    {
        if ($len < 0x80) return chr($len);
        $bytes = ltrim(pack('N', $len), "\x00");
        return chr(0x80 | strlen($bytes)) . $bytes;
    }

    private function asn1Seq(string $inner): string
    {
        return "\x30" . $this->asn1Len(strlen($inner)) . $inner;
    }

    private function asn1Int(string $bytes): string
    {
        if ($bytes === '') $bytes = "\x00";
        if ((ord($bytes[0]) & 0x80) !== 0) $bytes = "\x00" . $bytes;
        return "\x02" . $this->asn1Len(strlen($bytes)) . $bytes;
    }

    private function asn1Oid(string $oidBytes): string
    {
        return "\x06" . $this->asn1Len(strlen($oidBytes)) . $oidBytes;
    }

    private function asn1BitString(string $bytes): string
    {
        $payload = "\x00" . $bytes;
        return "\x03" . $this->asn1Len(strlen($payload)) . $payload;
    }
}
