<?php
namespace App\controllers;

// Domain Dependancies
use App\models\UserModel;

use App\system\security\PasswordHasher;
use App\system\security\JWTWrapper;

use App\system\response\ValidationError;
use App\system\response\ProcessSuccessful;
use App\system\response\ReturnResource;
use App\system\response\ValidatedUser;

// TODO Document
class UserController extends _Controller
{
    public function login()
    {
        // Get the user's email address
        $userEmail = $this->request->parsedPayload[ $this->resource ][ "email" ];
        $userPassword = $this->request->parsedPayload[ $this->resource ][ "password" ];

        // Create a new user model and populate it with the user data associated with this email address
        $this->model = new UserModel();
        $this->model->retrieveByEmail( $userEmail );
        $this->model->login( $userPassword );
    }

    public function register()
    {
        $this->model = new UserModel( $this->request->parsedPayload[ $this->resource ] );
        $this->model->create( PasswordHasher::hashPassword( $this->model->password ) );

        ProcessSuccessful::respond();
    }

    public function fetch()
    {
        $this->model = new UserModel();
        $this->model->retrieve( $this->request->id );

        $returnData = 
        [
            "user" => $this->model
        ];

        ReturnResource::respond( $returnData );
    }

    public function save()
    {
        $this->model = new UserModel( $this->request->parsedPayload[ $this->resource ] );
        $this->model->create();

        $returnData = 
        [
            "user" => $this->model
        ];

        ReturnResource::respond( $returnData );
    }
}
?>