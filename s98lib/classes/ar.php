<?php
/**
 * Function (ar) class, the array functions go here
 *
 * Functions:
 * info( $object ) - shows the information about an object
 *
 * @package Studio98 Framework
 * @since 1.0
 */

class ar extends Base_Class {
	/**
	 * Static variable to be used with functions
	 * 
	 * @param string $key
	 */
	public static $key;
	
	/**
	 * Assign Key
	 * 
	 * Assign the key of an inner array of a multi-dimensional array to each inner array
	 *
	 * @param array $array
	 * @param string $key the key of the inner array
	 * @param bool $remove (optional) defaults to false -- whether to remove the value as well from the inner array
	 * @return array
	 */
	public static function assign_key( $array, $key, $remove = false ) {
		if ( !is_array( $array ) )
			return $array;
		
		foreach ( $array as $a ) {
			// Get the value
			$value = $a[$key];
			
			// If we need to remove it
			if ( $remove ) {
				unset( $a[$key] );
				
				$new_array[$value] = ( 1 == count( $a ) ) ? array_shift( $a ) : $a;
			} else {
				$new_array[$value] = $a;
			}
		}
		
		return $new_array;
	}
	
	/**
	 * Compares two arrays of n-dimensions
	 *
	 * @link http://us3.php.net/manual/en/function.array-diff-assoc.php#89635
	 *
	 * @param array $array_1
	 * @param array $array_2
	 * @param bool $strict (optional) whether you want to do strict comparison or not
	 * @return array( $before, $after )|bool
	 */
	public static function compare( $array_1, $array_2, $strict = true ) {
		$diff = false;
		
		// Left-to-right
		foreach ( $array_1 as $key => $value ) {
			if ( !array_key_exists( $key, $array_2 ) ) {
				$diff[0][$key] = $value;
			} elseif ( is_array( $value ) ) {
				if ( !is_array( $array_2[$key] ) ) {
					$diff[0][$key] = $value;
					$diff[1][$key] = $array_2[$key];
				} else {
					$new = self::compare( $value, $array_2[$key], $strict );
					if ( $strict && false !== $new || $strict && false != $new ) {
						 if ( isset( $new[0] ) ) $diff[0][$key] = $new[0];
						 if ( isset( $new[1] ) ) $diff[1][$key] = $new[1];
					}
				 }
			} elseif ( $strict && $array_2[$key] !== $value || !$strict && $array_2[$key] != $value ) {
				$diff[0][$key] = $value;
				$diff[1][$key] = $array_2[$key];
			}
	 	}
		
		// Right-to-left
		foreach ( $array_2 as $key => $value ) {
			if ( !array_key_exists( $key, $array_1 ) )
				 $diff[1][$key] = $value;
		}
		
		return $diff;
	}
	
	/**
	 * Turns an array into an array with pointers (useful for MySQLi)
	 *
	 * @link http://us.php.net/manual/en/mysqli-stmt.bind-param.php#96770
	 * @since 1.0.0
	 *
	 * @param array $array
	 * @return array
	 */
	public static function references( $array ) {
        $references = array();
		
        foreach ( $array as $k => $v ) {
            $references[$k] = &$array[$k];
		}
		
    	return $references;
	}
	
	/**
	 * Encodes array values (ints) to Google's encoding (such as sparklines)
	 *
	 * @param array $array
	 * @return string
	 */
	public static function extended_encoding( $array ) {
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-.';
	 	
		$encoding = '';
		foreach ( $array as $value ) {
			$first = floor( $value / 64 );
			$second = $value % 64;
			$encoding .= $characters[$first] . $characters[$second];
		}
	 	
		return $encoding;
	}

	/**
	 * Sort function for sort_by_date
	 *
	 * @param string $a
	 * @param string $b
	 * @return int
	 */
	private static function compare_date( $a, $b ) {
		$a_t = strtotime( $a[self::$key] );
		$b_t = strtotime( $b[self::$key] );

		// If its equal, return 0
		if ( $a_t == $b_t )
			return 0;

		// Return which was is better
		return ( $a_t > $b_t ) ? -1 : 1;
	}

	/**
	 * Sorts an array by the date
	 *
	 * @param array $array
	 * @param string $array_key (which field is the date field)
	 * @return array
	 */
	public static function sort_by_date( $array, $array_key ) {
		// Determine what key of the array has the date
		self::$key = $array_key;
		
		// Sort the array
		usort( $array, array( 'ar', 'compare_date' ) );
		
		return $array;
	}
}