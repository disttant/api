<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OAuth Internal URI
    |--------------------------------------------------------------------------
    |
    | This value determines the internal URI path where the OAuth server is running
    |
    */
    'oauth_server_internal_uri' => env('OAUTH_SERVER_INTERNAL_URI', 'http://192.168.0.4:8000'),



    /*
    |--------------------------------------------------------------------------
    | Access Token Validation Internal Path
    |--------------------------------------------------------------------------
    |
    | This value determines the internal path where the API services are
    | going to check the token before granting access to the resources
    |
    */
    'check_token_uri' => env('CHECK_TOKEN_URI', '/internal/oauth/access_token/validate'),


];