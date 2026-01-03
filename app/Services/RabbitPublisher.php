<?php

namespace App\Services;

use Illuminate\Support\Str;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitPublisher
{
    /**
     * @throws \JsonException
     * @throws \Exception
     */
    private function publish(string $routingKey, array $payload): void
    {
        $conn = new AMQPStreamConnection(
            config('rabbit.host'),
            config('rabbit.port'),
            config('rabbit.user'),
            config('rabbit.password'),
            config('rabbit.vhost'),
        );

        $ch = $conn->channel();

        $exchange = 'events';
        $ch->exchange_declare($exchange, 'topic', false, true, false);

        $msg = new AMQPMessage(
            json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            ['content_type' => 'application/json', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        $ch->basic_publish($msg, $exchange, $routingKey);

        $ch->close();
        $conn->close();
    }

    public function publishAnalytics(string $key, array $payload): void
    {
        if (!config('rabbit.enabled')) {
            return;
        }

        $this->publish($key, [
            ...$payload,
            'app_name' => config('app.name'),
            'user_id' => auth()->id(),
            'event_id' => (string) Str::uuid(),
        ]);
    }
}
