<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
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

        $url = config('analytics.url');
        $timeout = config('analytics.timeout');

        dispatch(function () use ($url, $timeout, $payload) {
            try {
                Http::timeout($timeout)->post($url, $payload);
            } catch (\Throwable $e) {
                report($e);
            }
        })->afterResponse();
    }
}
