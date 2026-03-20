<?php

return [
    'enabled' => env('ANALYTICS_ENABLED', false),
    'url'     => env('ANALYTICS_URL', 'http://localhost:3000/api/analytics/track'),
    'timeout' => (int) env('ANALYTICS_TIMEOUT', 3),
];
