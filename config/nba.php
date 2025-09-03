<?php

return [
    'url' => env('NBA_API_URL'),
    'key' => env('NBA_API_KEY'),
    'headers' => [
        'rapidapi' => [
            'x-rapidapi-host' => 'nba-api-free-data.p.rapidapi.com',
            'x-rapidapi-key'  => env('NBA_API_KEY'),
        ],
    ],
];
