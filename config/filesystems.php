<?php

return [

    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

        // ===============================
        // SUPABASE - SUBMISSIONS
        // ===============================
        'supabase' => [
            'driver' => 's3',
            'key' => env('SUPABASE_ACCESS_KEY_ID'),
            'secret' => env('SUPABASE_SECRET_ACCESS_KEY'),
            'region' => env('SUPABASE_DEFAULT_REGION', 'ap-southeast-1'),
            'bucket' => env('SUPABASE_SUBMISSIONS_BUCKET', 'submissions'),
            'endpoint' => env('SUPABASE_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'throw' => true,

            // FIX: cegah cURL error 55 / 100 Continue
            'options' => [
                '@http' => [
                    'expect' => false,
                    'version' => '1.1',
                    'connect_timeout' => 10,
                    'timeout' => 120,
                ],
            ],
        ],

        // ===============================
        // SUPABASE - CONSULTATIONS
        // ===============================
        'supabase_consultations' => [
            'driver' => 's3',
            'key' => env('SUPABASE_ACCESS_KEY_ID'),
            'secret' => env('SUPABASE_SECRET_ACCESS_KEY'),
            'region' => env('SUPABASE_DEFAULT_REGION', 'ap-southeast-1'),
            'bucket' => env('SUPABASE_CONSULTATIONS_BUCKET', 'consultations'),
            'endpoint' => env('SUPABASE_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'throw' => true,

            'options' => [
                '@http' => [
                    'expect' => false,
                    'version' => '1.1',
                    'connect_timeout' => 10,
                    'timeout' => 120,
                ],
            ],
        ],

        // ===============================
        // SUPABASE - COMPLAINTS
        // ===============================
        'supabase_complaints' => [
            'driver' => 's3',
            'key' => env('SUPABASE_ACCESS_KEY_ID'),
            'secret' => env('SUPABASE_SECRET_ACCESS_KEY'),
            'region' => env('SUPABASE_DEFAULT_REGION', 'ap-southeast-1'),
            'bucket' => env('SUPABASE_COMPLAINTS_BUCKET', 'complaints'),
            'endpoint' => env('SUPABASE_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'throw' => true,
        ],

        'supabase_ktp' => [
            'driver' => 's3',
            'key' => env('SUPABASE_ACCESS_KEY_ID'),
            'secret' => env('SUPABASE_SECRET_ACCESS_KEY'),
            'region' => env('SUPABASE_DEFAULT_REGION', 'ap-southeast-1'),
            'bucket' => env('SUPABASE_KTP_BUCKET', 'ktp-photos'),
            'endpoint' => env('SUPABASE_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'throw' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | ⁠ storage:link ⁠ Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];
