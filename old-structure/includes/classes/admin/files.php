<?php
/**
 * Handles all the file manipulation
 *
 * Amazon S3 Manager: http://www.s3fm.com/
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Files extends Base_Class {
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if ( !parent::__construct() )
			return false;
		
		// Load Amazon S3
		library( 'S3' );
		$this->s3 = new S3( config::key('aws-access-key'), config::key('aws-secret-key') );
		$this->bucket = config::key('aws-bucket-domain');
	}
	
	/**
	 * Uploads an Image to Amazon
	 *
	 * @param file $image the product image file
	 * @param string $new_image_name the new image name
	 * @param int $width the width you want the image to be
	 * @param int $height the height you want the image to be
	 * @param string $industry the industry to upload it under
	 * @param string $directory (Optional) any path to the directory you want the file to be in
	 * @param bool $keep_proportions (Optional|true) keep image proportions
	 * @param bool fill_constraints (Optional|true) fill the constraints given
	 * @returns bool/int
	 */
	public function upload_image( $image, $new_image_name, $width, $height, $industry, $directory = '', $keep_proportions = true, $fill_constraints = true ) {
		// If there was an image, upload it
		if ( empty( $image['name'] ) )
			return false;
		
		list( $result, $image_file ) = image::resize( $image['tmp_name'], OPERATING_PATH . 'media/uploads/images/', $new_image_name, $width, $height, 90, $keep_proportions, $fill_constraints );
		
		if ( !$result || !$image_file )
			return false;
		
		// Make sure it exists
		if ( !is_file( $image_file ) )
			return false;

		// Upload the image
		if ( !empty( $industry ) && $this->s3->putObjectFile( $image_file, $industry . $this->bucket, $directory . basename( $image_file ), S3::ACL_PUBLIC_READ ) ) {
			// Delete the local image
			unlink( $image_file );
			return true;
		}
		
		return false;
	}

    /**
	 * Copy File
     *
     * Uploads a file to Amazon S3
	 *
	 * @param int $website_id
	 * @param string $url
     * @param string $bucket
	 * @return mixed
	 */
	public function copy_file( $website_id, $url, $bucket ) {
        $bucket = $bucket . $this->bucket;

        $uri = str_replace( 'http://' . $bucket . '/', '', $url );
        $new_uri = preg_replace( '/^([0-9]+)/', $website_id, $uri );

		if ( !$this->s3->copyObject( $bucket, $uri, $bucket, $new_uri, S3::ACL_PUBLIC_READ ) )
            return false;

        return 'http://' . $bucket . '/' . $new_uri;
	}
	
	/**
	 * Upload an attachment to Amazon S3
	 *
	 * @param string $attachment_name
	 * @param string $attachment_path
	 * @param int $website_id
	 * @return string
	 */
	public function upload_attachment( $attachment_name, $attachment_path, $website_id ) {
		global $user;
		
		// Using a different bucket
		$this->bucket = 'retailcatalog.us';
		
		// Put it in a directory for the organization
		$directory = $user['user_id'] . '/' . $website_id . '/';
		
		// Get the file extension
		$file_extension = f::extension( $attachment_name );
		
		// Create the image name
		$attachment_name = format::slug( str_replace( $file_extension, '', $attachment_name ) ) . '.' . $file_extension;
		
		if ( ( $ticket_upload_id = $this->add_upload( $directory . $attachment_name ) ) && $this->s3->putObjectFile( $attachment_path, $this->bucket, 'attachments/' . $directory . $attachment_name, S3::ACL_PUBLIC_READ ) ) {
			if ( is_file( $attachment_path ) )
                unlink( $attachment_path );

			return array( $ticket_upload_id, $attachment_name, 'http://s3.amazonaws.com/' . $this->bucket . "/attachments/{$directory}{$attachment_name}" );
		} else {
			$this->_err( "Failed to upload attachment.\nDirectory: $directory\nAttachment Name: $attachment_name\nBucket: " . $this->bucket, __LINE__, __METHOD__ );
			return false;
		}
	}
	
	/**
	 * Upload an attachment to Amazon S3
	 *
	 * @param string $attachment_name
	 * @param string $attachment_path
	 * @param int $ticket_id
	 * @return string
	 */
	public function upload_ticket_attachment( $attachment_name, $attachment_path, $ticket_id ) {
		global $user;
		
		// Using a different bucket
		$this->bucket = 'retailcatalog.us';
		
		// Put it in a directory for the organization
		$directory = $user['user_id'] . '/' . $user['website']['website_id'] . '/' . $ticket_id . '/';
		
		// Get the file extension
		$file_extension = f::extension( $attachment_name );
		
		// Create the image name
		$attachment_name = format::slug( str_replace( $file_extension, '', $attachment_name ) ) . '.' . $file_extension;
		
		if ( ( $ticket_upload_id = $this->add_upload( $directory . $attachment_name ) ) && $this->s3->putObjectFile( $attachment_path, $this->bucket, 'attachments/' . $directory . $attachment_name, S3::ACL_PUBLIC_READ ) ) {
			if ( is_file( $attachment_path ) )
                unlink( $attachment_path );

			return array( $ticket_upload_id, $attachment_name );
		} else {
			$this->_err( "Failed to upload attachment.\nDirectory: $directory\nAttachment Name: $attachment_name\nBucket: " . $this->bucket, __LINE__, __METHOD__ );
			return false;
		}
	}
	
	/**
	 * Add Upload
	 *
	 * @param string $key
	 * @return int
	 */
	public function add_upload( $key ) {
		$this->db->insert( 'ticket_uploads', array( 'key' => $key, 'date_created' => dt::date('Y-m-d H:i:s') ), 'ss' );
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to add upload.', __LINE__, __METHOD__ );
			return false;
		}
		
		return $this->db->insert_id;
	}
	
	/**
	 * Remove upload
	 *
	 * @param int $ticket_upload_id
	 * @return bool
	 */
	public function remove_upload( $ticket_upload_id ) {
		$key = $this->db->prepare( 'SELECT `key` FROM `ticket_uploads` WHERE `ticket_upload_id` = ?', 'i', $ticket_upload_id )->get_var('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to get ticket upload key.', __LINE__, __METHOD__ );
			return false;
		}
		
		// Using a different bucket
		$this->bucket = 'retailcatalog.us';
		
		// Delete the object
		if ( !$this->s3->deleteObject( $this->bucket, "attachments/{$key}" ) ) {
			$this->_err( "Failed to remove upload.\nURI: $uri\nBucket: " . $this->bucket, __LINE__, __METHOD__ );
			return false;
		}
		
		// Delete from database
		$this->db->prepare( 'DELETE FROM `ticket_uploads` WHERE `ticket_upload_id` = ?', 'i', $ticket_upload_id )->query('');
		
		// Handle any error
		if ( $this->db->errno() ) {
			$this->_err( 'Failed to delete ticket upload.', __LINE__, __METHOD__ );
			return false;
		}
		
		return true;
	}
	
	/**
	 * Remove uploads
	 *
	 * @param array $keys
	 * @return bool
	 */
	public function remove_uploads( $keys ) {
		// Using a different bucket
		$this->bucket = 'retailcatalog.us';
		
		// Delete all the images
		foreach ( $keys as $k ) {
			// Delete the object
			if ( !$this->s3->deleteObject( $this->bucket, "attachments/{$k}" ) ) {
				$this->_err( "Failed to remove upload.\nURI: $uri\nBucket: " . $this->bucket, __LINE__, __METHOD__ );
				return false;
			}
		}
		
		return true;
	}

	
	/**
	 * Delets an image from the Amazon S3
	 *
	 * @param string $image_path the image path (key)
	 * @param string $industry the industry
	 * @returns bool/int
	 */
	public function delete_image( $image_path, $industry ) {
		return $this->s3->deleteObject( $industry . $this->bucket, $image_path );
	}
	
	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
	 */
	private function _err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}
 
?>