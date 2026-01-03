<?php

namespace App\Services;

use Aws\S3\S3Client;

class S3ClientService
{
    private S3Client $s3;

    public function __construct()
    {
        $this->s3 = new S3Client([
            'version' => 'latest',
            'region' => config('s3.region'),
            'endpoint' => config('s3.endpoint'),
            'use_path_style_endpoint' => (bool)config('s3.use_path_style'),
            'credentials' => [
                'key' => config('s3.key'),
                'secret' => config('s3.secret'),
            ],
        ]);
    }

    public function getClient(): S3Client
    {
        return $this->s3;
    }
}
