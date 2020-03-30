<?php

namespace App\Http\Middleware;

use Closure;

class CustomHeaders
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

        return $next($request);
        
    }

}
