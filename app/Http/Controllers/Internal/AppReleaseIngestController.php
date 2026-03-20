<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Services\AppReleaseService;
use Illuminate\Http\Request;

class AppReleaseIngestController extends Controller
{
    public function __construct(private readonly AppReleaseService $appReleaseService)
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
            'changelog' => 'nullable|string|max:2000',

            'bucket' => 'required|string',
            'endpoint' => 'required|string',
            'key_latest' => 'required|string',
            'key_commit' => 'required|string',
        ]);

        $release = $this->appReleaseService->ingest($data);

        return response()->json([
            'ok' => true,
            'id' => $release->id,
        ]);
    }
}
