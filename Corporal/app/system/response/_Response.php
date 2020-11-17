<?php
namespace App\system\response;

abstract class _Response
{
    public string $state;
    public string $description;

    public const STATE_SUCCESS  = 'S';
    public const STATE_ERROR    = 'E';

    public const RSP_CODE_SYSTEM_SHUTDOWN   = '00';
    public const RSP_CODE_SYSTEM_ERROR      = '01';
    public const RSP_CODE_SQL_ERROR         = '02';
    public const RSP_CODE_VALIDATION_ERROR  = '03';
    public const RSP_CODE_DATA_NOT_FOUND    = '04';
    public const RSP_CODE_INVALID_REQUEST   = '05';

    public const RSP_CODE_SUCCESS = 'OK';

    abstract protected static function respond( array $data = [] ) : void;

    protected static function finalize( string $state, string $code, string $description, array $data = [] )
    {
        $responseArr =
        [
            "State"         => $state,
            "Code"          => $code,
            "Description"   => $description,
            "Data"          => $data
        ];

        echo json_encode( $responseArr );
        exit();
    }
}
?>