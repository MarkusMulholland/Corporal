<?php
namespace App\system;

// Domain Dependancies
USE \PDO;

// Data Access Singleton
class DB
{
    public PDO $conn;
    public static DB $instance;

    private const DB            = 'mysql:host=localhost;dbname=webstore;charset=utf8';
    private const DB_PASS       = '';
    private const DB_USER       = 'root';
    private const DB_OPTIONS    = [
                                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                                ];

    function __construct()
    {
        $this->conn = new PDO( self::DB, self::DB_USER, self::DB_PASS, self::DB_OPTIONS );
    }

    public static function getInstance() : DB
    {
        return self::$instance;
    }

    public static function setInstance()
    {
        if ( !isset( self::$instance ) )
        {
            self::$instance = new DB();
        }
    }
}
?>