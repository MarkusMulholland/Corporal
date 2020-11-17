<?php

namespace App\system\security;

// Domain Dependancies
use App\system\models\ConfigModel;
use Firebase\JWT\JWT;
use \UnexpectedValueException;


class JWTWrapper
{
    private const SECRET_KEY = "dv6b546zszd6rf5g4z6r2fg46df2gvszr6gdf";

    public static function encode( int $userId, int $role ) : string
    {
        $payload =
        [
            'iss' => $_SERVER['SERVER_NAME'],
            'iat' => time(),
            'nbf' => time() + 2,
            'exp' => time() + 60,
            'data' => 
            [
                "userId" => $userId,
                "role" => $role
            ]
        ];
            
        return JWT::encode($payload, self::SECRET_KEY, 'HS256');
    }
}
?>