<?php

namespace App\Http\Middleware;

use Closure;

class CustomJwt
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $_guzzle = new \GuzzleHttp\Client([
            'base_uri'    => config('internals.oauth_uri'),
            'http_errors' => false
        ]);

        if( $request->hasHeader('authorization') === false ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Authorization header not found'
            ], 400 )->send();
        }
        
        if( is_null($request->bearerToken()) ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bearer token not received'
            ], 400 )->send();
        }

        $response = $_guzzle->get( config('internals.oauth_check_token_route'), [
            'headers' => [ 
                'Authorization' => 'Bearer ' . $request->bearerToken(),
                'Accept'        => 'application/json'
             ]
        ]);

        if( $response->getStatusCode() >= 300 ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Bearer token validation failed'
            ], 401 )->send();
        }

        return $next($request);

        //echo $response->getStatusCode(); # 200
        //echo $response->getHeaderLine('content-type'); # 'application/json; charset=utf8'
        //echo $response->getBody(); # '{"id": 1420053, "name": "guzzle", ...}'
        
    }

}
