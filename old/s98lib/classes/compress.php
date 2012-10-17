<?php
/**
 * Compress class - compresses varies things
 *
 * @package Studio98 Framework
 * @since 1.0
 */
class compress extends Base_Class {
	/**
	 * Compresses CSS
	 *
	 * @param string $input the css to compress
	 * @returns string
	 */
	public static function css( $input ) {
		// Remove comments
		$output = preg_replace('#/\*.*?\*/#s', '', $input);
		// Remove whitespace
		$output = preg_replace('/\s*([{}|:;,])\s+/', '$1', $output);
		// Remove trailing whitespace at the start
		$output = preg_replace('/\s\s+(.*)/', '$1', $output);
		// Change 0px to 0
		$output = preg_replace('/([^0-9]0)px/', '$1', $output);
		// Change 6 digit color values to 3 digit (#FFFFFF to #FFF or #FFCC33 to #FC3)
		$output = preg_replace('/#([0-9a-f])\\1([0-9a-f])\\2([0-9a-f])\\3/i', '#$1$2$3', $output);
		// Remove unnecesairy ;'s
		$output = str_replace(';}', '}', $output);
		
		return $output;
	}

	/**
	 * Compresses Javascript
	 *
	 * @param string $input the javascript to compress
	 * @returns string
	 */
	public static function javascript( $input ) {
		return JSMin::minify( $input );
	}
}