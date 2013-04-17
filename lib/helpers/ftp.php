<?php
/**
 * Handles all the FTP data
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Ftp {
	// Connection settings
    public $host, $username, $password, $port, $cwd, $conn_id;
	
	/**
	 * An array of file extensions that should be uploaded in binary
	 * @param array $binary_extensions
	 */
	private $binary_extensions = array( 'jpg', 'jpeg', 'gif', 'bmp', 'png', 'pdf', 'swf', 'flv', 'mp4', 'f4v' );

    // FTP Settings
	public  $timeout = 60;
	public  $passive = true;
	public  $ssl 	 = false;
	public  $system_type = '';

	/**
	 * Creates new FTP instance
	 *
     * @param string $cwd Current Working Directory
	 */
	public function __construct( $cwd = '' ) {
        $this->cwd = $cwd;

        if ( '/' != substr( $this->cwd , strlen( $this->cwd ) - 1 ) )
            $this->cwd .= '/';
	}

	/**
	 * Change the default port 21 run time
	 */
	public function change_port( $port ) {
		$this->port = $port;
		ftp_close( $this->conn_id );
		
		return $this->connect();
	}
	
	/**
	 * Add a file to the server
     *
	 * @param string $file_path local file path
	 * @param string $remote_dir
     * @param string $new_file_name [optional]
     * @return bool
	 */
	public function add( $file_path, $remote_dir, $new_file_name = '' ) {
		if ( !$this->chdir( $this->cwd . $remote_dir ) )
			$this->mkdir( $this->cwd . $remote_dir );		
		
		if ( empty( $new_file_name ) )
			$new_file_name = $this->get_file_name( $file_path );
		
		$pathinfo = pathinfo( $file_path );
		
		$transfer_mode = ( in_array( strtolower( $pathinfo['extension'] ), $this->binary_extensions ) ) ? FTP_BINARY : FTP_ASCII;
		
		return $this->put( $file_path, $this->cwd . $remote_dir . $new_file_name, $transfer_mode );
	}

	
	/**
	 * Delete a file on server
	 *
	 * @param string $file_name remote file name
	 * @param string $remote_dir
     * @return bool
	 */
	public function delete( $file_name, $remote_dir ){
		if ( !$this->chdir( $this->cwd . $remote_dir ) )
			return false;
		
		return $this->_delete( $this->cwd . $remote_dir . $file_name );
	}
	
	/**
	 * Get a remote file
	 *
	 * @param string $file_name remote file name
	 * @param string $remote_dir
	 * @param string $local_dir
	 * @param bool $overwrite (optional) defaults to true
     * @return bool
	 */
	public function get( $file_name, $remote_dir, $local_dir, $overwrite = TRUE ) {
        if ( !$overwrite && file_exists( $local_dir . $file_name ) )
            return false;

		return $this->_get( $local_dir . $file_name , $this->cwd . $remote_dir . $file_name );
	}
	
	/**
	 * Get remote file contents and return it as a string
	 *
	 * @param string $file_name
     * @param int $mode
	 * @return string
	 */
	public function ftp_get_contents( $file_name, $mode = FTP_ASCII ) {
		// Create temp handler:
		$temp_handle = fopen('php://temp', 'r+');
		
		// Try both methods
		if ( !ftp_fget( $this->conn_id, $temp_handle, $this->cwd . $file_name, $mode, 0 ) ) {
			// Get the other mode
			$alternate_mode = ( FTP_ASCII == $mode ) ? FTP_BINARY : FTP_ASCII;

			ftp_fget( $this->conn_id, $temp_handle, $this->cwd . $file_name, $alternate_mode, 0 );
		}
		
		// Start at the beginning
		rewind( $temp_handle );
		
		// Return the data
        return stream_get_contents( $temp_handle );
	}

	/**
	 * Copies a file/directory to new location | DOES NOT WORK
	 *
	 * @param string $remote_path
	 * @param string $remote_new_path
	 * @return bool
	 */
	public function copy( $remote_path, $remote_new_path ) {
		return $this->exec( "cp -R $remote_path $remote_new_path" );
	}

	
	/***** Original Class *****/

	/**
	 * Connects to FTP server
	 *
	 * Connects to the FTP server and sets any errors along the way.
	 * Will return false if any crucial function fails.
	 *
	 * @return bool
	 */
	public function connect() {
		// Find out if we're connecting with SSL
		if ( $this->ssl == false ) {
			if ( !$this->conn_id = ftp_connect( $this->host, $this->port ) ) {
				$this->errors[] = 'Failed to connect';
				return false;
			}
		} else {
			// Test to see if the SSL function exists
			if ( function_exists('ftp_ssl_connect') ) {
				if ( !$this->conn_id = ftp_ssl_connect($this->host, $this->port) ) {
					$this->error = 'Failed to connect via SSL';
					return false;
				}
			} else {
				// The server isn't built with the function
				$this->errors[] = 'ftp_ssl_connection function is not supported on this server';
				return false;
			}
		}

 		// Login to the server
		if ( !$result = @ftp_login( $this->conn_id, $this->username, $this->password ) ) {
			$this->errors[] = 'FTP credentials are invalid';
			return false;
		}
		
 		// Set how many seconds should go by before it times out
		if ( !@ftp_set_option( $this->conn_id, FTP_TIMEOUT_SEC, $this->timeout ) )
			$this->errors[] = 'Could not set option "FTP_TIMEOUT_SEC"';

		// Try set passive mode
		if ( !ftp_pasv( $this->conn_id, $this->passive ) )
			$this->errors[] = 'Could not set passive mode';

		// Get the system type
		if ( !$this->system_type = ftp_systype( $this->conn_id ) )
			$this->errors[] = 'Could not get system type';

		if ( !$this->chdir( $this->cwd ) ) {
			$this->mkdir( $this->cwd );
			$this->chdir( $this->cwd );
		}
		
		return true;
	}

	/**
	 * Maintains a connection to FTP server
	 *
	 * Function should be called before every FTP function.
	 *
	 * Checks to see if the connection is active, if not, attempts to reconnect
	 *
	 * Returns bool true if connected
	 */
	private function maintain_connection() {
		if ( !ftp_systype( $this->conn_id ) )
			return $this->connect();

		return true;
	}

	/**
	 * Puts a file on FTP Server
	 *
	 * @param string $local_file_path a path to the local file to be moved
	 * @param string $remote_file_path a path to the destination on remote server
	 * @param int $mode (optional) FTP_ASCII/FTP_BINARY transfer method
	 * @return bool
	 */
	private function put( $local_file_path, $remote_file_path, $mode = FTP_ASCII ) {
		if ( !$this->maintain_connection() )
			return false;
		
		// Try both methods
		if ( !ftp_put( $this->conn_id, $remote_file_path, $local_file_path, $mode ) ) {
			// Get the other mode
			$alternate_mode = ( FTP_ASCII == $mode ) ? FTP_BINARY : FTP_ASCII;
	
			return ftp_put( $this->conn_id, $remote_file_path, $local_file_path, $alternate_mode );
		}
		
		return true;
	}
	
	/**
	 * Gets a file from FTP Server
	 *
	 * @param string $local_file_path a path to the local file to be saved
	 * @param string $remote_file_path a path to the destination on remote server
	 * @param int $mode (optional) FTP_ASCII/FTP_BINARY transfer method
	 * @return bool
	 */
	private function _get( $local_file_path, $remote_file_path, $mode = FTP_ASCII ) {
		if ( !$this->maintain_connection() )
			return false;

		// Try both methods
		if ( !ftp_get( $this->conn_id, $local_file_path, $remote_file_path, $mode ) ) {
			// Get the other mode
			$alternate_mode = ( FTP_ASCII == $mode ) ? FTP_BINARY : FTP_ASCII;

			return ftp_get( $this->conn_id, $remote_file_path, $local_file_path, $alternate_mode );
		}
		
		return true;
	}

	/**
	 * Change the permission on a file or directory
	 *
	 * @param int $permissions the permissions to set the directory, must be in octal
	 * @param string $remote_file_path the path to the file you want to change
	 * @return bool
	 */
	public function chmod( $permissions, $remote_file_path ) {
		if ( !$this->maintain_connection() )
			return false;

		// If the permissions are not octal, they will be now
		$this->octal( $permissions );

		return ( ftp_chmod( $this->conn_id, $permissions, $remote_file_path ) ) ? true : false;
	}


	/**
	 * Change the directory location
	 *
	 * @param string $remote_directory the remote directory to change to
	 * @return bool
	 */
	private function chdir( $remote_directory ) {
		if ( !$this->maintain_connection() )
			return false;
		
		return @ftp_chdir( $this->conn_id, $remote_directory );
	}

	/**
	 * Deletes a file
	 *
	 * @param string $remote_file_path the remote file to delete
	 * @return bool
	 */
	private function _delete( $remote_file_path ) {
		if ( !$this->maintain_connection() )
			return false;

		return ftp_delete( $this->conn_id, $remote_file_path );
	}

	/**
	 * Make a directory
	 *
	 * @param string $remote_directory the remote directory to create
	 * @return bool
	 */
	public function mkdir( $remote_directory ) {
		if ( !$this->maintain_connection() )
			return false;

		return( ftp_mkdir( $this->conn_id, $remote_directory ) ) ? true : false;
	}

	/**
	 * Remove a directory
	 *
	 * @param string $remote_directory the remote directory to remove
	 * @return bool
	 */
	private function rmdir( $remote_directory ) {
		if ( !$this->maintain_connection() )
			return false;

		return ftp_rmdir( $this->conn_id, $remote_directory );
	}

	/**
	 * Rename a file or directory
	 *
	 * @param string $old_name the name of the file/directory
	 * @param string $new_name the new name of the file/directory
	 * @return bool
	 */
	private function rename( $old_name, $new_name ) {
		if ( !$this->maintain_connection() )
			return false;

		return ftp_rename( $this->conn_id, $old_name, $new_name );
	}

	/**
	 * Retrieve remote directory list
	 *
	 * @param string $remote_directory the directory to list contents of
	 * @return array directory list on success
	 */
	public function dir_list( $remote_directory = '' ) {
		if ( !$this->maintain_connection() )
			return false;
		
		return ftp_nlist( $this->conn_id, $this->cwd . $remote_directory );
	}

	/**
	 * Retrieve remote directory list
	 *
	 * @param string $remote_directory the directory to list contents of
	 * @return array directory list on success
	 */
	public function raw_list( $remote_directory = '' ) {
		if ( !$this->maintain_connection() )
			return false;

		return $this->_parse_raw_list( ftp_rawlist( $this->conn_id, $this->cwd . $remote_directory ) );
	}

	/**
	 * Returns to the parent directory
	 *
	 * @return bool
	 */
	public function cdup() {
		if ( !$this->maintain_connection() )
			return false;

		return ftp_cdup( $this->conn_id );
	}

	/**
	 * Returns the current directory name
	 *
	 * @return string the directories name
	 */
	public function current_dir() {
		if ( !$this->maintain_connection() )
			return false;

		return ftp_pwd( $this->conn_id );
	}

	/**
	 * Ensures a number is octal
	 *
	 * Checks to see if the number is octal. If not,
	 * turns it into octal.
	 *
	 * @param mixed $i the number to check
	 */
	private function octal( &$i ) {
		// If it's not octal, turn it into octal
    	if ( decoct( octdec( $i ) ) != $i )
			octdec( str_pad( $i, 4, '0', STR_PAD_LEFT ) );
	}


	/**
	 * Destroys ftp session
	 *
	 * @return bool
	 */
	public function __destruct() {
		// Check to make sure a connetion exists
		if ( $this->conn_id )
			return ftp_close( $this->conn_id );

		return false;
	}
	
	/**
	 * Get the local filename for any path
	 */
	private function get_file_name( $file_path ) {
		$file_path = explode ( '/' , $file_path );
		return ( $file_path[count( $file_path ) - 1] );
	}

    /**
     * Parse Raw List
     * 
     * @url http://www.php.net/manual/en/function.ftp-rawlist.php
     * 
     * @param $raw_list
     * @return array
     */
    private function _parse_raw_list( $raw_list ) {
        if ( empty( $raw_list ) )
            return array();

        // Declare variables
        $parsed_list = array();
        
        //if you want the dots (. & ..) set this variable to 0
        $start = 0;
        
        //specify the order of the contents here
        //currently set to first directories "d"
        //second links "l"
        //and last the files "-"
        //change it to your convenience but don't touch the value names!
        $order_list = array("d", "l", "-");
        
        //the name and the order of the columns
        //change it to your convenience
        //but don't increase/reduce the number of columns
        $typeCol = "type";
        //$cols = array( "permissions", "number", "owner", "group", "size", "month", "day", "time", "name" );
        $cols = array( "date", "time", "size", "name" );

        foreach ( $raw_list as $key => $value ) {
            $parser = null;
            
            if ( $key < $start )
                continue;

            $parser = explode( " ", preg_replace ('!\s+!', ' ', $value ) );

            if ( isset( $parser ) ) {
                foreach( $parser as $key => $item ) {
                    if ( 'size' == $cols[$key] )
                        $item = $this->_bytesToSize1024( $item );

                    $parser[$cols[$key]] = $item;


                    unset( $parser[$key] );
                }
                
                $parsed_list[] = $parser;
            }
        }
        
        foreach ( $order_list as $order ) {
            foreach ( $parsed_list as $key => $parsed_item ) {
                $type = substr( current( $parsed_item ), 0, 1 );
                
                if ( $type == $order ) {
                    $parsed_item[$typeCol] = $type;
                    unset( $parsed_list[$key] );
                    $parsed_list[] = $parsed_item;
                }
            }
        }
        
        return array_values( $parsed_list );
    }

    /**
     * Make them human redable
     * @param $bytes
     * @param int $precision
     * @return string
     */
    private function _bytesToSize1024( $bytes, $precision = 2 ) {
        // human readable format -- powers of 1024
        //
        $unit = array('B','KB','MB','GB','TB','PB','EB');

        return @round(
            $bytes / pow(1024, ($i = floor(log($bytes, 1024)))), $precision
        ) .' ' . $unit[$i];
    }
}