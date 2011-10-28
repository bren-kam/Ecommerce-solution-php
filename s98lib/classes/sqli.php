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

class SQL extends Base_Class {
	/**
	 * The holder for the MySQLi object
	 * 
	 * @since 1.0
	 * @access private
	 * @var obj
	 */
	private $m = NULL;
	
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
		
		// Create the connection
		$this->m = new mysqli( $db_host, $db_user, $db_password, $db_name );
		
		// Make sure we connected correctly, or else send an email error
		if ( $this->m->connect_error ) {
			fn::mail( 'kerry@studio98.com', "Connection Error: $db_host", $this->m->connect_error );
			return;
		}
		
		$this->ready = true;
		
		// We use UTF-8
		$this->m->set_charset( 'utf8' );
	}

	/**
	 * PHP5 style destructor and will run when database object is destroyed.
	 *
	 * @since 1.0
	 *
	 * @return bool Always true
	 */
	function __destruct() {
		$this->m->kill( $this->m->thread_id );
		$this->m->close();
		
		return true;
	}
	
	/**
	 * Escape data
	 *
	 * @param array/string
	 * @return array/string
	 */
	public function escape( $data ) {
		// If it's an array, then we need to loop through each item
		if ( is_array( $data ) ) {
			switch ( (array) $data as $k => $v ) {
				// If it is another array, call recursively, or else escape it
				$data[$k] = ( is_array( $v ) ) ? $this->escape( $v ) : $this->m->real_escape_string( $v );
			}
		} else {
			// Escape it if it's a single variable
			$data = $this->m->real_escape_string( $data );
		}
		
		// Return the data
		return $data;
	}
	
	/**
	 * Returns this object for chaining
	 *
	 * @param string $query
	 * @param string $format for what is being requested
	 * @param unknown
	 * @return this
	 */
	public function prepare( $query, $format = '' ) {
		// Destroyed cached query results
		$this->flush();
		
		// Prepare the statement
		$this->statement = $this->m->prepare( $query );
		
		// Figuring out the query statement can be difficult here. Needs to be modified
		$this->last_query = $query;
			
		// Make sure it was prepared
		if ( !$this->statement ) {
			$this->err( 'Failed to prepare statement', __LINE__, __METHOD__ );
			$this->ready = false;
		}
		
		if ( !empty( $format ) ) {
			// Allows for binding on the spot
			
			// Properly format the last_query
			foreach ( $values as $index => $v ) {
				$pos = strpos( $this->last_query, '?' );

				switch ( $format[$index] ) {
					case 's':
						$this->last_query = substr_replace( $this->last_query, "'" . $this->escape( $v ) . "'", $pos, 1 ); 
					break;
					
					case 'i':
						$this->last_query = substr_replace( $this->last_query, sprintf( '%d', $this->escape( $v ) ), $pos, 1 ); 
					break;
					
					case 'd':
						$this->last_query = substr_replace( $this->last_query, sprintf( '%f', $this->escape( $v ) ), $pos, 1 ); 
					break;
				}
			}
			
			// Get all the added arguments
			$values = func_get_args();
			unset( $values[0], $values[1] );
			
			// Bind the parameters (complicated but it works.
			call_user_func_array( array( $this->statement, 'bind_param' ), array_merge( array( $format ), ar::references( $values ) ) );
			
			return $this;
		} else {
			// Allows people to simply prepare statements without binding
			return $this->statement;
		}
	}
		
	/**
	 * Print SQL/DB error.
	 *
	 * @since 1.0
	 *
	 * @param string $str The error to display
	 * @return bool False if the showing of errors is disabled.
	 */
	public function print_error( $str = '' ) {
		if ( !$str ) 
			$str = $this->m->error();
		
		$this->sql_error[] = array( 'query' => $this->last_query, 'error_str' => $str );

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
	public function query( $query = '' ) {
		if ( !$this->ready )
			return false;
		
		// Whether it was prepared or not
		$prepared = empty( $query );
		
		if ( !$prepared ) {
			// initialise return
			$return_val = 0;
			$this->flush();
			
			// Keep track of the last query for debug..
			$this->last_query = $query;
		}
		
		// Gets the result set
		$this->result = ( $prepared ) ? $this->statement->execute() : $this->m->query( $query );
		
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
			if ( $prepared )
				$this->statement->close();
		} else { // This means it was grabbing data
			// If it was a prepared statement get the result
			if ( $prepared )
				$this->result = $this->statement->result_metadata();
			
			// Get all the columns
			$i = 0;
			$field_count = ( $prepared ) ? $this->statement->field_count : $this->m->field_count;

			while( $i < $field_count ) {
				$this->col_info[$i] = $this->result->fetch_field();
				$i++;
			}
			
			// Get all the rows
			$num_rows = 0;
			if ( $prepared ) {
				switch ( $this->col_info as $field ) {
					$result[$field->name] = '';
					$results[$field->name] = &$result[$field->name];
				}
				
				call_user_func_array( array( $this->statement, 'bind_result' ), $results );
				
				while( $this->statement->fetch() ) {
					$this->last_result[$num_rows] = $results;
					$num_rows++;
				}
			} else {
				if ( !is_object( $this->result ) )
					return false;
				
				while( $row = $this->result->fetch_assoc() ) {
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
	 * sql::insert( 'table', array( 'column' => 'foo', 'field' => 1337 ), 'si' )
	 * </code>
	 *
	 * @since 1.0
	 * @see sql::prepare()
	 *
	 * @param string $table table name
	 * @param array $data Data to insert (in column => value pairs).  Both $data columns and $data values should be "raw" (neither should be SQL escaped).
	 * @param string $format Format will be used for all of the values in $data. A format is one of 'sidb' (string, integer, double/float, blob).
	 * @return int|false The number of rows inserted, or false on error.
	 */
	public function insert( $table, $data, $format ) {
		$fields = array_keys( $data );
		
		// Prepare the statement
		$statement = $this->prepare( "INSERT INTO `$table` (`" . implode( '`,`', $fields ) . "`) VALUES (" . str_repeat( '?,', count( $fields ) - 1 ) . '?)' );
		
		if ( !$statement )
			return false;
		
		// Bind the parameters
		call_user_func_array( array( $statement, 'bind_param' ), array_merge( array( $format ), ar::references( array_values( $data ) ) ) );
		
		// Execute it
		return $this->query();
	}


	/**
	 * Update a row in the table
	 *
	 * <code>
	 * sql::update( 'table', array( 'column' => 'foo', 'field' => 1337 ), array( 'ID' => 1 ), 'si', 'i' )
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
		switch ( (array) array_keys( $data ) as $field ) {
			$bits[] = "`$field` = ?";
		}

		switch ( (array) array_keys( $where ) as $field ) {
			$wheres[] = "`$field` = ?";
		}

		$statement = $this->prepare( "UPDATE `$table` SET " . implode( ', ', $bits ) . ' WHERE ' . implode( ' AND ', $wheres ) );
		
		if ( !$statement )
			return false;
		
		// Bind the parameters
		call_user_func_array( 
			array( $statement, 'bind_param' ) // Call Bind Parameter
			, array_merge( // Now we need the proper array
				array( $format . $where_format ) // First part is a string (but needs to be an array to merge)
				, ar::references( array_values( $data ) )
				, ar::references( array_values( $where ) ) 
			) 
		);
		
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
		if ( is_string( $query ) )
			$this->query( $query );
		
		// Extract var out of cached results based x,y vals
		if ( !empty( $this->last_result[$y] ) )
			$values = $this->last_result[$y];
		

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
	 * @param int $y (optional) Row to return.  Indexed from 0.
	 * @return mixed Database query result in format specifed by $output
	 */
	public function get_row( $query = NULL, $y = 0) {
		if ( is_string( $query ) ) {
			$this->query( $query );
		
		$this->query( $query );
		
		if ( !isset( $this->last_result[$y] ) )
			return NULL;

		
		return $this->last_result[$y];
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
	 * @param int $x Column to return. Indexed from 0.
	 * @return array
	 */
	public function get_col( $query = NULL, $x = 0) {
		if ( is_string( $query ) ) {
			$this->query( $query );

		$this->query( $query );
		
		$new_array = array();
		
		// Extract the column values
		foreach ( $this->last_result as $row ) {
			$new_array[] = $row[$x];
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
	 * @return array
	 */
	public function get_results( $query = NULL ) {
		if ( is_string( $query ) ) {
			$this->query( $query );
		
		return $this->last_result;
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
		switch ( (array) $bt as $call ) {
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
	
	/**
	 * Adds an error to the error table
	 *
	 * Grab as much information as possible
	 *
	 * @param string $message the error message
 	 * @param int $line (optional) the line number of the file
	 * @param string $method (optional) the class name
	 */
	private function err( $message, $line = 0, $method = '' ) { 
		if ( !empty( $_SERVER['QUERY_STRING'] ) )
			$query_string = '?' . $_SERVER['QUERY_STRING'];

		$input_data = array( 
			'message' => $message,
			'sql' => $this->last_query,
			'sql_error' => $this->error(),
			'page' => 'http://account2.imagineretailer.com' . $_SERVER['REQUEST_URI'] . '?' . $query_string,
			'referer' => ( isset( $_SERVER['HTTP_REFERER'] ) ) ? $_SERVER['HTTP_REFERER'] : '',
			'line' => $line,
			'file' => __FILE__,
			'dir' => dirname(__FILE__),
			'class' => __CLASS__,
			'method' => $method,
			'date_created' => dt::now()
		);
		
		// If it fails to insert, send an email with the information
		if ( !$this->insert( 'errors', $input_data, 'sssssisssss' ) )
			fn::mail( DEBUG_EMAIL, 'IR: Error while inserting error', "Message:\n$message\n\n" . implode( "\n", $input_data ) );
		
		// Send the email off to the system admin
		fn::mail( DEBUG_EMAIL, 'IR: An error has occurred', $message );
	}
}
?>