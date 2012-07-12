<?php
/**
 * File class (f)
 *
 * Functions:
 * read( $path ) - reads the file path
 *
 * @package Studio98 Framework
 * @since 1.0
 */
class f extends Base_Class {
	/**
	 * Reads a files content
	 * 
	 * @since 1.0
	 *
	 * @param string $path
	 * @return |array
	 */
	public static function read( $path ) {
		$handle = fopen( $path, 'r' );
		$content = fread( $handle, filesize( $path ) );
		fclose( $handle );		
		
		return $content;
	}
	
	/**
	 * Reads a directory and returns the files
	 *
	 * @since 1.0
	 * 
	 * @param string $path
	 * @return array
	 */
	public static function read_dir( $path ) {
		$files = array();
		
		// Open the directory
		if ( $handle = opendir( $path ) ) {
			while( false !== ( $file = readdir( $handle ) ) ) {
				if ( '.' != $file && '..' != $file )
					$files[] = $file;
			}
			
			closedir($handle);
		}
		
		return $files;
	}

    /**
	 * Returns a file name
	 *
	 * @param string $path
	 * @return string
	 */
	public static function name( $path ) {
		$path_info = pathinfo( $path );
		return $path_info['basename'];
	}

	/**
	 * Returns a file extension
	 *
	 * @param string $path the path to the file that has the extension yo uwant
	 * @return string
	 */
	public static function extension( $path ) {
		$path_info = pathinfo( $path );
		return ( isset( $path_info['extension'] ) ) ? $path_info['extension'] : '';
	}

	/**
	 * Removes a file extension
	 *
	 * @param string $file_name the file name you want to strip of an extension
	 * @return string
	 */
	public static function strip_extension( $file_name ) {
		return str_replace( '.' . self::extension( $file_name ), '', $file_name );
	}
}