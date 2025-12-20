<?php

return [
    'api_key' => env('TERMINAL49_API_KEY'),
    'base_url' => env('TERMINAL49_BASE_URL', 'https://api.terminal49.com'),
    'cache_ttl' => env('TERMINAL49_CACHE_TTL', 3600), // 1 hour in seconds
];
