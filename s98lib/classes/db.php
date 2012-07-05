<?php
/**
 * Database Class
 *
 * Uses PDO for a standard DB Abstraction Layer
 *
 * @package Studio98 Library
 */

class DB {
    /**
     * Hold the PDO object
     * @var PDO
     */
    private $_db;

    /**
     * Hold the last error messages
     * @var string
     */
    private $_error_message = NULL;

    /**
     * Hold the last error code
     * @var int
     */
    private $_error_code = NULL;

	/**
	 * Connects to the database server and selects a database
	 *
	 * @param string $user MySQL database user
	 * @param string $password MySQL database password
	 * @param string $name MySQL database name
	 * @param string $host MySQL database host
     * @return bool
	 */
	public function connect( $user, $password, $name, $host ) {
        // Connect
        try {
    		$this->_db = new PDO( "mysql:host=$host;dbname=$name", $user, $password );
        } catch ( PDOException $e ) {
            throw new DatabaseException( $e->getMessage(), $e->getCode(), $e );
        }

        return true;
   	}

    /**
     * Prepares a statement
     *
     * @param string $sql You can either use ":variable" or "?"
     * @return PDOStatement
     */
    public function prepare( $sql ) {
        return $this->_db->prepare( $sql );
    }

    /**
     * Performs a query
     *
     * @param string|PDOStatement $query
     * @return bool|PDOStatement
     */
    public function query( $query ) {
        if ( is_string( $query ) ) {
            return $this->_db->query( $query );
        } else if ( $query instanceof PDOStatement ) {
            return $query->execute();
        } else {
            return false;
        }
    }
}
?>