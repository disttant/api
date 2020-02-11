<?php

namespace App\Http\Middleware;

use Closure;

class RequestPrechecker
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

        if( $request->hasHeader('content-type') === false ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Content-Type header not found'
            ], 400 )->send();
        }

        if( $request->hasHeader('accept') === false ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Accept header not found'
            ], 400 )->send();
        }

        if( $request->isJson() === false ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Wrong Content-Type header: This API only can understand JSON'
            ], 400 )->send();
        }

        if( $request->wantsJson() === false ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'Wrong Accept header: This API can just response JSON'
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
