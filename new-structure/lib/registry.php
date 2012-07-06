<?php
class Registry {

    /**
     * Hold the PDO object
     * @var PDO
     */
    private static $connection;

    public static function getConnection() {
        if ( self::$connection )
            return self::$connection;

        $host = 'localhost';
        $user = 'imaginer_admin';
        $password = 'rbDxn6kkj2e4';
        $name = 'imaginer_system';
        try {
            self::$connection = new PDO( "mysql:host=$host;dbname=$name", $user, $password );
        } catch(PDOException $exception ) {
            throw new LibraryException( $exception->getMessage() );
        }
        return self::$connection;
    }

}
