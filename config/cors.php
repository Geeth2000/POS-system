<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure CORS settings for your application. This
    | configuration is important for allowing cross-origin requests to your API.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',    // React/Vue dev server
        'http://localhost:8080',    // Alternative frontend port
        'http://localhost:5173',    // Vite dev server
        'http://127.0.0.1:3000',
        'http://127.0.0.1:8080',
        'http://127.0.0.1:5173',
        // In production, add your frontend domain
        // 'https://yourdomain.com',
    ],

    'allowed_origins_patterns' => [
        // Allow localhost with any port during development
        '#^http://localhost:\d+$#',
        '#^http://127\.0\.0\.1:\d+$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['Authorization'],

    'max_age' => 0,

    'supports_credentials' => true,

];
