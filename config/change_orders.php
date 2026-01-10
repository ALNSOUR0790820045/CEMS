<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Change Order Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the Change Orders Management module.
    |
    */

    // Default fee percentage (0.3% = 0.003)
    'default_fee_percentage' => env('CO_DEFAULT_FEE_PERCENTAGE', 0.003),

    // Stamp duty calculation settings
    'stamp_duty' => [
        'rate' => env('CO_STAMP_DUTY_RATE', 0.001), // 0.1%
        'minimum' => env('CO_STAMP_DUTY_MIN', 50), // Minimum 50 SAR
        'maximum' => env('CO_STAMP_DUTY_MAX', 10000), // Maximum 10,000 SAR
    ],

    // VAT rate (15% = 0.15)
    'vat_rate' => env('CO_VAT_RATE', 0.15),

    // CO number format
    'co_number_format' => env('CO_NUMBER_FORMAT', 'CO-%Y-%03d'), // CO-YYYY-NNN

    // Auto-assignment settings
    'auto_assign_pm' => env('CO_AUTO_ASSIGN_PM', true),
    'auto_assign_technical' => env('CO_AUTO_ASSIGN_TECHNICAL', true),
    'auto_assign_consultant' => env('CO_AUTO_ASSIGN_CONSULTANT', false),
    'auto_assign_client' => env('CO_AUTO_ASSIGN_CLIENT', false),

    // Approval workflow settings
    'require_all_signatures' => env('CO_REQUIRE_ALL_SIGNATURES', true),
    'allow_skip_levels' => env('CO_ALLOW_SKIP_LEVELS', false),

    // File upload settings
    'max_file_size' => env('CO_MAX_FILE_SIZE', 10240), // 10MB in KB
    'allowed_extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'dwg'],

    // Notification settings
    'notifications_enabled' => env('CO_NOTIFICATIONS_ENABLED', true),
    'reminder_days' => env('CO_REMINDER_DAYS', 7), // Remind after 7 days
];
