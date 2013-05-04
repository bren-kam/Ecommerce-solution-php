<?php
/**
 * MySQLi Class
 *
 * While this class can be used by itself, it is more powerful to
 * use it as an inherited class.
 *
 * @package Studio98 Framework
 * @since 1.0
 */

/**
 * @since 1.0
 */
define('OBJECT', 'OBJECT', true);

/**
 * @since 1.0
 */
define('OBJECT_K', 'OBJECT_K', false);

/**
 * @since 1.0
 */
define('ARRAY_A', 'ARRAY_A', false);

/**
 * @since 1.0
 */
define('ARRAY_N', 'ARRAY_N', false);

/** Database Charset to use in creating database tables. */
if ( !defined('DB_CHARSET') )
	define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
if ( !defined('DB_COLLATE') )
	define( 'DB_COLLATE', '' );

class SQL {
	/**
	 * The holder for the MySQLi object
	 * 
	 * @since 1.0
	 * @access private
	 * @var obj
	 */
	private $m = NULL;
	
	/**
	 * Whether to show SQL/DB errors
	 *
	 * @since 1.0
	 * @access private
	 * @var bool
	 */
	private $show_errors = false;

	/**
	 * Whether to suppress errors during the DB bootstrapping.
	 *
	 * @access private
	 * @since 1.0
	 * @var bool
	 */
	private $suppress_errors = false;

	/**
	 * The last error during query.
	 *
	 * @since 1.0
	 * @var string
	 */
	public $last_error = '';

	/**
	 * Amount of queries made
	 *
	 * @since 1.0
	 * @access private
	 * @var int
	 */
	public $num_queries = 0;

	/**
	 * Saved result of the last query made
	 *
	 * @since 1.0
	 * @access public
	 * @var array
	 */
	public $last_query = '';
	
	/**
	 * Rows affects
	 *
	 * @access public
	 * @var int
	 */
	public $rows_affected = 0;

	/**
	 * Saved info on the table column
	 *
	 * @since 1.0
	 * @access private
	 * @var array
	 */
	private $col_info;

	/**
	 * Saved queries that were executed
	 *
	 * @since 1.0
	 * @access private
	 * @var array
	 */
	public $queries;

	/**
	 * Whether the database queries are ready to start executing.
	 *
	 * @since 1.0
	 * @access private
	 * @var bool
	 */
	private $ready = false;
	
	/**
	 * Database table columns charset
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $charset;

	/**
	 * Database table columns collate
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	public $collate;

	/**
	 * Whether to use mysql_real_escape_string
	 *
	 * @since 1.0
	 * @access public
	 * @var bool
	 */
	public $real_escape = false;
	
	/**
	 * Connects to the database server and selects a database
	 *
	 * @since 1.0
	 *
	 * @param string $db_user MySQL database user
	 * @param string $db_password MySQL database password
	 * @param string $db_name MySQL database name
	 * @param string $db_host MySQL database host
	 */
	function __construct( $db_user, $db_password, $db_name, $db_host ) {
		if ( true == DEBUG )
			$this->show_errors();

		if ( defined('DB_CHARSET') )
			$this->charset = DB_CHARSET;

		if ( defined('DB_COLLATE') )
			$this->collate = DB_COLLATE;

		$this->m = new mysqli($db_host, $db_user, $db_password, $db_name);
		if ( $this->m->connect_error ) {
			$this->bail( sprintf( "
<h1>Error establishing a database connection</h1>
<p>This either means that the username and password information is incorrect or we can't contact the database server at <code>%s</code>. This could mean your host's database server is down. The connection error is:\n%s</p>
<ul>
	<li>Are you sure you have the correct username and password?</li>
	<li>Are you sure that you have typed the correct hostname?</li>
	<li>Are you sure that the database server is running?</li>
</ul>
<p>If you're unsure what these terms mean you should probably contact your host.</p>
", $db_host, mysqli_connect_error() ) );
			return;
		}

		$this->ready = true;
		
		if ( !empty( $this->charset ) )
			$this->m->set_charset( $this->charset );
	}

	/**
	 * PHP5 style destructor and will run when database object is destroyed.
	 *
	 * @since 1.0
	 *
	 * @return bool Always true
	 */
	function __destruct() {
		if ( isset( $this->m->thread_id ) )
			$this->m->kill( $this->m->thread_id );
		
		$this->m->close();
		return true;
	}
	
	private function now() {
		return dt::date( 'datetime' );
	}
	
	public function escape( $data ) {
		if ( is_array( $data ) ) {
			foreach ( (array) $data as $k => $v ) {
				$data[$k] = ( is_array( $v ) ) ? $this->escape( $v ) : $this->m->real_escape_string( $v );
			}
		} else {
			$data = $this->m->real_escape_string( $data );
		}

		return $data;
	}

	/**
	 * Escapes content by reference for insertion into the database, for security
	 *
	 * @since 1.0
	 *
	 * @param string $s
	 */
	private function escape_by_ref( &$string ) {
		$string = $this->m->real_escape_string( $string );
	}
	
	/**
	 * Returns this object for chaining
	 *
	 * @param string $query
	 * @param string $format for what is being requested
	 * @param unknown
	 * @return Mysqli::statement
	 */
	public function prepare( $query, $format = '' ) {
		$this->flush();
		
		$this->statement = $this->m->prepare( $query );
		
		// Log how the function was called
		$this->func_call = "\$db->prepare(\"$query\")";
		
		// Keep track of the last query for debug..
		$this->last_query = $query;
		
		if ( !empty( $format ) ) {
			$values = func_get_args();
			unset( $values[0], $values[1] );
			
			if ( !$this->statement ) {
				throw new ModelException( 'Failed to prepare statement' );
				$this->ready = false;
			} else {
				// Bind the parameters
				call_user_func_array( array( $this->statement, 'bind_param' ), array_merge( array( $format ), ar::references( $values ) ) );
			}
			
			return $this;
		} else {
			if ( !$this->statement ) {
                throw new ModelException( 'Failed to prepare statement' );
				$this->ready = false;
			}
			
			return $this->statement;
		}
	}
		
	/**
	 * Print SQL/DB error.
	 *
	 * @since 1.0
	 * @global array $EZSQL_ERROR Stores error information of query and error string
	 *
	 * @param string $str The error to display
	 * @return bool False if the showing of errors is disabled.
	 */
	public function print_error( $str = '' ) {
		if ( !$str ) 
			$str = $this->m->error();
		
		$this->sql_error[] = array( 'query' => $this->last_query, 'error_str' => $str );

		if ( $this->suppress_errors )
			return false;

		if ( $caller = $this->get_caller() ) {
			$error_str = sprintf('Studio98 Framework database error %1$s for query %2$s made by %3$s', $str, $this->last_query, $caller );
		} else {
			$error_str = sprintf('Studio98 Framework  database error %1$s for query %2$s', $str, $this->last_query );
		}
		
		$log_error = true;
		if ( !function_exists('error_log') )
			$log_error = false;

		$log_file = @ini_get('error_log');
		if ( !empty( $log_file ) && ('syslog' != $log_file) && !@is_writable( $log_file ) )
			$log_error = false;
		
		if ( $log_error )
			@error_log( $error_str, 0 );

		// Is error output turned on or not..
		if ( !$this->show_errors )
			return false;

		$str = htmlspecialchars( $str, ENT_QUOTES );
		$query = htmlspecialchars( $this->last_query, ENT_QUOTES );

		// If there is an error then take note of it
		print "<div id='error'>
		<p class='error'><strong>Studio98 Framework database error:</strong> [$str]<br />
		<code>$query</code></p>
		</div>";
	}

	/**
	 * Enables showing of database errors.
	 *
	 * This function should be used only to enable showing of errors.
	 * sql::hide_errors() should be used instead for hiding of errors. However,
	 * this function can be used to enable and disable showing of database
	 * errors.
	 *
	 * @since 1.0
	 *
	 * @param bool $show Whether to show or hide errors
	 * @return bool Old value for showing errors.
	 */
	public function show_errors( $show = true ) {
		$errors = $this->show_errors;
		$this->show_errors = $show;
		return $errors;
	}

	/**
	 * Disables showing of database errors.
	 *
	 * @since 1.0
	 *
	 * @return bool Whether showing of errors was active or not
	 */
	public function hide_errors() {
		$show = $this->show_errors;
		$this->show_errors = false;
		return $show;
	}

	/**
	 * Whether to suppress database errors.
	 *
	 * @param unknown_type $suppress
	 * @return unknown
	 */
	public function suppress_errors( $suppress = true ) {
		$errors = $this->suppress_errors;
		$this->suppress_errors = $suppress;
		return $errors;
	}

	/**
	 * Kill cached query results.
	 *
	 * @since 1.0
	 */
	public function flush() {
		$this->last_result = array();
		$this->col_info = NULL;
		$this->last_query = '';
		$this->func_call = NULL;
	}

	/**
	 * Perform a MySQLi database query, using current database connection.
	 *
	 * @since 1.0
	 *
	 * @param string $query
	 * @param bool $close_statements (optional|yes) this will close prepared statements
	 * @return int|false Number of rows affected/selected or false on error
	 */
	public function query( $query = '', $close_statements = true ) {
		if ( !$this->ready )
			return false;
		
		$prepared = ( empty( $query ) ) ? true : false;
		
		if ( !$prepared ) {
			// initialise return
			$return_val = 0;
			$this->flush();
			
			// Log how the function was called
			$this->func_call = "\$db->query(\"$query\")";
			
			// Keep track of the last query for debug..
			$this->last_query = $query;
		}
		
		// Perform the query via std mysql_query function..
		if ( defined('SAVE_QUERIES') && SAVE_QUERIES )
			$this->timer_start();
		
		// Gets the result set
		$this->result = ( $prepared ) ? $this->statement->execute() : $this->m->query( $query );
		
		$this->num_queries++;

		if ( defined('SAVE_QUERIES') && SAVE_QUERIES )
			$this->queries[] = array( $this->last_query, $this->timer_stop(), $this->get_caller() );
		
		// If there is an error then take note of it..
		if ( $prepared && $this->last_error = $this->statement->error || !$prepared && $this->last_error = $this->m->error ) {
			$this->print_error( $this->last_error );
			return false;
		}
		
		if ( !$this->result )
			return false;
		
		// Checks if its putting data
		if ( preg_match( "/^\\s*(insert|delete|update|replace|alter) /i", $this->last_query ) ) {
			$this->rows_affected = ( $prepared ) ? $this->statement->affected_rows : $this->m->affected_rows;
			
			// Take note of the insert_id
			if ( preg_match( "/^\\s*(insert|replace) /i", $this->last_query ) )
				$this->insert_id = ( $prepared ) ? (int) $this->statement->insert_id : (int) $this->m->insert_id;
			
			// Return number of rows affected
			$return_val = $this->rows_affected;
			
			// Find out if we're supposed to close the statements
			if ( $prepared && $close_statements )
				$this->statement->close();
		} else { // This means it was grabbing data
			// If it was a prepared statement get the result
			if ( $prepared )
				$this->result = $this->statement->result_metadata();
			
			// Get all the columns
			$i = 0;
			while( $i < ( ( $prepared ) ? $this->statement->field_count : $this->m->field_count ) ) {
				$this->col_info[$i] = $this->result->fetch_field();
				$i++;
			}
			
			// Get all the rows
			$num_rows = 0;
			if ( $prepared ) {
				foreach ( $this->col_info as $field ) {
					$result[$field->name] = '';
					$results[$field->name] = &$result[$field->name];
				}
				
				call_user_func_array( array( $this->statement, 'bind_result' ), $results );
				
				while( $this->statement->fetch() ) {
					$row = new stdClass();
					
					foreach ( $results as $k => $v ) {
						$row->$k = $v;
					}
					
					$this->last_result[$num_rows] = $row;
					$num_rows++;
				}
			} else {
				if ( !is_object( $this->result ) )
					return false;
				
				while( $row = $this->result->fetch_object() ) {
					$this->last_result[$num_rows] = $row;
					$num_rows++;
				}
			}
			
			// Close the statments/free results
			if ( $prepared ) {
				$this->result->close();
				
				// Find out if we're supposed to close the statements
				if ( $close_statements )
					$this->statement->close();
			} else {
				$this->result->free();
			}
			
			// Log number of rows the query returned
			$this->num_rows = $num_rows;

			// Return number of rows selected
			$return_val = $this->num_rows;
		}

		return $return_val;
	}

	/**
	 * Insert a row into a table.
	 *
	 * <code>
	 * sql::insert( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( '%s', '%d' ) )
	 * </code>
	 *
	 * @since 1.0
	 * @see sql::prepare()
	 *
	 * @param string $table table name
	 * @param array $data Data to insert (in column => value pairs).  Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 * @param string $format Format will be used for all of the values in $data. A format is one of 'sidb' (string, integer, double/float, blob).
     * @param bool $override [optional]
	 * @return int|false The number of rows inserted, or false on error.
	 */
	public function insert( $table, $data, $format, $override = false ) {
    	$fields = array_keys( $data );
        $on_duplicate_key = '';

        if ( $override )
            $on_duplicate_key .= ' ON DUPLICATE KEY UPDATE `' . implode( '` = ?, `', $fields ) . '` = ?';

		// Prepare the statement
		$statement = $this->prepare( "INSERT INTO `$table` (`" . implode( '`,`', $fields ) . "`) VALUES (" . str_repeat( '?,', count( $fields ) - 1 ) . '?)' . $on_duplicate_key );

		if ( !$statement )
			return false;

        // References
        $references = array_values( $data );

        if ( $override ) {
            $format .= $format;
            $references = array_merge( $references, array_values( $data ) );
        }

		// Bind the parameters
		call_user_func_array( array( $statement, 'bind_param' ), array_merge( array( $format ), ar::references( $references ) ) );
		
		// Execute it
		return $this->query();
	}


	/**
	 * Update a row in the table
	 *
	 * <code>
	 * sql::update( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( 'ID' => 1 ), array( '%s', '%d' ), array( '%d' ) )
	 * </code>
	 *
	 * @since 1.0
	 * @see sql::prepare()
	 *
	 * @param string $table table name
	 * @param array $data Data to update (in column => value pairs).  Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 * @param array $where A named array of WHERE clauses (in column => value pairs).  Multiple clauses will be joined with ANDs.  Both $where columns and $where values should be "raw".
	 * @param array|string $format Format will be used for all of the values in $data.  A format is one of 'sidb' (string, integer, double/float, blob).
	 * @param array|string $format_where Format will be used for all of  the items in $where. A format is one of 'sidb' (string, integer, double/float, blob).
	 * @return int|false The number of rows updated, or false on error.
	 */
	public function update( $table, $data, $where, $format, $where_format ) {
		if ( !is_array( $where ) )
			return false;
		
		$bits = $wheres = array();
		foreach ( (array) array_keys( $data ) as $field ) {
			$bits[] = "`$field` = ?";
		}

		foreach ( (array) array_keys( $where ) as $field ) {
			$wheres[] = "`$field` = ?";
		}

		$statement = $this->prepare( "UPDATE `$table` SET " . implode( ', ', $bits ) . ' WHERE ' . implode( ' AND ', $wheres ) );
		
		if ( !$statement )
			return false;
		
		// Bind the parameters
		call_user_func_array( array( $statement, 'bind_param' ), array_merge( array( $format . $where_format ), ar::references( array_values( $data ) ), ar::references( array_values( $where ) ) ) );
		
		return $this->query();
	}

	/**
	 * Retrieve one variable from the database.
	 *
	 * Executes a SQL query and returns the value from the SQL result.
	 * If the SQL result contains more than one column and/or more than one row, this function returns the value in the column and row specified.
	 * If $query is null, this function returns the value in the specified column and row from the previous SQL result.
	 *
	 * @since 1.0
	 *
	 * @param string|null $query SQL query.  If null, use the result from the previous query.
	 * @param int $x (optional) Column of value to return.  Indexed from 0.
	 * @param int $y (optional) Row of value to return.  Indexed from 0.
	 * @return string Database query result
	 */
	public function get_var( $query = NULL, $x = 0, $y = 0 ) {
		$this->func_call = "\$db->get_var(\"$query\",$x,$y)";
		
		if ( is_string( $query ) )
			$this->query( $query );
		
		// Extract var out of cached results based x,y vals
		if ( !empty( $this->last_result[$y] ) )
			$values = array_values( get_object_vars( $this->last_result[$y] ) );
		

		// If there is a value return it else return null
		return ( isset( $values[$x] ) && '' !== $values[$x] ) ? $values[$x] : NULL;
	}

	/**
	 * Retrieve one row from the database.
	 *
	 * Executes a SQL query and returns the row from the SQL result.
	 *
	 * @since 1.0
	 *
	 * @param string|null $query SQL query.
	 * @param string $output (optional) one of ARRAY_A | ARRAY_N | OBJECT constants.  Return an associative array (column => value, ...), a numerically indexed array (0 => value, ...) or an object ( ->column = value ), respectively.
	 * @param int $y (optional) Row to return.  Indexed from 0.
	 * @return mixed Database query result in format specifed by $output
	 */
	public function get_row( $query = NULL, $output = ARRAY_A, $y = 0) {
		$this->func_call = "\$db->get_row(\"$query\",$output,$y)";
		
		if ( is_string( $query ) ) {
			$this->query( $query );
		} else {
			return NULL;
		}
		
		if ( !isset( $this->last_result[$y] ) )
			return NULL;

		if ( $output == OBJECT ) {
			return $this->last_result[$y] ? $this->last_result[$y] : null;
		} elseif ( $output == ARRAY_A ) {
			return $this->last_result[$y] ? get_object_vars( $this->last_result[$y] ) : null;
		} elseif ( $output == ARRAY_N ) {
			return $this->last_result[$y] ? array_values( get_object_vars( $this->last_result[$y] ) ) : null;
		} else {
			$this->print_error(" \$db->get_row(string query, output type, int offset) -- Output type must be one of: OBJECT, ARRAY_A, ARRAY_N");
		}
	}

	/**
	 * Retrieve one column from the database.
	 *
	 * Executes a SQL query and returns the column from the SQL result.
	 * If the SQL result contains more than one column, this function returns the column specified.
	 * If $query is null, this function returns the specified column from the previous SQL result.
	 *
	 * @since 1.0
	 *
	 * @param string|null $query SQL query.  If null, use the result from the previous query.
	 * @param int $x Column to return.  Indexed from 0.
	 * @return array Database query result.  Array indexed from 0 by SQL result row number.
	 */
	public function get_col( $query = NULL , $x = 0) {
		if ( NULL != $query )
			$this->query( $query );

		$new_array = array();
		
		// Extract the column values
		for ( $i = 0; $i < count( $this->last_result ); $i++ ) {
			$new_array[$i] = $this->get_var( NULL, $x, $i );
		}
		
		return $new_array;
	}

	/**
	 * Retrieve an entire SQL result set from the database (i.e., many rows)
	 *
	 * Executes a SQL query and returns the entire SQL result.
	 *
	 * @since 1.0
	 *
	 * @param string $query SQL query.
	 * @param string $output (optional) ane of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants.  With one of the first three, return an array of rows indexed from 0 by SQL result row number.  Each row is an associative array (column => value, ...), a numerically indexed array (0 => value, ...), or an object. ( ->column = value ), respectively.  With OBJECT_K, return an associative array of row objects keyed by the value of each row's first column's value.  Duplicate keys are discarded.
	 * @return mixed Database query results
	 */
	public function get_results( $query = NULL, $output = ARRAY_A ) {
		$this->func_call = "\$db->get_results(\"$query\", $output)";

		if ( is_string( $query ) ) {
			$this->query( $query );
		} else {
			return NULL;
		}
		
		if ( $output == OBJECT ) {
			// Return an integer-keyed array of row objects
			return $this->last_result;
		} elseif ( $output == OBJECT_K ) {
			// Return an array of row objects with keys from column 1
			// (Duplicates are discarded)
			foreach ( $this->last_result as $row ) {
				$key = array_shift( get_object_vars( $row ) );
				if ( !isset( $new_array[ $key ] ) )
					$new_array[ $key ] = $row;
			}
			
			return $new_array;
		} elseif ( $output == ARRAY_A || $output == ARRAY_N ) {
			// Return an integer-keyed array of...
			if ( $this->last_result ) {
				$i = 0;
				
				foreach ( (array) $this->last_result as $row ) {
					if ( $output == ARRAY_N ) {
						// ...integer-keyed row arrays
						$new_array[$i] = array_values( get_object_vars( $row ) );
					} else {
						// ...column name-keyed row arrays
						$new_array[$i] = get_object_vars( $row );
					}
					++$i;
				}
				
				return $new_array;
			}
		}
	}

	/**
	 * Retrieve column metadata from the last query.
	 *
	 * @since 1.0
	 *
	 * @param string $info_type one of name, table, def, max_length, not_null, primary_key, multiple_key, unique_key, numeric, blob, type, unsigned, zerofill
	 * @param int $col_offset 0: col name. 1: which table the col's in. 2: col's max length. 3: if the col is numeric. 4: col's type
	 * @return mixed Column Results
	 */
	public function get_col_info( $info_type = 'name', $col_offset = -1 ) {
		if ( $this->col_info ) {
			if ( $col_offset == -1 ) {
				$i = 0;
				foreach ( (array) $this->col_info as $col ) {
					$new_array[$i] = $col->{ $info_type };
					$i++;
				}
				return $new_array;
			} else {
				return $this->col_info[$col_offset]->{$info_type};
			}
		}
	}

    /**
     * Copy a database table
     *
     * @param string $table
     * @param array $fields the Fields to copy
     * @param array $where the fields to base it on
     * @return bool
     */
    public function copy( $table, $fields, $where ) {
        $table = "`$table`";
        $duplicate_keys = array();

        foreach ( $fields as $key => &$field ) {
            $key = "`$key`";

            if ( is_null( $field ) )
                $field = $key;

            $duplicate_keys[] = "$key = VALUES( $key )";
        }

        $field_keys = '`' . implode( '`, `', array_keys( $fields ) ) . '`';
        $field_values = implode( ',', array_values( $fields ) );

        $where_sql = array();

        foreach ( $where as $key => $field ) {
            if ( is_array( $field ) ) {
                // Make sure the array is sql safe
                foreach ( $field as &$i ) {
                    if ( !is_int( $i ) && !is_float( $i ) )
                        $i = "'" . $this->escape( $i ) . "'";
                }

                $where_sql[] = "`$key` IN (" . implode( ', ', $field ) . ')';
            } else {
                $where_sql[] = "`$key` = $field";
            }
		}

        return $this->query( "INSERT INTO $table ( $field_keys ) SELECT $field_values FROM $table WHERE " . IMPLODE ( ' AND ', $where_sql ) . ' ON DUPLICATE KEY UPDATE ' . implode( ', ', $duplicate_keys ) );
    }

	/**
	 * Starts the timer, for debugging purposes.
	 *
	 * @since 1.0.0
	 *
	 * @return true
	 */
	function timer_start() {
		$this->time_start = microtime( true );
		return true;
	}

	/**
	 * Stops the debugging timer.
	 *
	 * @since 1.0.0
	 *
	 * @return int Total time spent on the query, in milliseconds
	 */
	function timer_stop() {
		return microtime( true ) - $this->time_start;
	}

	/**
	 * Wraps errors in a nice header and footer and dies.
	 *
	 * Will not die if sql::$show_errors is true
	 *
	 * @since 1.0
	 *
	 * @param string $message
	 * @return false|void
	 */
	private function bail( $message ) {
		if ( !$this->show_errors ) {
			$this->error = $message;
			return false;
		}
		
		die( $message );
	}

	/**
	 * Retrieve the name of the function that called sql.
	 *
	 * Requires PHP 4.3 and searches up the list of functions until it reaches
	 * the one that would most logically had called this method.
	 *
	 * @since 1.0
	 *
	 * @return string The name of the calling function
	 */
	public function get_caller() {
		// requires PHP 4.3+
		if ( !is_callable('debug_backtrace') )
			return '';

		$bt = debug_backtrace();
		$caller = array();
		
		$bt = array_reverse( $bt );
		foreach ( (array) $bt as $call ) {
			if ( @$call['class'] == __CLASS__ )
				continue;
			
			$function = $call['function'];
			
			if ( isset( $call['class'] ) )
				$function = $call['class'] . "->$function";
			
			$caller[] = $function;
		}
		$caller = join( ', ', $caller );

		return $caller;
	}

	/**
	 * The database version number
	 * @param false|string|resource $dbh_or_table (not implemented) Which database to test.  False = the currently selected database, string = the database containing the specified table, resource = the database corresponding to the specified mysql resource.
	 * @return false|string false on failure, version number on success
	 */
	public function db_version() {
		return preg_replace('/[^0-9.].*/', '', $this->m->server_info );
	}
	
	/**
	 * Returns the error code for the most recent function call
	 *
	 * @return int
	 */
	public function errno() {
		return $this->m->errno;
	}

	/**
	 * Returns the error for the most recent function call
	 *
	 * @return string
	 */
	public function error() {
		return $this->m->error;
	}
}
?>