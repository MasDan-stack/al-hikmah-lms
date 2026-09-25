<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Financial & Academic Settings
    |--------------------------------------------------------------------------
    */
    'registration_fee' => (int) env('ALHIKMAH_REGISTRATION_FEE', 150000),
    'rate_per_session' => (int) env('ALHIKMAH_RATE_PER_SESSION', 150000),
    'mentor_fee_per_session' => (int) env('ALHIKMAH_MENTOR_FEE', 100000),
    'infaq_percentage' => (int) env('ALHIKMAH_INFAQ_PERCENT', 10),

    /*
    |--------------------------------------------------------------------------
    | Health Check & Observability
    |--------------------------------------------------------------------------
    */
    'health_key' => env('HEALTH_CHECK_KEY', 'alhikmah-secure-probe-key-2026'),
    'health' => [
        'disk_warning_threshold_mb' => 500,
        'disk_critical_threshold_mb' => 100,
        'heartbeat_max_age_hours' => 25,
        'db_latency_warning_ms' => 100,
        'db_latency_critical_ms' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Automated Backup & GFS Retention Policy
    |--------------------------------------------------------------------------
    */
    'backup' => [
        'enabled' => env('BACKUP_ENABLED', true),
        'disk' => env('BACKUP_DISK', 's3'), // 's3', 'local', 'google'
        's3_provider' => env('BACKUP_S3_PROVIDER', 'wasabi'), // 'wasabi', 'idcloudhost', 'aws'
        'schedule' => [
            'daily' => '01:00',
            'weekly' => 'sunday 02:00',
            'monthly' => '01 03:00',
            'yearly' => '01-01 04:00',
        ],
        'retention' => [
            'daily' => 7,
            'weekly' => 4,
            'monthly' => 6,
            'yearly' => 3,
        ],
        'encrypt' => env('BACKUP_ENCRYPT', true),
        'verify_restore_weekly' => env('BACKUP_VERIFY_RESTORE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Emergency Alerting Channels
    |--------------------------------------------------------------------------
    */
    'alert' => [
        'telegram_bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'telegram_chat_id' => env('TELEGRAM_CHAT_ID'),
        'admin_email' => env('ALHIKMAH_ADMIN_EMAIL', 'admin@alhikmah.sch.id'),
        'whatsapp_gateway' => env('WA_GATEWAY_KEY'),
    ],
];
