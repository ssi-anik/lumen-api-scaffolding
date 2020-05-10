<?php

return [

    'default' => env('QUEUE_CONNECTION', 'sync'),

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver'      => 'database',
            'table'       => env('QUEUE_TABLE', 'jobs'),
            'queue'       => 'default',
            'retry_after' => 90,
        ],

        'beanstalkd' => [
            'driver'      => 'beanstalkd',
            'host'        => env('BS_HOST', 'localhost'),
            'port'        => env('BS_PORT', 11300),
            'queue'       => env('BS_DEFAULT_QUEUE', 'default'),
            'retry_after' => 90,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key'    => env('SQS_KEY', 'your-public-key'),
            'secret' => env('SQS_SECRET', 'your-secret-key'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue'  => env('SQS_QUEUE', 'your-queue-name'),
            'region' => env('SQS_REGION', 'us-east-1'),
        ],

        'redis' => [
            'driver'      => 'redis',
            'connection'  => env('QUEUE_REDIS_CONNECTION', 'default'),
            'queue'       => env('QUEUE_REDIS_DEFAULT_QUEUE', 'default'),
            'retry_after' => 90,
            'block_for'   => null,
        ],

    ],

    'failed' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table'    => env('QUEUE_FAILED_TABLE', 'failed_jobs'),
    ],
];
