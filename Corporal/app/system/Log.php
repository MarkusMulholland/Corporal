<?php
namespace App\system;

class Log
{
    public static function log( response\_Response $response )
    {
        try
        {
            $file = "/app/logs/{$response->logContext}.log";

            if( file_exists( $file ) )
            {
                if( filesize( $file  ) < 5000000 )
                {
                    file_put_contents( self::formLog( $response ), $file, FILE_APPEND );
                }
                else
                {
                    $handle     = fopen( $file, 'r+' );
                    $contents   = file_get_contents( $file );

                    file_put_contents( self::formLog( $response ), str_replcae( ".log", "_ARCHIVE.log", $file ), FILE_APPEND );
                    ftruncate( $handle );
                    fclose( $handle );
                }
            }
            else
            {
                file_put_contents( self::formLog( $response ), $file, FILE_APPEND );
            }
        }
        catch( \Exception $e )
        {
            throw new \Exception('Featue Not Implemented');
        }
    }

    private static function formLog( response\_Response $response ) : String
    {
        $now = date('Y.m.d H:i:s');
        $logString;

        if( $response->state == response\_Response::STATE_SUCCESS )
            $logString =
            "
                CodeNinjas - {$now}\r\n
                Success         : {$response->description}\r\n
                Additional Data : " . json_encode( $response->data ) . "\r\n
                \r\n\r\n
            ";
        else
            $logString =
            "
                CodeNinjas - {$now}\r\n
                Error           : {$response->description}\r\n
                Additional Data : " . json_encode( $response->data ) . "\r\n
                \r\n\r\n
            ";

        return $logString;
    }
}
?>