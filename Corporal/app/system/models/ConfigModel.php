<?php
namespace App\system\models;

// Domain Dependancies
use App\system\response\SQLError;

class ConfigModel extends _Model
{
    public int      $id;
    public string   $dataKey;
    public string   $dataValue;

    public function retrieve( string $dataKey ) : void
    {
        $stmt = $this->conn->prepare
        (
            "
            SELECT * 
            FROM `config`
            WHERE data_key = :dataKey;
            "
        );

        $this->querySucceeded = $stmt->execute([ ':dataKey' => $dataKey ]);

        if( $this->querySucceeded )
        {
            $result = $stmt->fetch();

            $this->id           = $result[ "id" ];
            $this->dataKey      = $result[ "data_key" ];
            $this->dataValue    = $result[ "data_value" ];
        }
        else
        {
            $errorData = $stmt->errorInfo();
            SQLError::respond( [ "SQLErrorData" => $errorData ] );
        }
    }

    public function create() : void
    {
    }

    public function update( int $id, array $data ) : void
    {
    }

}

?>