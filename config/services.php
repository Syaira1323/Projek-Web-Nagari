<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'huggingface' => [
        'api_key' => env('HUGGINGFACE_API_KEY', ''),
        'model' => env('HUGGINGFACE_MODEL', 'Qwen/Qwen2.5-7B-Instruct'),
    ],

    'fastapi' => [
        'url' => env('FASTAPI_URL', 'http://127.0.0.1:8000'),
        'timeout' => (int) env('FASTAPI_TIMEOUT', 10),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID', env('GOOGLE_DRIVE_CLIENT_ID', '')),
        'client_secret' => env('GOOGLE_CLIENT_SECRET', env('GOOGLE_DRIVE_CLIENT_SECRET', '')),
        'redirect_uri' => env('GOOGLE_REDIRECT_URI', env('APP_URL', 'http://localhost') . '/admin/gdrive/callback'),
        'refresh_token' => env('GOOGLE_DRIVE_REFRESH_TOKEN', null),
    ],

    'gdrive_arsip_url' => env('GOOGLE_DRIVE_ARSIP_URL', 'https://drive.google.com/'),

];
