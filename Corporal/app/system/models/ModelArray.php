<?php
namespace App\system\models;

// Domain Dependancies
use Exception;

class ModelArray
{
    public string $type;
    public array $elements;

    public function __construct( string $Type )
    {
        if( class_exists( "App\\" . $Type ) )
            $this->type = $Type;
        else
            //TODO Reconsider the way we handle this error. ( Trying to avoid throwing exceptions globally )
            throw new Exception("Invalid class $Type");
    }
}