<?php

    namespace App\system\security;

    // Domain Dependancies
    use App\system\models\ConfigModel;

    class PasswordHasher
    {
        private const PEPPER = "Capitao";

        public static function hashPassword( string $password ) : array
        {
            $userSalt = rand( 10000, 99999 );
            $salt = self::getSalt();

            $passwordSliceOne = substr( $password, 0, -3 );
            $passwordSliceTwo = substr( $password, -3 );

            $hashedPassword = password_hash(self::PEPPER . $salt . $passwordSliceOne . $userSalt . $passwordSliceTwo,PASSWORD_DEFAULT );

            return [ "hashedPassword" => $hashedPassword, "userSalt" => $userSalt ];
        }

        private static function getSalt() : string
        {
            $config = new ConfigModel();
            $config->retrieve( "passwordSalt" );

            return $config->dataValue;
        }

        public static function validatePassword( string $receivedUnhashedPassword, $userSalt, $storedHashedPassword) : bool
        {
            $salt = self::getSalt();

            $passwordSliceOne = substr( $receivedUnhashedPassword, 0, -3 );
            $passwordSliceTwo = substr( $receivedUnhashedPassword, -3 );

            return password_verify( self::PEPPER . $salt . $passwordSliceOne . $userSalt . $passwordSliceTwo, $storedHashedPassword );
        }
    }