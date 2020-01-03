<?php

namespace App\Http\Middleware;

use Closure;

class RequestScopeChecker
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $scope)
    {
        
        # We need to have recieved a JWT
        if( is_null($request->bearerToken()) ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'bearer token not received'
            ], 400 )->send();
        }

        # Get the payload from the JWT and turn it into an array
        $payload = json_decode( base64_decode ( explode( '.', $request->bearerToken() )[1] ), true );

        
        # Check for the asked scope into payload
        if ( !in_array($scope, $payload['scopes']) ){
            return response()->json([
                'status'    => 'error',
                'message'   => 'needed scope not granted'
            ], 403 )->send();
        }

        # Right scope found, next layer
        return $next($request);
        
        //echo $response->getStatusCode(); # 200
        //echo $response->getHeaderLine('content-type'); # 'application/json; charset=utf8'
        //echo $response->getBody(); # '{"id": 1420053, "name": "guzzle", ...}'

    }

}
