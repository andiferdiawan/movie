<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, SparkPost and others. This file provides a sane default
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

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'omdb' => [
        'base_url' => env('OMDB_BASE_URL', 'https://www.omdbapi.com/'),
        'api_key' => env('OMDB_API_KEY'),
        'cache_minutes' => env('OMDB_CACHE_MINUTES', 60),
        // OMDb has no "list all movies" endpoint, so the movie list page
        // seeds itself with this keyword until the user searches for something else.
        'default_query' => env('OMDB_DEFAULT_QUERY', 'Batman'),
    ],

];
