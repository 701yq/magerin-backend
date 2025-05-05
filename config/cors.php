<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

   'paths' => ['api/*', 'sanctum/csrf-cookie'], //ini baru bos 2

    'allowed_methods' => ['*'],

    'allowed_origins' => ['http://localhost:5173'], //ini baru bos

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Authorization', 'Content-Type', 'X-Requested-With', 'Accept'], //ini baru bos 2

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

    

];
