<?php
use App\system\response\UncaughtException;
use App\system\response\SystemFailure;

ini_set('display_errors', '0');

set_error_handler( 
    function( $lvl, $msg, $file, $line, $cntx )
    {
        $trace = debug_backtrace();

        error_clear_last();

        $returnData =
            [
                "lvl"   => $lvl,
                "msg"   => $msg,
                "file"  => $file,
                "line"  => $line,
                "trace" => $trace
            ];

        UncaughtException::respond( $returnData );
    }
);

register_shutdown_function(
    function( )
    {
        $lastError = error_get_last();
        if( !empty( $lastError ) )
        {
            $message = $lastError[ 'message' ];

            $trace = substr( $message, strpos( $message, "Stack trace" ), strlen( $message ) );

            $message = substr( $message, 0, strpos( $message, "Stack trace" ) );

            $returnData =
            [
                "lvl"   => $lastError[ 'type' ],
                "msg"   => $message,
                "file"  => $lastError[ 'file' ],
                "line"  => $lastError[ 'line' ],
                "trace" => $trace
            ];
            
            SystemFailure::respond( $returnData );
        }
    }
);
?>