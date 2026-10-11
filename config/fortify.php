<?php

use Laravel\Fortify\Features;

return [
    'guard' => 'web',
    'middleware' => ['web'],
    'auth_middleware' => 'auth',
    'passwords' => 'users',
    'username' => 'email',
    'email' => 'email',
    'views' => true,
    'home' => '/studio/activities',
    'prefix' => 'studio',
    'domain' => null,
    'lowercase_usernames' => true,
    'limiters' => [
        'login' => 'studio-login',
        'passkeys' => null,
    ],
    'paths' => [
        'login' => null,
        'logout' => null,
        'password' => [
            'request' => null,
            'reset' => null,
            'email' => null,
            'update' => null,
            'confirm' => null,
            'confirmation' => null,
        ],
        'register' => null,
        'verification' => [
            'notice' => null,
            'verify' => null,
            'send' => null,
        ],
        'user-profile-information' => ['update' => null],
        'user-password' => ['update' => null],
        'two-factor' => [
            'login' => null,
            'enable' => null,
            'confirm' => null,
            'disable' => null,
            'qr-code' => null,
            'secret-key' => null,
            'recovery-codes' => null,
        ],
        'passkey' => [
            'login-options' => null,
            'login' => null,
            'confirm-options' => null,
            'confirm' => null,
            'registration-options' => null,
            'store' => null,
            'destroy' => null,
        ],
    ],
    'redirects' => [
        'login' => '/studio/activities',
        'logout' => '/studio/login',
        'password-confirmation' => '/studio/activities',
        'register' => '/studio/activities',
        'email-verification' => '/studio/activities',
        'password-reset' => '/studio/login',
    ],
    'features' => [
        Features::resetPasswords(),
        Features::updatePasswords(),
    ],
];
