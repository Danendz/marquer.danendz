<?php

namespace App\Services;

use App\Jobs\PublishAnalyticsEvent;
use Illuminate\Support\Str;

class AnalyticsPublisherService
{
    public function publish(string $eventName, array $properties): void
    {
        if (!config('analytics.enabled')) {
            return;
        }

        $payload = [
            'event_id' => (string) Str::uuid(),
            'app_name' => config('app.name'),
            'user_id' => auth()->id() !== null ? (string) auth()->id() : null,
            'event_name' => $eventName,
            'properties' => $properties,
        ];

        PublishAnalyticsEvent::dispatch(
            config('analytics.url'),
            config('analytics.timeout'),
            $payload,
        );
    }
}
