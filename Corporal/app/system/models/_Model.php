<?php
namespace App\system\models;

// Domain Dependancies
use \PDO;
use App\system\DB;
use \ReflectionClass;
use \ReflectionProperty;
use App\system\response\ValidationError;

class _Model
{
    protected PDO $conn;
    protected bool $querySucceeded;

    public function __construct( array $propInitData = [] )
    {
        // Attain a reference to the DB instance
        $db = DB::getInstance();
        $this->conn = $db->conn;

        //TODO Rethink this validation as it is clunky and not encapsulated in mapData

        // Reflect the child class so that we have scope to its members.
        $thisClassReflected = new ReflectionClass( $this );

        // Collect the names of the public properties within the child class
        $propNameArray          = $thisClassReflected->getProperties( ReflectionProperty::IS_PUBLIC );
        $refinedPropNameArray   = [];

        // Strip the name property from the returned Reflection class
        foreach ( $propNameArray as $prop )
            $refinedPropNameArray[] = $prop->name;

        // Check that the number of properties passed to the constructor match the number of public properties in the
        // child class.
        if( !empty( $propInitData ) )
            if( count( $refinedPropNameArray ) == count( $propInitData ) )
                $this->mapData( $propInitData, $this, get_class( $this ) );
            else
                ValidationError::respond( [ "description" => "Data does not match entity requirements" ] );
    }

    private function mapData( array $rawData, &$context, string $dataWrapperType )
    {
        // Arg:     rawData
        // Desc:    This is the raw array of data which we will map to the data structure.
        // Arg:     &context
        // Desc:    This is a reference to the property in the parent data structure which we are mapping.
        // Arg:     dataWrapperType
        // Desc:    This is the type of data structure we are mapping.

        $thisTypeReflected = new ReflectionClass( $dataWrapperType );

        foreach( $rawData as $key => $value )
        {
            $propType = $thisTypeReflected->getProperty( $key )->getType()->getName();

            // If the property is scalar or an array, simply assign the value
            // Else if the property extends the _Model class, recurse and loop over its properties
            // Else if the property is a ModelArray, loop over the elements

            if( is_scalar( $value ) || $propType == 'array' )
                $context->$key = $value;
            else if( is_subclass_of( $propType, "models\_Model" ) )
                $context->$key = new $propType( $value );
            else if( $propType == "system\models\ModelArray" )
                foreach( $value as $index => $nestedModel )
                    $context->$key->elements[ $index ] = new $context->$key->type( $nestedModel );
        }
    }

    // TODO Rethink the way I orientate the child class memebers
    // Declare abstract methods.
    protected function retrieve ( string $key ) : void
    {

    }
}
