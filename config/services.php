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

    /*
    |--------------------------------------------------------------------------
    | Universal Multi-Provider AI Configuration (Gemini, DeepSeek, Qwen, Claude, GPT)
    |--------------------------------------------------------------------------
    */
    'ai' => [
        'provider' => env('AI_PROVIDER', 'auto'), // auto, gemini, openai, deepseek, qwen, claude
        'api_key' => env('AI_API_KEY', env('GEMINI_API_KEY', '')),
        'model' => env('AI_MODEL', env('GEMINI_TEXT_MODEL', 'gemini-1.5-flash')),
        'base_url' => env('AI_BASE_URL', null),
        'max_retries' => (int) env('AI_MAX_RETRIES', env('GEMINI_MAX_RETRIES', 2)),
        'timeout' => (int) env('AI_TIMEOUT', env('GEMINI_TIMEOUT', 45)),

        'gemini' => [
            'api_key' => env('GEMINI_API_KEY', ''),
            'model' => env('GEMINI_TEXT_MODEL', 'gemini-1.5-flash'),
        ],

        'openai' => [
            'api_key' => env('OPENAI_API_KEY', ''),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        ],

        'deepseek' => [
            'api_key' => env('DEEPSEEK_API_KEY', ''),
            'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
            'base_url' => env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com/v1'),
        ],

        'qwen' => [
            'api_key' => env('QWEN_API_KEY', ''),
            'model' => env('QWEN_MODEL', 'qwen-plus'),
            'base_url' => env('QWEN_BASE_URL', 'https://dashscope-intl.aliyuncs.com/compatible-mode/v1'),
        ],

        'claude' => [
            'api_key' => env('CLAUDE_API_KEY', env('ANTHROPIC_API_KEY', '')),
            'model' => env('CLAUDE_MODEL', 'claude-3-5-sonnet-20241022'),
            'base_url' => env('CLAUDE_BASE_URL', 'https://api.anthropic.com/v1'),
        ],
    ],

    // Backward-compatibility configuration for legacy Gemini references
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY', env('AI_API_KEY', '')),
        'model' => env('GEMINI_TEXT_MODEL', env('AI_MODEL', 'gemini-1.5-flash')),
        'max_retries' => (int) env('GEMINI_MAX_RETRIES', env('AI_MAX_RETRIES', 2)),
        'timeout' => (int) env('GEMINI_TIMEOUT', env('AI_TIMEOUT', 45)),
    ],

];
