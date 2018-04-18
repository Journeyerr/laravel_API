<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'chuanglan' => [
        'api_notice_account'=> env('API_NOTICE_ACCOUNT'),
        'api_notice_password'=> env('API_NOTICE_PASSWORD'),
        'api_marketing_account'=> env('API_MARKETING_ACCOUNT'),
        'api_marketing_password'=> env('API_MARKETING_PASSWORD'),
        'api_send_url'=> env('API_SEND_URL'),
        'api_balance_query_url'=> env('API_BALANCE_QUERY_URL'),
    ],

    'wechat' => [
        'appId' => env('WECHAT_APP_ID'),
        'appsecret' => env('WECHAT_APP_SECRET'),
        'redirect_url' => env('REDIRECT_URI '),
        'scope' => env('WECHAT_APP_SCOPE '),
    ],

    'is_send_true_smg' => env('IS_SEND_TRUE_SMGS', false),

];
