<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthorizationController extends Controller
{

    public $_guzzle;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_guzzle = new \GuzzleHttp\Client([
            'base_uri' => 'http://192.168.0.4:8000',
            'http_errors' => false
        ]);
    }

    /**
     * Show the profile of the user.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function validationRequest(Request $request)
    {

        if( $request->hasHeader('authorization') === false ){
            response()->json([
                'status'    => 'error',
                'message'   => 'Authorization header not found'
            ], 400 )->send();
        }
        
        if( is_null($request->bearerToken()) ){
            response()->json([
                'status'    => 'error',
                'message'   => 'Bearer token not received'
            ], 400 )->send();
        }

        $response = $this->_guzzle->get('/internal/oauth/access_token/validate', [
            'headers' => [ 
                'Authorization' => 'Bearer ' . $request->bearerToken(),
                'Accept'        => 'application/json'
             ]
        ]);

        if( $response->getStatusCode() >= 300 ){
            response()->json([
                'status'    => 'error',
                'message'   => 'Bearer token validation failed'
            ], 401 )->send();
        }

        //echo $response->getStatusCode(); # 200
        //echo $response->getHeaderLine('content-type'); # 'application/json; charset=utf8'
        //echo $response->getBody(); # '{"id": 1420053, "name": "guzzle", ...}'

    }
}
