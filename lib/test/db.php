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
     * Define connection parameters
     */
    CONST DB_HOST = 'localhost';
    CONST DB_USER = 'gsr';
    CONST DB_PASSWORD = 'asdasd123';
    CONST DB_NAME = 'gsr';

    /**
     * Hold the PDO object
     * @var PDO
     */
    private $_pdo;

    /**
     * Hold the last statement
     * @var PDOStatement
     */
    private $_statement = NULL;

    /**
     * Hold the last query
     * @var string
     */
    private $_last_query = NULL;

    /**
     * Table name, case sensitive
     * @var string
     */
    private $table = NULL;

    private $not_connected_message = "The Connection object was not created. Did you call parent::__construct(\$table) ?";

    public function __construct() {
        // Make sure we're connected
        $this->_connect();
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
     * @return DBStatement
     */
    public function prepare( $sql, $format, $values ) {
        // Get the arguments
        if ( !is_array( $values ) )
            $values = array_slice( func_get_args(), 2 );

        return new DBStatement( $this, $this->_get_statement( $sql, $format, $values ) );
    }

    /**
     * Insert something into the database
     *
     * @param string $table
     * @param array $data
     * @param string $format
     * @param bool $on_duplicate_key [optional]
     * @return int
     */
    public function insert( $table, array $data, $format, $on_duplicate_key = false ) {
        // Separate fields from values
        $fields = array_keys( $data );
        $values = array_values( $data );

        // Create the SQL
        $sql = "INSERT INTO `$table` (`" . implode( '`,`', $fields ) . "`) VALUES (" . str_repeat( '?,', count( $fields ) - 1 ) . '?)';

        // Handle the on duplicate key
        if ( $on_duplicate_key ) {
            // We need to add on duplicate key
            $sql .= ' ON DUPLICATE KEY UPDATE `' . implode( '` = ?, `', $fields ) . '` = ?';

            // Double the format and values
            $format .= $format;
            $values = array_merge( $values, $values );
        }

        // Prepare the statement
        $statement = $this->_get_statement( $sql, $format, $values );

        // Execute query
        $this->query( $statement );

        return $this->get_insert_id();
    }

    /**
     * Update a table
     *
     * @param string $table
     * @param array $data
     * @param array $where
     * @param string $format
     * @param string $where_format
     */
    public function update( $table, array $data, array $where, $format, $where_format ) {
        // Make sure we have base arrays
        $fields = $criteria = array();

        // Setup the fields and criteria
        foreach ( array_keys( $data ) as $field ) {
			$fields[] = "`$field` = ?";
		}

        foreach ( (array) array_keys( $where ) as $field ) {
			$criteria[] = "`$field` = ?";
		}

        // Create the values for a statement
        $sql = "UPDATE `$table` SET " . implode( ', ', $fields ) . ' WHERE ' . implode( ' AND ', $criteria );
        $format .= $where_format;
        $values = array_merge( array_values( $data ), array_values( $where ) );

        // Prepare the statement
        $statement = $this->_get_statement( $sql, $format, $values );

        // Execute query
        $this->query( $statement );
    }

    /**
     * Delete
     *
     * @param string $table
     * @param array $where
     * @param string $where_format
     */
    public function delete( $table, array $where, $where_format ) {
        // Make sure we have base arrays
        $criteria = array();

         // Setup the fields and criteria
        foreach ( (array) array_keys( $where ) as $field ) {
			$criteria[] = "`$field` = ?";
		}

        // Create the values for a statement
        $sql = "DELETE FROM `$table` WHERE " . implode( ' AND ', $criteria );

        // Prepare the statement
        $statement = $this->_get_statement( $sql, $where_format, array_values( $where ) );

        // Execute query
        $this->query( $statement );
    }

    /**
     * Get Results
     *
     * Defaults to fetching as an object
     *
     * @param string|PDOStatement $query [optional]
     * @param int $style [optional] FETCH_OBJ, FETCH_ASSOC,
     * @return mixed
     */
    public function get_results( $query = NULL, $style = PDO::FETCH_OBJ ) {
        // Make sure we have a statement
        if ( !is_null( $query ) )
            $this->query( $query );

        // Make sure we have a statement
        $this->_statement();

        return $this->_statement->fetchAll( $style );
    }

    /**
     * Get a single Row
     *
     * Defaults to fetching as an object
     *
     * @param string|PDOStatement $query [optional]
     * @param int $style [optional] FETCH_OBJ, FETCH_ASSOC,
     * @param mixed $fetch_argument [optional]
     * @return mixed
     */
    public function get_row( $query = NULL, $style = PDO::FETCH_OBJ, $fetch_argument = NULL ) {
        // Make sure we have a statement
        if ( !is_null( $query ) )
            $this->query( $query );

        // Make sure we have a statement
        $this->_statement();

        // Make it possible to do the FETCH_CLASS
        if ( in_array( $style, array( PDO::FETCH_CLASS, PDO::FETCH_INTO ) ) && !is_null( $fetch_argument ) )
            $this->_statement->setFetchMode( $style, $fetch_argument );

        return $this->_statement->fetch( $style );
    }

    /**
     * Get a single column
     *
     * @param string|PDOStatement $query [optional]
     * @return mixed
     */
    public function get_col( $query = NULL ) {
        // Make sure we have a statement
        if ( !is_null( $query ) )
            $this->query( $query );

        // Make sure we have a statement
        $this->_statement();

        return $this->_statement->fetchAll( PDO::FETCH_COLUMN, 0 );
    }

    /**
     * Get a single variable
     *
     * @param string|PDOStatement $query [optional]
     * @return mixed
     */
    public function get_var( $query = NULL ) {
        // Make sure we have a statement
        if ( !is_null( $query ) )
            $this->query( $query );

        // Make sure we have a statement
        $this->_statement();

        return $this->_statement->fetchColumn( 0 );
    }

    /**
     * Copy data from one table to another
     *
     * @param string $table
     * @param array $fields the Fields to copy
     * @param array $where the fields to base it on
     * @return bool
     */
    public function copy( $table, $fields, $where ) {
        // Initialize variables
        $duplicate_keys = array();

        // Determine the fields that need to be copied over
        foreach ( $fields as $key => &$field ) {
            $key = "`$key`";

            if ( is_null( $field ) )
                $field = $key;

            $duplicate_keys[] = "$key = VALUES( $key )";
        }

        // Define field keys and values
        $field_keys = '`' . implode( '`, `', array_keys( $fields ) ) . '`';
        $field_values = implode( ',', array_values( $fields ) );

        // Begin sql and the values
        $where_sql = $where_values = array();

        // Build the where -- if it's an int or a float, we don't need to protect it
        foreach ( $where as $key => $field ) {
            if ( is_array( $field ) ) {
                // Make sure the array is sql safe
                foreach ( $field as &$i ) {
                    if ( !is_int( $i ) && !is_float( $i ) ) {
                        $field_values = $i;
                        $i = '?';
                    }
                }

                $where_sql[] = "`$key` IN (" . implode( ', ', $field ) . ')';
            } else {
                $where_sql[] = "`$key` = $field";
            }
		}

        // Define SQL
        $sql = "INSERT INTO $table ( $field_keys ) SELECT $field_values FROM $table WHERE " . IMPLODE ( ' AND ', $where_sql ) . ' ON DUPLICATE KEY UPDATE ' . implode( ', ', $duplicate_keys );

        // Prepare statement
        $statement = $this->_get_statement( $sql, NULL, $field_values );

        // Query it
        $this->query( $statement );
    }

    /**
     * Performs a query
     *
     * @param string|PDOStatement $query
     * @return bool|PDOStatement
     * @throws InvalidParametersException|ModelException
     */
    public function query( $query ) {
        // We Now have a statement
        $this->_statement = $this->_clean_statement( $query );

        // Do the actual Database call
        $this->_statement->execute();
    }

    /**
     * Begin Transaction
     *
     * @static
     */
    public static function begin_transaction() {
        Registry::get('pdo')->beginTransaction();
    }

    /**
     * Commit
     *
     * @static
     */
    public static function commit() {
        Registry::get('pdo')->commit();
    }

    /**
     * Roll back
     *
     * @static
     */
    public static function roll_back() {
        Registry::get('pdo')->rollBack();
    }

    /**
     * Get insert ID
     *
     * @return int
     */
    public function get_insert_id() {
        return $this->_pdo->lastInsertId();
    }

    /**
     * Get row count
     *
     * @throws ModelException
     * @return int
     */
    public function get_row_count() {
        // Make sure we can do a query
        $this->_statement();

        return $this->_statement->rowCount();
    }

    /**
     * Get Last Query
     *
     * @return string
     */
    public function get_last_query() {
        return $this->_last_query;
    }

    /**
     * Quote a string
     *
     * @param string $string
     * @return string
     */
    public function quote( $string ) {
        return $this->_pdo->quote( $string );
    }

    /**
     * Connect to PDO
     *
     * @throws ModelException
     */
    private function _connect() {
        // Make sure we can do a query
        if ( !$this->_pdo instanceof PDO ) {
            // Try to get it from the registry
            $this->_pdo = Registry::get('pdo');

            // Doesn't exist, then create it
            if ( !$this->_pdo ) {
                try {
                    $this->_pdo = new PDO( 'mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME, self::DB_USER, self::DB_PASSWORD );
                } catch( PDOException $e ) {
                    throw new ModelException( $e->getMessage(), $e->getCode(), $e );
                }

                // Set it in the registry
                Registry::set( 'pdo', $this->_pdo );
            }
        }
    }

    /**
     * Make sure we have a statement
     *
     * @throws ModelException
     */
    private function _statement() {
        // Make sure we can do a query
        if ( !$this->_statement instanceof PDOStatement )
            throw new ModelException( $this->not_connected_message );
    }

    /**
     * Makes sure that we have a statement
     *
     * @param string|PDOStatement
     * @throws InvalidParametersException
     * @return PDOStatement
     */
    private function _clean_statement( $query ) {
        // If it's valid, then return it
        if ( $query instanceof PDOStatement )
            return $query;

        // If it's not a string or PDO Statement, we have a problem
        if ( !is_string( $query ) )
            throw new InvalidParametersException('$query expected to be a string');

        // Return the statement
        return $this->_prepare( $query );
    }

    /**
     * Get Statement
     *
     * @param string $sql
     * @param string $format
     * @param array $values
     * @return PDOStatement
     */
    private function _get_statement( $sql, $format, array $values ) {
        // Reset everything -- last query data is no longer last
        $this->_flush();

        // Create the statement
        $statement = $this->_prepare( $sql );

        // Get the proper format
        $format = $this->_format( $format, count( $values ) );

        // Bind the values
        $this->_bind( $statement, $values, $format );

        return $statement;
    }

    /**
     * Prepare SQL
     *
     * @param string $sql
     * @throws ModelException
     * @return PDOStatement
     */
    private function _prepare( $sql ) {
        try {
            $statement = $this->_pdo->prepare( $sql );
        } catch ( PDOException $e ) {
            throw new ModelException( $e->getMessage(), $e->getCode(), $e );
        }

        // Mark the last query
        $this->_last_query = $sql;

        return $statement;
    }

    /**
     * Create the right format
     *
     * @param string|null $old_format
     * @param int $count
     * @throws InvalidParametersException
     * @return string
     */
    private function _format( $old_format, $count ) {
        if ( is_null( $old_format ) ) {
            // If they didn't put in a format, default to string
            $format = array_fill( 0, $count, PDO::PARAM_STR );
        } else if ( is_string( $old_format ) ) {
            // If they did put in a format, translate it
            $format = array();
            $old_format = str_split( $old_format );

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
        return $format;
    }

    /**
     * Bind the values
     *
     * @param PDOStatement $statement
     * @param array $values
     * @param string $format
     * @throws ModelException
     */
    private function _bind( $statement, array $values, $format ) {
        // To keep track of the format
        $i = 0;

        // Loop through values and bind the value
        foreach ( $values as $key => $value ) {
            // Get the proper format
            $format = $format[$i];

            // If it's a string, then let's hope they did it correctly, if it's not, use the integer version
            $key = ( is_string( $key ) ) ? $key : $key + 1;

            $replacement = ( is_string( $key ) ) ? $key : '?';
            $this->_last_query = preg_replace( '/' . regexp::escape_string( $replacement ) . '/', $this->_pdo->quote( $value ), $this->_last_query, 1 );

            // Bind the value
            try {
                $statement->bindValue( $key, $value, $format );
            } catch ( PDOException $e ) {
                throw new ModelException( $e->getMessage(), $e->getCode(), $e );
            }
        }
    }

    /**
     * Flush all the private information
     */
    private function _flush() {
        $this->_last_query = NULL;
    }
}


/**
 * DBStatement
 */
class DBStatement {
    /**
     * Hold the database object
     * @var ActiveRecordBase
     */
    private $_ar;

    /**
     * Hold the statement
     * @var PDOStatement
     */
    private $_statement;

    /**
     * Hold the ActiveRecord Object
     *
     * @param ActiveRecordBase $ar
     * @param PDOStatement $statement
     */
    public function __construct( ActiveRecordBase $ar, PDOStatement $statement ) {
        $this->_ar = $ar;
        $this->_statement = $statement;
    }

    /**
     * Query the database
     *
     * @return ActiveRecordBase
     */
    public function query() {
        $this->_ar->query( $this->_statement );

        return $this->_ar;
    }

    /**
     * Get Results
     *
     * @return object
     */
    public function get_results() {
        return $this->_ar->get_results( $this->_statement );
    }

    /**
     * Get Row
     *
     * @return object
     */
    public function get_row() {
        return $this->_ar->get_row( $this->_statement );
    }

    /**
     * Get Column
     *
     * @return object
     */
    public function get_col() {
        return $this->_ar->get_col( $this->_statement );
    }

    /**
     * Get Variable
     *
     * @return object
     */
    public function get_var() {
        return $this->_ar->get_var( $this->_statement );
    }
}