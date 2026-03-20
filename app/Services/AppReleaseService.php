<?php

namespace App\Services;

use App\Models\AppRelease;
use Illuminate\Support\Facades\DB;

readonly class AppReleaseService
{
    private array $contentTypes;

    public function __construct(private AnalyticsPublisherService $publisher, private S3ClientService $s3ClientService)
    {
        $this->contentTypes = [
            'android' => 'application/vnd.android.package-archive',
        ];
    }

    public function ingest(array $data): AppRelease
    {
        $buildNumber = null;
        if (!empty($data['build_number']) && ctype_digit($data['build_number'])) {
            $buildNumber = (int) $data['build_number'];
        }

        $attributes = [
            'build_number' => $buildNumber,
            'version_full' => $data['version_full'] ?? null,
            'git_sha' => $data['git_sha'] ?? null,
            'bucket' => $data['bucket'],
            'object_key_latest' => $data['key_latest'],
            'object_key_commit' => $data['key_commit'],
            'released_at' => now(),
        ];

        if (!empty($data['changelog'])) {
            $attributes['changelog'] = $data['changelog'];
        }

        return DB::transaction(function () use ($data, $attributes) {
            $release = AppRelease::updateOrCreate(
                ['platform' => $data['platform'], 'channel' => $data['channel'], 'version' => $data['version']],
                $attributes
            );

            DB::afterCommit(function () use ($release) {
                $this->publisher->publish('app_released', [
                    'release_id' => $release->id,
                    'platform' => $release->platform,
                    'channel' => $release->channel,
                    'version' => $release->version,
                    'released_at' => $release->released_at,
                ]);
            });

            return $release;
        });
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

        $this->publisher->publish('app_fetched', [
            'release_id' => $release->id,
            'platform' => $release->platform,
            'channel' => $release->channel,
            'version' => $release->version,
        ]);

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

        $this->publisher->publish('app_downloaded', [
            'release_id' => $release->id,
            'platform' => $release->platform,
            'channel' => $release->channel,
            'version' => $release->version,
        ]);

        return $url;
    }
}
