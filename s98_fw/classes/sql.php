<?php
/**
 * MySQL Class
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
if( !defined('DB_CHARSET') )
	define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
if( !defined('DB_COLLATE') )
	define( 'DB_COLLATE', '' );

class SQL extends Base_Class {
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
	private $num_queries = 0;

	/**
	 * Saved result of the last query made
	 *
	 * @since 1.0
	 * @access public
	 * @var array
	 */
	public $last_query;

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
	private $queries;

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
		$this->db_user = $db_user;
		$this->db_password = $db_password;
		$this->db_name = $db_name;
		$this->db_host = $db_host;
		
		if ( true == DEBUG )
			$this->show_errors();

		if ( defined('DB_CHARSET') )
			$this->charset = DB_CHARSET;

		if ( defined('DB_COLLATE') )
			$this->collate = DB_COLLATE;

		$this->dbh = @mysql_connect($db_host, $db_user, $db_password, true);
		if (!$this->dbh) {
			$this->bail( sprintf( "
<h1>Error establishing a database connection</h1>
<p>This either means that the username and password information is incorrect or we can't contact the database server at <code>%s</code>. This could mean your host's database server is down.</p>
<ul>
	<li>Are you sure you have the correct username and password?</li>
	<li>Are you sure that you have typed the correct hostname?</li>
	<li>Are you sure that the database server is running?</li>
</ul>
<p>If you're unsure what these terms mean you should probably contact your host.</p>
", $db_host) );
			return;
		}

		$this->ready = true;

		if ( !empty( $this->charset ) ) {
			if ( function_exists('mysql_set_charset') ) {
				mysql_set_charset( $this->charset, $this->dbh );
				$this->real_escape = true;
			} else {
				$collation_query = "SET NAMES '{$this->charset}'";
				if ( !empty($this->collate) )
					$collation_query .= " COLLATE '{$this->collate}'";
					
				$this->query( $collation_query );
			}
		}

		$this->select( $db_name );
	}

	/**
	 * PHP5 style destructor and will run when database object is destroyed.
	 *
	 * @since 1.0
	 *
	 * @return bool Always true
	 */
	function __destruct() {
		return true;
	}

	/**
	 * Selects a database using the current database connection.
	 *
	 *
	 * @since 1.0
	 *
	 * @param string $db MySQL database name
	 * @return null Always null.
	 */
	private function select( $db ) {
		if( !@mysql_select_db( $db, $this->dbh ) ) {
			$this->ready = false;
			$this->bail( sprintf('
<h1>Can&#8217;t select database</h1>
<p>We were able to connect to the database server (which means your username and password is okay) but not able to select the <code>%1$s</code> database.</p>
<ul>
<li>Are you sure it exists?</li>
<li>Does the user <code>%2$s</code> have permission to use the <code>%1$s</code> database?</li>
<li>On some systems the name of your database is prefixed with your username, so it would be like <code>username_%1$s</code>. Could that be the problem?</li>
</ul>
<p>If you don\'t know how to setup a database you should <strong>contact your host</strong>.</p>', $db, $this->db_user ) );
			return;
		}
	}

	private function _weak_escape( $string ) {
		return addslashes( $string );
	}
	
	public function now() {
		return date_time::date( 'datetime' );
	}

	private function _real_escape( $string ) {
		return ( $this->dbh && $this->real_escape ) ? mysql_real_escape_string( $string, $this->dbh ) : addslashes( $string );
	}

	private function _escape( $data ) {
		if ( is_array($data) ) {
			foreach( (array) $data as $k => $v ) {
				$data[$k] = ( is_array($v) ) ? $this->_escape( $v ) : $this->_real_escape( $v );
			}
		} else {
			$data = $this->_real_escape( $data );
		}

		return $data;
	}

	/**
	 * Escapes content for insertion into the database using addslashes(), for security
	 *
	 * @since 1.0
	 *
	 * @param string|array $data
	 * @return string query safe string
	 */
	private function escape( $data ) {
		if ( is_array($data) ) {
			foreach ( (array) $data as $k => $v ) {
				$data[$k] = ( is_array($v) ) ? $this->escape( $v ) : $this->_weak_escape( $v );
			}
		} else {
			$data = $this->_weak_escape( $data );
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
		$string = $this->_real_escape( $string );
	}

	/**
	 * Prepares a SQL query for safe execution.  Uses sprintf()-like syntax.
	 *
	 * This function only supports a small subset of the sprintf syntax; it only supports %d (decimal number), %s (string).
	 * Does not support sign, padding, alignment, width or precision specifiers.
	 * Does not support argument numbering/swapping.
	 *
	 * May be called like {@link http://php.net/sprintf sprintf()} or like {@link http://php.net/vsprintf vsprintf()}.
	 *
	 * Both %d and %s should be left unquoted in the query string.
	 *
	 * <code>
	 * sql::prepare( "SELECT * FROM `table` WHERE `column` = %s AND `field` = %d", "foo", 1337 )
	 * </code>
	 *
	 * @link http://php.net/sprintf Description of syntax.
	 * @since 1.0
	 *
	 * @param string $query Query statement with sprintf()-like placeholders
	 * @param array|mixed $args The array of variables to substitute into the query's placeholders if being called like {@link http://php.net/vsprintf vsprintf()}, or the first variable to substitute into the query's placeholders if being called like {@link http://php.net/sprintf sprintf()}.
	 * @param mixed $args,... further variables to substitute into the query's placeholders if being called like {@link http://php.net/sprintf sprintf()}.
	 * @return null|string Sanitized query string
	 */
	public function prepare($query = null) { // ( $query, *$args )
		if ( is_null( $query ) )
			return;
		
		$args = func_get_args();
		array_shift($args);
		
		// If args were passed as an array (as in vsprintf), move them up
		if ( isset( $args[0] ) && is_array( $args[0] ) )
			$args = $args[0];
		
		$query = str_replace("'%s'", '%s', $query); // in case someone mistakenly already singlequoted it
		$query = str_replace('"%s"', '%s', $query); // doublequote unquoting
		$query = str_replace('%s', "'%s'", $query); // quote the strings
		
		array_walk($args, array(&$this, 'escape_by_ref'));
		return @vsprintf( $query, $args );
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
		if( !$str ) 
			$str = mysql_error( $this->dbh );
		
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
		$this->col_info = null;
		$this->last_query = null;
	}

	/**
	 * Perform a MySQL database query, using current database connection.
	 *
	 * @since 1.0
	 *
	 * @param string $query
	 * @return int|false Number of rows affected/selected or false on error
	 */
	public function query($query) {
		if ( ! $this->ready )
			return false;

		// filter the query, if filters are available
		// NOTE: some queries are made before the plugins have been loaded, and thus cannot be filtered with this method
		if ( function_exists('apply_filters') )
			$query = apply_filters('query', $query);

		// initialise return
		$return_val = 0;
		$this->flush();

		// Log how the function was called
		$this->func_call = "\$db->query(\"$query\")";

		// Keep track of the last query for debug..
		$this->last_query = $query;

		// Perform the query via std mysql_query function..
		if ( defined('SAVEQUERIES') && SAVEQUERIES )
			$this->timer_start();

		$this->result = @mysql_query( $query, $this->dbh );
		++$this->num_queries;

		if ( defined('SAVEQUERIES') && SAVEQUERIES )
			$this->queries[] = array( $query, $this->timer_stop(), $this->get_caller() );

		// If there is an error then take note of it..
		if ( $this->last_error = mysql_error( $this->dbh ) ) {
			$this->print_error();
			return false;
		}

		if ( preg_match("/^\\s*(insert|delete|update|replace|alter) /i",$query) ) {
			$this->rows_affected = mysql_affected_rows( $this->dbh );
			// Take note of the insert_id
			if ( preg_match("/^\\s*(insert|replace) /i", $query) ) {
				$this->insert_id = mysql_insert_id($this->dbh);
			}
			// Return number of rows affected
			$return_val = $this->rows_affected;
		} else {
			$i = 0;
			while ($i < @mysql_num_fields( $this->result ) ) {
				$this->col_info[$i] = @mysql_fetch_field( $this->result );
				$i++;
			}
			$num_rows = 0;
			while ( $row = @mysql_fetch_object($this->result) ) {
				$this->last_result[$num_rows] = $row;
				$num_rows++;
			}

			@mysql_free_result($this->result);

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
	 * @param array|string $format (optional) An array of formats to be mapped to each of the value in $data.  If string, that format will be used for all of the values in $data.  A format is one of '%d', '%s' (decimal number, string).  If omitted, all values in $data will be treated as strings.
	 * @return int|false The number of rows inserted, or false on error.
	 */
	public function insert( $table, $data, $format = null ) {
		$formats = $format = (array) $format;
		$fields = array_keys( $data );
		$formatted_fields = array();

		foreach ( $fields as $field ) {
			if ( !empty($format) ) {
				$form = ( $form = array_shift( $formats ) ) ? $form : $format[0];
			} elseif ( isset( $this->field_types[$field] ) ) {
				$form = $this->field_types[$field];
			} else {
				$form = '%s';
			}
			
			$formatted_fields[] = $form;
		}
		
		$sql = "INSERT INTO `$table` (`" . implode( '`,`', $fields ) . "`) VALUES ('" . implode( "','", $formatted_fields ) . "')";
		return $this->query( $this->prepare( $sql, $data ) );
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
	 * @param array|string $format (optional) An array of formats to be mapped to each of the values in $data.  If string, that format will be used for all of the values in $data.  A format is one of '%d', '%s' (decimal number, string).  If omitted, all values in $data will be treated as strings.
	 * @param array|string $format_where (optional) An array of formats to be mapped to each of the values in $where.  If string, that format will be used for all of  the items in $where.  A format is one of '%d', '%s' (decimal number, string).  If omitted, all values in $where will be treated as strings.
	 * @return int|false The number of rows updated, or false on error.
	 */
	public function update( $table, $data, $where, $format = null, $where_format = null ) {
		if ( !is_array( $where ) )
			return false;

		$formats = $format = (array) $format;
		$bits = $wheres = array();
		foreach ( (array) array_keys($data) as $field ) {
			if ( !empty($format) ) {
				$form = ( $form = array_shift($formats) ) ? $form : $format[0];
			} elseif ( isset($this->field_types[$field]) ) {
				$form = $this->field_types[$field];
			} else {
				$form = '%s';
			}
			
			$bits[] = "`$field` = {$form}";
		}

		$where_formats = $where_format = (array) $where_format;
		foreach ( (array) array_keys($where) as $field ) {
			if ( !empty($where_format) )
				$form = ( $form = array_shift($where_formats) ) ? $form : $where_format[0];
			elseif ( isset($this->field_types[$field]) )
				$form = $this->field_types[$field];
			else
				$form = '%s';
			$wheres[] = "`$field` = {$form}";
		}

		$sql = "UPDATE `$table` SET " . implode( ', ', $bits ) . ' WHERE ' . implode( ' AND ', $wheres );
		return $this->query( $this->prepare( $sql, array_merge( array_values($data), array_values($where) ) ) );
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
	public function get_var( $query = null, $x = 0, $y = 0 ) {
		$this->func_call = "\$db->get_var(\"$query\",$x,$y)";
		if ( $query )
			$this->query( $query );

		// Extract var out of cached results based x,y vals
		if ( !empty( $this->last_result[$y] ) ) {
			$values = array_values( get_object_vars( $this->last_result[$y] ) );
		}

		// If there is a value return it else return null
		return ( isset( $values[$x] ) && '' !== $values[$x] ) ? $values[$x] : null;
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
	public function get_row( $query = null, $output = OBJECT, $y = 0) {
		$this->func_call = "\$db->get_row(\"$query\",$output,$y)";
		
		if ( $query ) {
			$this->query($query);
		} else {
			return null;
		}
		
		if ( !isset($this->last_result[$y]) )
			return null;

		if ( $output == OBJECT ) {
			return $this->last_result[$y] ? $this->last_result[$y] : null;
		} elseif ( $output == ARRAY_A ) {
			return $this->last_result[$y] ? get_object_vars($this->last_result[$y]) : null;
		} elseif ( $output == ARRAY_N ) {
			return $this->last_result[$y] ? array_values(get_object_vars($this->last_result[$y])) : null;
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
	public function get_col( $query = null , $x = 0) {
		if ( $query )
			$this->query( $query );

		$new_array = array();
		// Extract the column values
		for ( $i = 0; $i < count( $this->last_result ); $i++ ) {
			$new_array[$i] = $this->get_var(null, $x, $i);
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
	public function get_results( $query = null, $output = OBJECT ) {
		$this->func_call = "\$db->get_results(\"$query\", $output)";

		if ( $query )
			$this->query($query);
		else
			return null;

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
				foreach( (array) $this->last_result as $row ) {
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
				foreach( (array) $this->col_info as $col ) {
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
			if ( class_exists('WP_Error') )
				$this->error = new WP_Error('500', $message);
			else
				$this->error = $message;
			return false;
		}
		
		$this->_die( $message );
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
		return preg_replace('/[^0-9.].*/', '', mysql_get_server_info( $this->dbh ));
	}
}
?>
