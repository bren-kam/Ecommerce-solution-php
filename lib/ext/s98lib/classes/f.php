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
	 * @return array
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
		return pathinfo( $path, PATHINFO_BASENAME );
	}

	/**
	 * Returns a file extension
	 *
	 * @param string $path the path to the file that has the extension yo uwant
	 * @return string
	 */
	public static function extension( $path ) {
		return pathinfo( $path, PATHINFO_EXTENSION );
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

    /**
     * Converts human readable file size (e.g. 10 MB, 200.20 GB) into bytes.
     *
     * @param string $str
     * @return int the result is in bytes
     * @author Svetoslav Marinov
     * @author http://slavi.biz
     */
    public static function size2bytes($str) {
        $bytes = 0;

        $bytes_array = array(
            'B' => 1,
            'KB' => 1024,
            'MB' => 1024 * 1024,
            'GB' => 1024 * 1024 * 1024,
            'TB' => 1024 * 1024 * 1024 * 1024,
            'PB' => 1024 * 1024 * 1024 * 1024 * 1024,
        );

        $bytes = floatval($str);

        if ( preg_match('#([KMGTP]?B)$#si', $str, $matches ) && !empty( $bytes_array[$matches[1]] ) ) {
            $bytes *= $bytes_array[$matches[1]];
        }

        $bytes = intval(round($bytes, 2));

        return $bytes;
    }
}