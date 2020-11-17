<?php
namespace App\system;

use App\system\response\InvalidRequest;

class Router
{
    // The entry point of the CodeNinjas Server Side Router
    public static function route()
    {
        $request = new Request();
        if( $request->containsValidURI )
        {
            $controllerName = "App\\controllers\\{$request->controller}";

            new $controllerName( $request );
        }
        else
            InvalidRequest::respond( [ "validationCode" => $request->validationCode ] );
    }
}
?>