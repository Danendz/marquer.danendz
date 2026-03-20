<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class PublishAnalyticsEvent implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $url,
        public int $timeout,
        public array $payload,
    ) {}

    public function handle(): void
    {
        Http::timeout($this->timeout)->post($this->url, $this->payload)->throw();
    }
}
