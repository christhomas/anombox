<?php

return [
    "allowed" => env('CORS_ALLOWED', ''),
    "default" => env('CORS_DEFAULT', ''),
    "headers" => env('CORS_HEADERS', ''),
    "methods" => env('CORS_METHODS', "GET, PUT, POST, DELETE, OPTIONS")
];
