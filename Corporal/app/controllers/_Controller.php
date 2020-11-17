<?php
namespace App\controllers;

// Domain Dependancies
use App\system\Request;
use \ReflectionClass;

class _Controller
{
    protected Request $request;
    protected object $model;

    protected string $resource;

    private $actionList = [];
    private $containsValidAction;

    function __construct( Request $Request )
    {
        $reflectedClass = new ReflectionClass( $this );

        $this->request              = $Request;
        $this->resource             = strtolower( str_replace( "Controller", "", $reflectedClass->getShortName() ) );
        $this->actionList           = get_class_methods( $this );
        $this->containsValidAction  = in_array( $this->request->action, $this->actionList );

        $this->processAction();
    }

    private function processAction()
    {
        if( $this->containsValidAction )
        {
            $action = $this->request->action;
            $this->$action();
        }
        else
        {
            echo "invalid action";
        }
    }
}
?>