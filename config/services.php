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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'medical_ai' => [
        'enabled' => env('MEDICAL_AI_MICROSERVICE_ENABLED', false),
        'async_enabled' => env('MEDICAL_AI_ASYNC_ENABLED', false),
        'classification_url' => env('MEDICAL_AI_CLASSIFICATION_URL', 'http://classification-service:8001'),
        'segmentation_url' => env('MEDICAL_AI_SEGMENTATION_URL', 'http://segmentation-service:8002'),
        'timeout' => env('MEDICAL_AI_TIMEOUT', 600),
    ],

];
