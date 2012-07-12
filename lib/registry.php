<?php
class Registry {

    /**
     * Hold the PDO object
     * @var PDO
     */
    private static $pdo;

    /**
     * Get a preexisting object
     *
     * @static
     * @param string $entry
     * @throws LibraryException
     * @return PDO
     */
    public static function get( $entry ) {
        if ( !isset( self::$$entry ) )
            throw new LibraryException('Registry entry does not exist');

        return self::$$entry;
    }

    /**
     * Sets a registry entry
     *
     * @static
     * @param string $entry
     * @param mixed $value
     * @throws LibraryException
     */
    public static function set( $entry, $value ) {
        if ( !isset( self::$$entry ) )
            throw new LibraryException('Registry entry does not exist');

        self::$$entry = $value;
    }
}
