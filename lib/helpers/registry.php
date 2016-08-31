<?php
class Registry {

    /**
     * Hold the PDO object
     * @var PDO
     */
    private static $pdo_master;

    /**
     * Hold the PDO object
     * @var PDO
     */
    private static $pdo_slave;

    /**
     * Hold the PDO object
     * @var PDO
     */
    private static $pdo_imr;

    /**
     * Get a preexisting object
     *
     * @static
     * @param string $entry
     * @throws LibraryException
     * @return PDO
     */
    public static function get( $entry ) {
        if ( !property_exists( 'Registry', $entry ) )
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
        if ( !property_exists( 'Registry', $entry ) )
            throw new LibraryException('Registry entry does not exist');

        self::$$entry = $value;
    }
}
