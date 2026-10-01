<?php

return [
    'default' => env('FILESYSTEM_DISK', 's3'),

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            // Si no defines AWS_ACCESS_KEY_ID / AWS_SECRET_ACCESS_KEY se usa el IAM Role de la EC2.
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            // true: si S3 falla, lanza excepción en vez de fallar en silencio.
            'throw' => true,
        ],
    ],

    'links' => [],
];
