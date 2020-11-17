<?php
// Autoload implementation
spl_autoload_register( 
    function( $className ) 
    {
        $file = $_SERVER[ 'DOCUMENT_ROOT' ] . '/app/' . $className . '.php';
        
        if( file_exists( $file ) )
        {
            include_once $file;
        }
        else
        {
            echo "Failed to locate dir";
        }
    }
);
?>