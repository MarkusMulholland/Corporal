<?php
// Bootstrap 
header("Access-Control-Allow-Origin: *");

// Include the autoloader
require $_SERVER[ 'DOCUMENT_ROOT' ] . '/vendor/autoload.php';

// Custom Error Handler
require $_SERVER[ 'DOCUMENT_ROOT' ] . '/config/errorHandler.php';

// Establish a connection to the Database
App\system\DB::setInstance();

?>