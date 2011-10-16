<?php
/**
 * Filter class - filters variables
 *
 * Functions:
 * string|array stripslashes_deep( string|array $value ) - strips slashes
 *
 * @package Studio98 Framework
 * @since 1.0
 */

class filter extends Base_Class {
	/**
	 * Navigates through an array and removes slashes from the values.
	 *
	 * If an array is passed, the array_map() function causes a callback to pass the
	 * value back to the function. The slashes from this value will removed.
	 *
	 * @since 1.0
	 *
	 * @param array|string $value The array or string to be stripped
	 * @return array|string Stripped array (or string in the callback).
	 */
	function stripslashes_deep( $value ) {
		return is_array( $value ) ? array_map( array('self', 'stripslashes_deep'), $value ) : stripslashes( $value );
	}
}