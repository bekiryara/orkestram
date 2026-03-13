<?php

return [
    'accounts_json' => env('ADMIN_ACCOUNTS_JSON', ''),
    'db_auth_enabled' => env('ADMIN_DB_AUTH_ENABLED', false),
    'basic' => [
        'user' => env('ADMIN_BASIC_USER', 'admin'),
        'pass' => env('ADMIN_BASIC_PASS', 'change-me'),
        'role' => env('ADMIN_BASIC_ROLE', 'super_admin'),
    ],
];

