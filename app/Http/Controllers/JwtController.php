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
    public static function getPayload( Request $request )
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
    public static function getParam( Request $request, string $param = 'sub' )
    {

        $payload = self::getPayload( $request );

        # Returns the field
        if ( !array_key_exists($param, $payload) )
            return [];

        return $payload[$param];
    }



    /* *
     *
     * Returns a string with the user
     *
     * */
    public static function getSub( Request $request )
    {
        return self::getParam( $request, 'sub' );
    }



    /* *
     *
     * Returns a string with the jti
     *
     * */
    public static function getJti( Request $request )
    {
        # Instance the JWT Parser
        //$jwt = new JwtController;

        return $jwt->getParam( $request, 'jti' );
    }



    /* *
     *
     * Returns data object
     *
     * */
    public static function getData( Request $request )
    {
        # Instance the JWT Parser
        //$jwt = new JwtController;

        return $jwt->getParam( $request, 'data' );
    }



    /* *
     *
     * Returns the card data
     *
     * */
    public static function getCard( Request $request )
    {
        # Get the data from JWT
        $data = collect( self::getData( $request ) );

        # Declare returned array if some field is missing
        $missing = [
            'node_id' => null,
            'key'     => null
        ];

        # Check for possible card missing
        if( ! $data->has('card') ){
            return $missing;
        }

        # Check for some field missing
        if( !$data['card']->has('node_id') || ! $data['card']->has('key')){
            return $missing;
        }

        # Success, return the values
        return $data['card'];
    }

}