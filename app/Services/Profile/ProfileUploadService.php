<?php

namespace App\Services\Profile;

use App\Services\S3ClientService;
use Illuminate\Support\Str;

readonly class ProfileUploadService
{
    public function __construct(
        private S3ClientService $s3ClientService
    ) {
    }

    public function generateUploadUrl(int $userId, string $type, string $contentType): array
    {
        $ext = $contentType === 'image/png' ? 'png' : 'jpg';
        $objectKey = "profiles/{$userId}/{$type}/" . Str::uuid() . ".{$ext}";
        $bucket = config('s3.public_bucket');

        $s3 = $this->s3ClientService->getClient();

        $cmd = $s3->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key' => $objectKey,
            'ContentType' => $contentType,
        ]);

        $presignedRequest = $s3->createPresignedRequest($cmd, '+15 minutes');
        $uploadUrl = (string) $presignedRequest->getUri();

        $endpoint = config('s3.endpoint');
        $publicUrl = rtrim($endpoint, '/') . "/{$bucket}/{$objectKey}";

        return [
            'upload_url' => $uploadUrl,
            'public_url' => $publicUrl,
            'object_key' => $objectKey,
        ];
    }
}
