<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Verification Token Length
     |--------------------------------------------------------------------------
     |
     | Here you can specify length of verification tokens which will send to users.
     |
     */
    'token_length' =>  env('VERIFICATION_TOKEN_LENGTH', 5),

    /*
     |--------------------------------------------------------------------------
     | Verification Token Lifetime
     |--------------------------------------------------------------------------
     |
     | Here you can specify lifetime of verification tokens (in minutes) which will send to users.
     |
     */
    'token_lifetime' => env('VERIFICATION_TOKEN_LIFE_TIME', 5),

    /*
    |--------------------------------------------------------------------------
    |  Token Storage Driver
    |--------------------------------------------------------------------------
    |
    | Here you may define token "storage" driver. If you choose the "cache", the token will be stored
    | in a cache driver configured by your application. Otherwise, a table will be created for storing tokens.
    |
    | Supported drivers: "cache", "database"
    |
    */
    'token_storage' => env('VERIFICATION_TOKEN_STORAGE', 'cache'),

    /*
    |--------------------------------------------------------------------------
    | Routes Prefix
    |--------------------------------------------------------------------------
    |
    | This is the routes prefix where Mobile-Verifier controller will be accessible from. Feel free
    | to change this path to anything you like.
    |
    */
    'routes_prefix' => 'auth',

    /*
     |--------------------------------------------------------------------------
     | Controller Routes
     |--------------------------------------------------------------------------
     |
     | Here you can specify your desired routes for verify and resend actions.
     |
     */
    'routes' => [
        'verify' => '/otp/verify',
        'resend' => '/otp/resend',
    ],

    /*
     |--------------------------------------------------------------------------
     | Throttle
     |--------------------------------------------------------------------------
     |
     | Here you can specify throttle for verify/resend routes
     |
     */
    'throttle' => 10,

    /*
     |--------------------------------------------------------------------------
     | Middleware
     |--------------------------------------------------------------------------
     |
     | Here you can specify which middleware you want to use for the APIs
     | For example: 'web', 'auth', 'auth:api', 'auth:sanctum'
     |
     */
    'middleware' => ['auth'],

    /*
     |--------------------------------------------------------------------------
     | Queue
     |--------------------------------------------------------------------------
     |
     | By default, This package does not queue sending verification messages.
     | But if you want your messages to process in a queue, change connection from sync to your preferred connection.
     | Be sure to config your queue settings in your .env file if you want to enable queue.
     |
     | Supported drivers: "sync", "database", "beanstalkd", "sqs", "redis", "null"
     |
     */
    'queue' =>  [
        'connection' => 'sync',
        'queue' => 'default',
        'tries' => 3,
        'timeout' => 60,
    ]
];
