<?php

return [

    'providers' => [
        'newsapi' => [
            'enabled' => env('NEWSAPI_ENABLED', true),
            'api_key' => env('NEWSAPI_KEY'),
            'base_url' => 'https://newsapi.org/v2',
        ],

        'guardian' => [
            'enabled' => env('GUARDIAN_ENABLED', true),
            'api_key' => env('GUARDIAN_API_KEY'),
            'base_url' => 'https://content.guardianapis.com',
        ],

        'nytimes' => [
            'enabled' => env('NYTIMES_ENABLED', true),
            'api_key' => env('NYTIMES_API_KEY'),
            'base_url' => 'https://api.nytimes.com/svc',
        ],
    ],


    'fetch' => [
        'schedule' => env('NEWS_FETCH_SCHEDULE', '*/6'), // Every 6 hours
        'timeout' => env('NEWS_FETCH_TIMEOUT', 30),
        'max_articles_per_provider' => env('NEWS_MAX_ARTICLES', 100),
    ],
];
