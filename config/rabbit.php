<?php

return [
    'host' => env('RABBITMQ_HOST'),
    'port' => (int)env('RABBITMQ_PORT'),
    'user' => env('RABBITMQ_USER'),
    'password' => env('RABBITMQ_PASSWORD'),
    'vhost' => env('RABBITMQ_VHOST'),
    'enabled' => env('RABBITMQ_ENABLED', true)
];
