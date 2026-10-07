<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'smsapi' => [
    'login' => env('SMSAPI_LOGIN'),
    'password' => env('SMSAPI_PASSWORD'),
    'sender' => env('SMSAPI_SENDER', 'SOGLOMAVLOD'),
],
'sms' => [
    'provider'  => env('SMS_PROVIDER', 'eskiz'),
    'login'     => env('SMS_LOGIN'),
    'password'  => env('SMS_PASSWORD'),
    'sender'    => env('SMS_SENDER', '4546'),
    'enabled'   => env('SMS_ENABLED', true),
    'test_mode' => env('SMS_TEST_MODE', true),
],

'iio' => [
    'phone' => env('IIO_PHONE', '+998993554567'),
],
];
