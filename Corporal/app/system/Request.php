<?php
namespace App\system;

class Request
{
    private string $uri;
    private string $method;
    private string $payload;
    private string $auth;

    public int $validationCode;
    public array $parsedPayload = [];
    public string $controller;
    public string $action;
    public string $id;

    public bool $containsValidURI = false;

    private const URI_VALIDATION_REGEX = "((\/{1}[a-z]+){2}(\/{1}\d+)?)";

    private const REQ_ERR_REGEX = 0;
    private const REQ_ERR_INVALID_METHOD = 1;
    private const REQ_ERR_EMPTY_PAYLOAD = 2;
    private const REQ_ERR_INVALID_JSON_IN_PAYLOAD = 3;
    private const REQ_ERR_INVALID_DATA_IN_PAYLOAD = 4;
    private const REQ_ERR_UNAUTHORIZED = 5;

    private const REQ_VALID = 5;

    function __construct()
    {
        $this->uri      = $_SERVER[ 'REQUEST_URI' ];
        $this->method   = $_SERVER[ 'REQUEST_METHOD' ];
        $this->payload  = file_get_contents('php://input');
        $this->auth     = $_SERVER[ 'HTTP_AUTHORIZATION' ] ?? "";

        $this->validationCode = $this->validateRequest();
    }

    private function validateRequest() : int
    {
        // Ensure the URI complies with the CodeNinjas standard.
        if( !preg_match( self::URI_VALIDATION_REGEX, $this->uri ) )
            return self::REQ_ERR_REGEX;
        // Ensure the Request is either POST or GET as these are the only request methods accepted.
        else if( !( $this->method == 'GET' || $this->method == 'POST' ) )
            return self::REQ_ERR_INVALID_METHOD;
        // If the request is a POST, ensure the body of the request has been populated.
        else if( $this->method == 'POST' && empty( $this->payload ) )
            return self::REQ_ERR_EMPTY_PAYLOAD;
        // Ensure a token was passed.
        else if( empty( $this->auth ) )
            return self::REQ_ERR_UNAUTHORIZED;

        $uriComponents = explode( '/', $this->uri );

        $this->controller   = $uriComponents[ 1 ] . 'Controller';
        $this->action       = $uriComponents[ 2 ];

        // If this is a get request, pull out the id
        if( $this->method == 'GET' )
        $this->id = $uriComponents[ 3 ];

        // If this is a post, parse the JSON payload. If this fails, the payload is not valid JSON.
        // Also check that the payload label matches the requested resource name.
        if( $this->method == 'POST' )
        {
            $this->parsedPayload = json_decode( $this->payload, true );

            if( empty( $this->parsedPayload ) )
                return self::REQ_ERR_INVALID_JSON_IN_PAYLOAD;
            
            if( !isset( $this->parsedPayload[ $uriComponents[ 1 ] ] ) )
                return self::REQ_ERR_INVALID_DATA_IN_PAYLOAD;
        }

        // If no validation has caused this method to return, the request is valid
        $this->containsValidURI = true;
        return self::REQ_VALID;
    }

    private function authorizeRequest()
    {
        /*TODO
            Get encrypted packet
            -> Request : Will contain auth type 3, username, token
        */

        $authHeader = json_decode( $this->auth, true );
    }
}
?>