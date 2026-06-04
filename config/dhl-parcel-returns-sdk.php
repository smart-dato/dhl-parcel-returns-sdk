<?php

return [
    'api_key' => env('DHL_PARCEL_RETURNS_API_KEY'),
    'username' => env('DHL_PARCEL_RETURNS_USERNAME'),
    'password' => env('DHL_PARCEL_RETURNS_PASSWORD'),
    'client_secret' => env('DHL_PARCEL_RETURNS_CLIENT_SECRET'),
    'base_url' => env('DHL_PARCEL_RETURNS_BASE_URL'),
    'oauth_base_url' => env('DHL_PARCEL_RETURNS_OAUTH_BASE_URL'),
    'sandbox' => env('DHL_PARCEL_RETURNS_SANDBOX', false),
];
