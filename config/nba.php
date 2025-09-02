<?php

return [

    'url' => env('NBA_API_URL', 'https://v2.nba.api-sports.io'),
    'key' => env('NBA_API_KEY'),

    'headers' => [
        'api-sports' => [
            'x-apisports-key' => env('NBA_API_KEY'),
        ],
        'rapidapi' => [
            'x-rapidapi-host' => 'api-nba-v1.p.rapidapi.com',
            'x-rapidapi-key'  => env('NBA_API_KEY'),
        ],
    ],

];
