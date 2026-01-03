<?php

namespace App\Services;

use App\Models\AppRelease;
use Aws\S3\S3Client;

readonly class AppReleaseService
{
    private array $contentTypes;

    public function __construct(private RabbitPublisher $publisher)
    {
        $this->contentTypes = [
            'android' => 'application/vnd.android.package-archive',
        ];
    }


    private function getAppRelease(string $platform, string $channel): AppRelease
    {
        return AppRelease::query()
            ->where('platform', $platform)
            ->where('channel', $channel)
            ->whereNotNull('released_at')
            ->orderByDesc('released_at')
            ->firstOrFail();
    }

    public function getLatest(string $platform, string $channel): AppRelease
    {
        $release = $this->getAppRelease($platform, $channel);

        $this->publisher->publishAnalytics('app.fetched', [
            'event_name' => 'app_fetched_latest',
            'properties' => [
                'release_id' => $release->id,
                'platform' => $release->platform,
                'channel' => $release->channel,
                'version' => $release->version,
            ]
        ]);

        return $release;
    }

    public function getDownloadUrl(string $platform, string $channel): string
    {
        $release = $this->getAppRelease($platform, $channel);

        $s3 = new S3Client([
            'version' => 'latest',
            'region' => config('s3.region'),
            'endpoint' => config('s3.endpoint'),
            'use_path_style_endpoint' => (bool)config('s3.use_path_style'),
            'credentials' => [
                'key' => config('s3.key'),
                'secret' => config('s3.secret'),
            ],
        ]);

        $cmd = $s3->getCommand('GetObject', [
            'Bucket' => config('s3.bucket'),
            'Key' => $release->object_key_latest,
            'ResponseContentType' => $this->contentTypes[$platform] ?? 'application/octet-stream',
        ]);

        $presignedRequest = $s3->createPresignedRequest($cmd, '+15 minutes');
        $url = (string)$presignedRequest->getUri();

        $this->publisher->publishAnalytics('app.downloaded', [
            'event_name' => 'app_downloaded_latest',
            'properties' => [
                'release_id' => $release->id,
                'platform' => $release->platform,
                'channel' => $release->channel,
                'version' => $release->version,
            ]
        ]);

        return $url;
    }
}
