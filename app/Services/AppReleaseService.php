<?php

namespace App\Services;

use App\Models\AppRelease;

readonly class AppReleaseService
{
    private array $contentTypes;

    public function __construct(private RabbitPublisherService $publisher, private S3ClientService $s3ClientService)
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

        try {
            $this->publisher->publishAnalytics('app.fetched', [
                'event_name' => 'app_fetched',
                'properties' => [
                    'release_id' => $release->id,
                    'platform' => $release->platform,
                    'channel' => $release->channel,
                    'version' => $release->version,
                ]
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        return $release;
    }

    public function getDownloadUrl(string $platform, string $channel): string
    {
        $release = $this->getAppRelease($platform, $channel);

        $s3 = $this->s3ClientService->getClient();

        $cmd = $s3->getCommand('GetObject', [
            'Bucket' => config('s3.bucket'),
            'Key' => $release->object_key_latest,
            'ResponseContentType' => $this->contentTypes[$platform] ?? 'application/octet-stream',
        ]);

        $presignedRequest = $s3->createPresignedRequest($cmd, '+15 minutes');
        $url = (string)$presignedRequest->getUri();

        try {
            $this->publisher->publishAnalytics('app.downloaded', [
                'event_name' => 'app_downloaded',
                'properties' => [
                    'release_id' => $release->id,
                    'platform' => $release->platform,
                    'channel' => $release->channel,
                    'version' => $release->version,
                ]
            ]);
        } catch (\Throwable $e) {
            report($e);
        }

        return $url;
    }
}
