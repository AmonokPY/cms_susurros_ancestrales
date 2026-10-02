<?php

return [
    'admin_email' => env('SECURITY_ADMIN_EMAIL', 'admin@example.com'),

    'password' => [
        'min' => 8,
        'uncompromised' => env('SECURITY_CHECK_UNCOMPROMISED', false),
        'common' => [
            'password', 'password1', 'password123', '12345678', '123456789',
            'qwerty123', 'admin123', 'letmein', 'welcome1', 'iloveyou',
            'abc12345', 'segura123', 'colombia', 'susurrosancestrales',
        ],
    ],

    'rate_limits' => [
        'login_per_minute' => 5,
        'register_per_hour' => 20,
        'verification_per_hour' => 5,
    ],

    'skip_mx_check' => env('SECURITY_SKIP_MX', true),

    'disposable_domains' => [
        'mailinator.com', 'guerrillamail.com', 'guerrillamail.net', 'sharklasers.com',
        'grr.la', 'tempmail.com', 'temp-mail.org', 'temp-mail.io', '10minutemail.com',
        '10minutemail.net', 'yopmail.com', 'yopmail.fr', 'trashmail.com', 'trashmail.de',
        'getnada.com', 'nada.ltd', 'discard.email', 'mailnesia.com', 'maildrop.cc',
        'throwawaymail.com', 'fakeinbox.com', 'emailondeck.com', 'moakt.com',
        'tmpmail.org', 'tmpmail.net', 'dispostable.com', 'mailcatch.com',
        'inboxbear.com', 'getairmail.com', 'mintemail.com', 'mytemp.email',
        'tempail.com', 'tempr.email', 'mail-temp.com', 'dropmail.me',
    ],
];
