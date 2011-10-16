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
	public function read( $path ) {
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
	public function read_dir( $path ) {
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
}