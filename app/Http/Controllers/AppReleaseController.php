<?php
namespace App\Http\Controllers;

use App\Models\AppRelease;
use Aws\S3\S3Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppReleaseController extends Controller
{
    public function latest(Request $request): JsonResponse
    {
        $platform = $request->query('platform', 'android');
        $channel = $request->query('channel', 'stable');

        $release = AppRelease::query()
            ->where('platform', $platform)
            ->where('channel', $channel)
            ->whereNotNull('released_at')
            ->orderByDesc('released_at')
            ->firstOrFail();

        return response()->json([
            'platform' => $release->platform,
            'channel' => $release->channel,
            'version' => $release->version,
            'build_number' => $release->build_number,
            'version_full' => $release->version_full,
            'download_url' => url("/api/marquer/app/latest/download?platform={$platform}&channel={$channel}"),
            'released_at' => optional($release->released_at)->toISOString(),
        ]);
    }

    public function downloadLatest(Request $request): RedirectResponse
    {
        $platform = $request->query('platform', 'android');
        $channel  = $request->query('channel', 'stable');

        $release = AppRelease::query()
            ->where('platform', $platform)
            ->where('channel', $channel)
            ->whereNotNull('released_at')
            ->orderByDesc('released_at')
            ->firstOrFail();

        $s3 = new S3Client([
            'version' => 'latest',
            'region' => config('s3.region'),
            'endpoint' => config('s3.endpoint'),
            'use_path_style_endpoint' => (bool) config('s3.use_path_style'),
            'credentials' => [
                'key' => config('s3.key'),
                'secret' => config('s3.secret'),
            ],
        ]);

        $cmd = $s3->getCommand('GetObject', [
            'Bucket' => config('s3.bucket'),
            'Key' => $release->object_key_latest,
            'ResponseContentType' => 'application/vnd.android.package-archive',
        ]);

        $presignedRequest = $s3->createPresignedRequest($cmd, '+15 minutes');
        $url = (string) $presignedRequest->getUri();

        return redirect()->away($url);
    }
}
