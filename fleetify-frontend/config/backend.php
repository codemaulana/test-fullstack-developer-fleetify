<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Backend Base URL
    |--------------------------------------------------------------------------
    |
    | Centralize the backend API base URL to avoid hardcoding. Set via
    | BACKEND_BASE_URL in your .env. Defaults to http://localhost:8080.
    |
    */
    'base_url' => env('BACKEND_BASE_URL', 'http://localhost:8080'),
];