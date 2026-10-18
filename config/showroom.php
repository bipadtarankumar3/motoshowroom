<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MotoShowroom SaaS Global Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for multi-tenancy, subscription defaults, AI assistants,
    | and platform wide settings.
    |
    */

    'platform_name' => env('APP_NAME', 'MotoShowroom SaaS'),
    'platform_domain' => env('PLATFORM_DOMAIN', 'localhost'),

    'currency' => [
        'default' => 'INR',
        'symbol' => '₹',
    ],

    'tenancy' => [
        'subdomain_enabled' => true,
        'path_prefix' => 'showrooms',
        'reserved_slugs' => [
            'admin', 'superadmin', 'platform', 'api', 'auth', 'login',
            'register', 'password', 'billing', 'docs', 'assets', 'build',
        ],
    ],

    'trial' => [
        'default_days' => 14,
        'auto_assign_starter' => true,
    ],

    'ai_assistant' => [
        'provider' => env('AI_PROVIDER', 'simulated'),
        'api_key' => env('AI_API_KEY'),
        'model' => env('AI_MODEL', 'gpt-4o-mini'),
        'rate_limit_per_minute' => 15,
        'fallback_message' => 'Our virtual showroom assistant is temporarily taking a pit stop. Please call or submit an enquiry and our team will get right back to you!',
    ],

    'media' => [
        'max_image_size_kb' => 5120, // 5MB
        'max_3d_model_size_kb' => 25600, // 25MB
        'max_resume_size_kb' => 10240, // 10MB
    ],
];
