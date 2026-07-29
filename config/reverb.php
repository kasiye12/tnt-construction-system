<?php
// File: config/reverb.php

return [
    'apps' => [
        [
            'app_id' => env('REVERB_APP_ID'),
            'key' => env('REVERB_APP_KEY'),
            'secret' => env('REVERB_APP_SECRET'),
            'max_connections' => 1000,
            'enable_client_messages' => true,
            'enable_statistics' => true,
        ],
    ],

    'allowed_origins' => ['*'],

    'options' => [
        'cluster' => env('REVERB_CLUSTER', 'mt1'),
        'encrypted' => true,
        'host' => env('REVERB_HOST', 'localhost'),
        'port' => env('REVERB_PORT', 8080),
        'scheme' => env('REVERB_SCHEME', 'http'),
    ],

    'scaling' => [
        'enabled' => env('REVERB_SCALING_ENABLED', false),
        'channel' => 'reverb',
    ],
];