<?php

namespace App\Http\Resources\AppRelease;

use App\Models\AppRelease;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/* @mixin AppRelease */
class AppReleaseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'platform' => $this->platform,
            'channel' => $this->channel,
            'version' => $this->version,
            'build_number' => $this->build_number,
            'version_full' => $this->version_full,
            'download_url' => url("/api/marquer/app/latest/download?platform={$this->platform}&channel={$this->channel}"),
            'released_at' => $this->released_at?->toISOString(),
        ];
    }
}
