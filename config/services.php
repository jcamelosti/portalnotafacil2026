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

    'spedy' => [
        'url' => env('SPEDY_API_URL', 'https://api.spedy.com.br/v1'),
        'api_key' => env('SPEDY_API_KEY'),
    ],

    'opencnpj' => [
        'url' => env('OPENCNPJ_API_URL', 'https://api.opencnpj.org'),
    ],

    'cnaes' => [
        'url' => env('API_CNAES_URL', 'https://cnaes.portalnotafacil.com.br/api/v1'),
        'email' => trim((string) env('API_CNAES_EMAIL')),
        'chave' => trim((string) env('API_CNAE_CHAVE')),
    ],
];
