<?php
/**
 * Regular Expression class - handles regular expressions
 *
 * Functions:
 * string pattern( string $key ) - returns a regular expression pattern
 * bool match( string $string, string $key ) - tells you whether a string matches a pattern
 *
 * @package Studio98 Framework
 * @since 1.0
 */

class regexp extends Base_Class {
	/**
	 * Returns a regular expression patter
	 *
	 * @since 1.0
	 *
	 * @param string $key
	 * @return string
	 */
	public static function pattern( $key ) {
		$patterns = array(
			'alnum' 		=> '/[^A-Za-z0-9\ ]/',
			'alnumhyphen' 	=> '/[^A-Za-z0-9\-_]/',
			'alpha'			=> '/[^A-Za-z\ ]/',
			'author'		=> '/[^A-Za-z.\ ]/',
			'cc' 			=> '/^(3[47]|4|5[1-5]|6011)/', // Credit card number
			'csv'			=> '/[^-a-zA-Z0-9,\s]/', // Comma separated values
			'date'			=> '/^[\d]{4}-[\d]{2}-[\d]{2}$/',
			'email'			=> "/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)\$/",
			'float'			=> '/[^0-9\.]/',
			'img'			=> '/^[0-9A-Za-z_ \-]+(.[jJ][pP][gG]|.[jJ][pP][eE][gG]|.[gG][iI][fF]|.[pP][nN][gG])\$/',
			'num'			=> '/[^0-9]/',
			'phone'			=> '/[^0-9\- ()]/',
			'url'			=> '/((([\w]+:)?\/\/)?(([\d\w]|%[a-fA-f\d]{2,2})+(:([\d\w]|%[a-fA-f\d]{2,2})+)?@)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)*(\?(&?([-+_~.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~.\d\w]|%[a-fA-f\d]{2,2})*)?)/',
			'zip'			=> '/[^-0-9]/'
		);
		
		return $patterns[$key];
	}
	
	/**
	 * Tells you whether string matches a regular expression pattern
	 *
	 * @param string $string
	 * @param string $key the regular expression key
	 * @return bool
	 */
	public static function match( $string, $key ) {
		return ( 0 == preg_match( self::pattern( $key ), $string ) ) ? false : true;
	}
	
	/**
	 * Replaces a string matching a regular expression pattern with another string
	 *
	 * @param string $string
	 * @param string $key the regular expression key
	 * @param string $replacement what you want to replace it with
	 * @return bool
	 */
	public static function replace( $string, $key, $replacement ) {
		return preg_replace( self::pattern( $key ), $replacement, $string );
	}
}