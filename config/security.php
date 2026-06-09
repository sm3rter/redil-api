<?php

return [
    'rate_limit' => (int) env('RATE_LIMIT', 60),
    'rate_window' => (int) env('RATE_WINDOW', 60),
    'rate_limit_store' => env('RATE_LIMIT_STORE', 'apcu'),
    'csp' => "default-src 'none'; frame-ancestors 'none'; base-uri 'none'; form-action 'none';",
    'referrer_policy' => 'strict-origin-when-cross-origin',
    'permissions_policy' => 'geolocation=(), microphone=(), camera=()',
    'hsts' => [
        'enabled' => true,
        'max_age' => 31536000,
        'include_sub_domains' => true,
        'preload' => false,
    ],
];
