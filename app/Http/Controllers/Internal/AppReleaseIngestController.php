<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\AppRelease;
use App\Services\RabbitPublisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppReleaseIngestController extends Controller
{
    public function __construct(private readonly RabbitPublisher $publisher)
    {
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'platform' => 'required|string|in:android,ios',
            'channel' => 'required|string|in:stable,beta',
            'version' => 'required|string|max:32',
            'build_number' => 'nullable|string|max:20',
            'version_full' => 'nullable|string|max:64',
            'git_sha' => 'nullable|string|size:40',

            'bucket' => 'required|string',
            'endpoint' => 'required|string',
            'key_latest' => 'required|string',
            'key_commit' => 'required|string',
        ]);

        $buildNumber = null;
        if (!empty($data['build_number']) && ctype_digit($data['build_number'])) {
            $buildNumber = (int)$data['build_number'];
        }

        return DB::transaction(static function () use ($data, $buildNumber) {
            $release = AppRelease::updateOrCreate(
                ['platform' => $data['platform'], 'channel' => $data['channel'], 'version' => $data['version']],
                [
                    'build_number' => $buildNumber,
                    'version_full' => $data['version_full'] ?? null,
                    'git_sha' => $data['git_sha'] ?? null,
                    'bucket' => $data['bucket'],
                    'object_key_latest' => $data['key_latest'],
                    'object_key_commit' => $data['key_commit'],
                    'released_at' => now(),
                ]
            );

            DB::afterCommit(function () use ($release) {
                $this->publisher->publishAnalytics('app.released', [
                    'event_name' => 'app_released',
                    'properties' => [
                        'release_id' => $release->id,
                        'platform' => $release->platform,
                        'channel' => $release->channel,
                        'version' => $release->version,
                        'released_at' => $release->released_at,
                    ]
                ]);
            });

            return response()->json([
                'ok' => true,
                'id' => $release->id,
            ]);
        });
    }
}
