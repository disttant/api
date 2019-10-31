<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JwtController extends Controller
{
    /* *
     *
     * Returns an array with the payload decoded
     * 
     * */
    public function getPayload( Request $request )
    {
        # Check the existance of JWT in headers
        if( is_null($request->bearerToken()) ){
            return [];
        }

        # Get the coded JSON data from the JWT
        $bearerToken = explode( '.', $request->bearerToken() );
        $payload = base64_decode ( $bearerToken[1] );

        # Transform to array and return
        $payload = json_decode( $payload, true );

        if ( !is_array($payload) )
            return [];
        
        return $payload;  
    }

    /* *
     *
     * Returns a string with the given param
     * 
     * */
    public function getParam( Request $request, string $param = 'sub' )
    {
        # Instance the JWT Parser
        $jwt = new JwtController;
        $payload = $jwt->getPayload( $request );

        # Returns the field
        if ( !array_key_exists($param, $payload) )
            return [];

        return $payload[$param];
    }

    /* *
     *
     * Returns a string with the sandbox
     * 
     * */
    public function getSub( Request $request )
    {
        # Instance the JWT Parser
        $jwt = new JwtController;

        return $jwt->getParam( $request, 'sub' );
    }

    /* *
     *
     * Returns a string with the jti
     * 
     * */
    public function getJti( Request $request )
    {
        # Instance the JWT Parser
        $jwt = new JwtController;

        return $jwt->getParam( $request, 'jti' );
    }
}
