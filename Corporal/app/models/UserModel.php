<?php
namespace App\models;

// Domain Dependancies
use App\system\response\SQLError;
use App\system\models\_Model;

use App\system\response\ValidatedUser;
use App\system\response\ValidationError;
use App\system\security\PasswordHasher;
use App\system\security\JWTWrapper;

class UserModel extends _Model
{
    public int      $id;
    public string   $email;
    public string   $mobileNumber;
    public string   $password;
    public int      $role;
    public DeliveryDetailsModel $deliveryDetails;

    public function retrieve( string $id ) : void
    {
        $stmt = $this->conn->prepare
        (
            "
            SELECT *
            FROM `users` u
            INNER JOIN `delivery_details` dd
                ON dd.user_id = u.id
            WHERE id = :id;
            "
        );

        $this->querySucceeded = $stmt->execute([ ':id' => $id ]);

        if( $this->querySucceeded )
        {
            $result = $stmt->fetch();

            $this->id           = $result[ "id" ];
            $this->email        = $result[ "email" ];
            $this->mobileNumber = $result[ "mobile_number" ];
            $this->password     = $result[ "password" ];
            $this->role         = $result[ "role" ];
        }
        else
        {
            $errorData = $stmt->errorInfo();
            SQLError::respond( [ "SQLErrorData" => $errorData ] );
        }
    }

    public function retrieveByEmail( string $email ) : void
    {
        $stmt = $this->conn->prepare
        (
            "
                SELECT * 
                FROM `users`
                WHERE email = :email;
                "
        );

        $this->querySucceeded = $stmt->execute([ ':email' => $email ]);

        if( $this->querySucceeded )
        {
            $result = $stmt->fetch();
            
            if( !$result )
            {
                $this->id = 0;
                return;
            }

            $this->id           = $result[ "id" ];
            $this->email        = $result[ "email" ];
            $this->mobileNumber = $result[ "mobile_number" ];
            $this->password     = $result[ "password" ];
            $this->role         = $result[ "role_id" ];
        }
        else
        {
            $errorData = $stmt->errorInfo();
            SQLError::respond( [ "SQLErrorData" => $errorData ] );
        }
    }

    public function create( array $passwordPackage ) : void
    {
        if( !$this->conn->inTransaction() )
            $this->conn->beginTransaction();
        try
        {
            $stmt = $this->conn->prepare
            (
                "
            INSERT INTO users 
                ( email, password, mobile_number, salt )
            VALUES
                ( :email, :pass, :num, :salt );
            "
            );

            $this->querySucceeded = $stmt->execute
            (
                [
                    ':email'    => $this->email,
                    ':num'      => $this->mobileNumber,
                    ':pass'     => $passwordPackage[ "hashedPassword" ],
                    ':salt'     => $passwordPackage[ "userSalt" ]

                ]
            );

            if( !$this->querySucceeded )
            {
                $errorData = $stmt->errorInfo();
                SQLError::respond( [ "SQLErrorData" => $errorData ] );
            }

            $this->conn->commit();
        }
        catch( \Exception $e )
        {
            $this->conn->rollBack();
            Throw $e;
        }
    }

    public function getSalt() : string
    {
        $stmt = $this->conn->prepare
        (
            "
            SELECT `salt` 
            FROM `users`
            WHERE id = :id;
            "
        );

        $this->querySucceeded = $stmt->execute([ ':id' => $this->id ]);

        if( !$this->querySucceeded )
        {
            $errorData = $stmt->errorInfo();
            SQLError::respond( [ "SQLErrorData" => $errorData ] );
        }

        $result = $stmt->fetch();
        return $result[ "salt" ];
    }
    
    public function login( string $password )
    {
        // If no data is returned, respond with a validation error
        if( $this->id == 0 )
            ValidationError::respond( [ "description" => "Failed to Authenticate" ] );

        // Validate the password
        $passwordIsValid = PasswordHasher::validatePassword( $password, $this->getSalt(), $this->password );

        // Generate a JWT and respond with required data if the password is correct. Otherwise respond with a validation error.
        if( $passwordIsValid )
        {
            // Generate a JWT
            $jwt = JWTWrapper::encode( $this->id, $this->role );

            $userData = get_object_vars( $this );

            ValidatedUser::respond( [ 'user' => $userData, 'jwt' => $jwt ] );
        }
        else
            ValidationError::respond( [ "description" => "Failed to Authenticate" ] );
    }
}

?>