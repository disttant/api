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
    'oauth_uri' => env('OAUTH_URI', 'http://accounts.server'),



    /*
    |--------------------------------------------------------------------------
    | Access Token Validation Internal Path
    |--------------------------------------------------------------------------
    |
    | This value determines the internal path where the API services are
    | going to check the token before granting access to the resources
    |
    */
    'oauth_check_token_route' => env('OAUTH_CHECK_TOKEN_ROUTE', '/internal/oauth/access_token/validate'),


];