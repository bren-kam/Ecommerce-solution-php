<?php
class File {
    /**
     * Amazon S3 Wrapper
     *
     * @var S3
     */
    protected $s3;

    /**
     * Amazon S3 Bucket
     *
     * @var string
     */
    protected $bucket;
    
    /**
     * Key used to store file list in cache
     * 
     * @var string 
     */
    protected $cache_key;

    /**
	 * Construct initializes data
     *
     * @param string $override_bucket
	 */
	public function __construct( $override_bucket = NULL ) {
		// Load Amazon S3
		library('S3');
		$this->s3 = new S3( Config::key('aws-access-key'), Config::key('aws-secret-key') );
		$this->bucket = ( is_null( $override_bucket ) ) ? Config::key('aws-bucket-domain') : $override_bucket;
		$this->cache_key = 's3_file_list_' . $this->bucket;
	}

    /**
     * Set bucket
     *
     * @param string $bucket
     */
    public function set_bucket( $bucket ) {
        $this->bucket = $bucket;
		$this->cache_key = 's3_file_list_' . $this->bucket;
    }

    /**
	 * Uploads an Image to Amazon
	 *
     * @throws InvalidParametersException|HelperException
     *
	 * @param file|string $image the product image file
	 * @param string $new_image_name the new image name
	 * @param int $width the width you want the image to be
	 * @param int $height the height you want the image to be
	 * @param string $subdomain the industry to upload it under
	 * @param string $directory (Optional) any path to the directory you want the file to be in
	 * @param bool $keep_proportions (Optional|true) keep image proportions
	 * @param bool $fill_constraints (Optional|true) fill the constraints given
     * @return string
	 */
	public function upload_image( $image, $new_image_name, $width, $height, $subdomain, $directory = '', $keep_proportions = true, $fill_constraints = true ) {
        // Get hte image path
        $image_path = ( is_string( $image ) ) ? $image : $image['tmp_name'];
        
		if ( empty( $image_path ) || empty( $subdomain ) )
			throw new InvalidParametersException( 'The image path could not be determined.' );
		
		list( $result, $image_file ) = image::resize( $image_path, TMP_PATH . 'media/uploads/images/', $new_image_name, $width, $height, 90, $keep_proportions, $fill_constraints );
		
		if ( !$result || !$image_file || !is_file( $image_file ) )
            throw new HelperException( "Failed to resize image" );

		// Define the base name
		$base_name = basename( $image_file );

		// Upload the image
		if ( !$this->s3->putObjectFile( $image_file, $subdomain . $this->bucket, $directory . $base_name, S3::ACL_PUBLIC_READ, array(), S3::__getMimeType( $base_name ) ) )
			throw new HelperException( "Failed to put image on Amazon S3" );

        // Delete the local image
        unlink( $image_file );
		Cache::delete( $this->cache_key );
        
        // Return image name
        return $base_name;
	}

    /**
     * Upload File
     *
     * @throws HelperException
     * 
     * @param string $file_path
     * @param string $key
     * @param string $dir [optional]
     * @return string|bool
     */
    public function upload_file( $file_path, $key, $dir = '' ) {
        if ( !$this->s3->putObjectFile( $file_path, $this->bucket, $dir . $key, S3::ACL_PUBLIC_READ, array(), S3::__getMimeType( $key ) ) )
            throw new HelperException( 'Amazon S3 failed to upload file' );

        unlink( $file_path );
        
		Cache::delete( $this->cache_key );

        return 'http://s3.amazonaws.com/' . $this->bucket . '/' . $dir . $key;
    }

    /**
	 * Copy File
     *
     * Copes a file in Amazon S3
	 *
     * @throws HelperException
     * 
	 * @param int $account_id
	 * @param string $url
     * @param string $bucket
	 * @return mixed
	 */
	public function copy_file( $account_id, $url, $bucket ) {
        $bucket = $bucket . $this->bucket;

        $uri = str_replace( 'http://' . $bucket . '/', '', $url );
        $new_uri = preg_replace( '/^([0-9]+)/', $account_id, $uri );

		if ( !$this->s3->copyObject( $bucket, $uri, $bucket, $new_uri, S3::ACL_PUBLIC_READ ) )
            throw new HelperException( 'Amazon S3 failed to copy file' );

		Cache::delete( $this->cache_key );

        return 'http://' . $bucket . '/' . $new_uri;
	}

    /**
	 * Deletes an image from the Amazon S3
	 *
	 * @param string $image_path (key)
	 * @param string $subdomain
	 * @return bool
	 */
	public function delete_image( $image_path, $subdomain ) {
        // remove whitespaces from subdomain
        $subdomain = str_replace(' ', '', $subdomain);
		return $this->s3->deleteObject( $subdomain . $this->bucket, $image_path );
	}

    /**
     * Delete File
     *
     * @param string $key
     * @param string $dir [optional]
     * @return bool
     */
    public function delete_file( $key, $dir = '' ) {
		// Delete the object
		$result = $this->s3->deleteObject( $this->bucket, $dir . $key );
		Cache::delete( $this->cache_key );
		return $result;
    }

    /**
     * List Files
     *
     * @return array
     */
    public function list_files() {
		$files =  Cache::get( $this->cache_key );
		if ( !$files ) {
			$files = $this->s3->getBucket( $this->bucket );
			Cache::set( $this->cache_key, $files );
		}
		return $files; 
   }

    /**
     * Search Files
     *
     * Search for files in S3
     *
     * @param null $pattern
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function search_files( $pattern = null, $offset = 0, $limit = 50 ) {
        $files = $this->list_files();

        // will be much better if we search in S3 directly!
        if ( $pattern ) {
            foreach ( $files as $k => &$v ) {
                if( stripos( $k, $pattern ) === FALSE) {
                    unset( $files[$k] );
                }
            }
        }

        return array_slice( $files, $offset, $limit, true );
    }
}