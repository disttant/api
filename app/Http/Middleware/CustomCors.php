<?php

namespace App\Http\Middleware;

use Closure;

class CustomCors
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

        # Headers when the method is OPTIONS
        if ($request->isMethod('options')) 
        {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
            header('Content-Type: text/plain; charset=UTF-8');
            header('Content-Length: 0');
            return response('', 204);
        }

        # Headers when the method is different than OPTIONS
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');

        return $next($request);

    }

}
