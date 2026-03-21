<?php

return [
    'endpoint' => env('S3_ENDPOINT'),
    'region'   => env('S3_REGION'),
    'key'      => env('S3_ACCESS_KEY_ID'),
    'secret'   => env('S3_SECRET_ACCESS_KEY'),
    'bucket'   => env('S3_BUCKET'),
    'public_bucket' => env('S3_PUBLIC_BUCKET', 'marquer-public'),
    'use_path_style' => env('S3_PATH_STYLE', true),
];
