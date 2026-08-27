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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'pakasir' => [
        'api_key' => env('PAKASIR_API_KEY', 'wakGifjocg8pjIxFjMQXXJcNjvjkGQd1'),
        'project_slug' => env('PAKASIR_PROJECT_SLUG', 'al-hikmah'),
        'base_url' => env('PAKASIR_BASE_URL', 'https://app.pakasir.com/api'),
        'webhook_url' => env('PAKASIR_WEBHOOK_URL', 'https://stopped-preseason-unskilled.ngrok-free.dev/api/webhook/pakasir'),
        'fee_payer' => env('PAKASIR_FEE_PAYER', 'customer'), // 'customer' or 'merchant'
        'fee_qris_percent' => (float) env('PAKASIR_FEE_QRIS_PERCENT', 0.7),
        'fee_va_flat' => (int) env('PAKASIR_FEE_VA_FLAT', 3500),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY', ''),
        'model' => env('GEMINI_TEXT_MODEL', 'gemini-3.6-flash'),
        'max_retries' => (int) env('GEMINI_MAX_RETRIES', 1),
        'timeout' => (int) env('GEMINI_TIMEOUT', 30),
    ],

];
