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
    private $_pdo;

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
     * Hold the last query
     * @var string
     */
    private $_last_query = NULL;

	/**
	 * Connects to the database server and selects a database
	 *
	 * @param string $user MySQL database user
	 * @param string $password MySQL database password
	 * @param string $name MySQL database name
	 * @param string $host MySQL database host
     * @throws ModelException
	 */
	public function connect( $user, $password, $name, $host ) {
        // Connect
        try {
    		$this->_pdo = new PDO( "mysql:host=$host;dbname=$name", $user, $password );
        } catch ( PDOException $e ) {
            throw new ModelException( $e->getMessage(), $e->getCode(), $e );
        }
   	}

    /**
     * Prepares a statement
     *
     * Example:
     * ...->prepare('SELECT `id` FROM `users` WHERE `email` LIKE :email', 's', array( ':email' => $email ) )->query();
     *
     * @param string $sql
     * @param string $format (i for integer, s for string )
     * @param mixed $values Used just to ensure they included this parameter
     * @throws ModelException
     * @return DB_Statement
     */
    public function prepare( $sql, $format, $values ) {
        // Reset everything -- last query data is no longer last
        $this->_flush();

        // Get the arguments
        $values = array_slice( func_get_args(), 2 );

        // Create the statement
        $db_statement = new DB_Statement( $this, $this->_pdo );
        $db_statement->prepare( $sql, $format, $values );
        $this->_last_query = $db_statement->getQuery();

        return $db_statement;
    }

    /**
     * Performs a query
     *
     * @param string|PDOStatement $query
     * @return bool|PDOStatement
     */
    public function query( $query ) {
        if ( is_string( $query ) ) {
            // Query it as a string
            return $this->_db->query( $query );
        } else if ( $query instanceof PDOStatement ) {
            // Execute PDO Statement
            return $query->execute();
        } else {
            return false;
        }
    }

    /**
     * Flush all the private information
     */
    private function _flush() {
        $this->_error_message = $this->_error_code = $this->_last_query = NULL;
    }
}


/**
 * DB Statement
 */
class DB_Statement {
    /**
     * Hold the db class
     * @var DB
     */
    private $_db;

    /**
     * Hold the PDO class
     * @var PDO
     */
    private $_pdo;

    /**
     * Hold the statement
     * @var PDOStatement
     */
    private $_statement = NULL;

    /**
     * Hold the format
     * @var array
     */
    private $_format = array();

    /**
     * Hold the SQL
     * @var string
     */
    private $_sql = '';

    /**
     * Hold the DB Object
     * @param DB $db
     * @param PDO $pdo
     */
    public function __construct( DB $db, PDO $pdo ) {
        $this->_db = $db;
        $this->_pdo = $pdo;
    }

    /**
     * Bind a Value
     *
     * @param string $sql
     * @param string $format
     * @param array $values
     */
    public function prepare( $sql, $format, array $values ) {
        $this->_prepare( $sql );
        $this->_format( $format, count( $values ) );
        $this->_bind( $values );
    }

    /**
     * Query the database
     */
    public function query() {
        $this->_db->query( $this );
    }

    /**
     * Prepare SQL
     *
     * @param string $sql
     * @throws ModelException
     */
    private function _prepare( $sql ) {
        try {
            $this->_statement = $this->_pdo->prepare( $sql );
        } catch ( PDOException $e ) {
            throw new ModelException( $e->getMessage(), $e );
        }
    }

    /**
     * Create the right format
     *
     * @param string|null $old_format
     * @param int $count
     * @throws InvalidParametersException
     */
    private function _format( $old_format, $count ) {
        if ( is_null( $old_format ) ) {
            // If they didn't put in a format, default to string
            $format = array_fill( 0, $count, PDO::PARAM_STR );
        } else if ( is_string( $old_format ) ) {
            // If they did put in a format, translate it
            $format = array();

            // Assign every one
            foreach ( $old_format as $letter ) {
                $format[] = ( 'i' == $letter ) ? PDO::PARAM_INT : PDO::PARAM_STR;
            }

            // Make sure that they have reached their limit
            while ( count( $format ) < $count ) {
                $format[] = PDO::PARAM_STR;
            }
        } else {
            throw new InvalidParametersException( '$old_format must be a string or null' );
        }

        // Assign the format
        $this->_format = $format;
    }

    /**
     * Bind the values
     *
     * @param array $values
     * @throws ModelException
     */
    private function _bind( array $values ) {
        // To keep track of the format
        $i = 0;

        // Loop through values and bind the value
        foreach ( $values as $key => $value ) {
            // Get the proper format
            $format = $this->_format[$i];

            // If it's a string, then let's hope they did it correctly, if it's not, use the integer version
            $key = ( is_string( $key ) ) ? $key : $key + 1;

            $replacement = ( is_int( $key ) ) ? $key : '?';
            $this->_sql = str_replace( $replacement, $this->_pdo->quote( $value ), $this->_sql );

            // Bind the value
            try {
                $this->_statement->bindValue( $key, $value, $format );
            } catch ( PDOException $e ) {
                throw new ModelException( $e->getMessage(), $e );
            }
        }
    }

}
?>