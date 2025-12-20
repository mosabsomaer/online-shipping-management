<?php

return [
    'api_key' => env('PLUTU_API_KEY'),
    'secret_key' => env('PLUTU_SECRET_KEY'),
    'access_token' => env('PLUTU_ACCESS_TOKEN'),
    'mode' => env('PLUTU_MODE', 'test'), // test or production
];
