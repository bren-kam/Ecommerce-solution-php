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
	 * Construct initializes data
	 */
	public function __construct() {
		// Load Amazon S3
		library('S3');
		$this->s3 = new S3( Config::key('aws-access-key'), Config::key('aws-secret-key') );
		$this->bucket = Config::key('aws-bucket-domain');
	}

    /**
	 * Uploads an Image to Amazon
	 *
	 * @param file|string $image the product image file
	 * @param string $new_image_name the new image name
	 * @param int $width the width you want the image to be
	 * @param int $height the height you want the image to be
	 * @param string $industry the industry to upload it under
	 * @param string $directory (Optional) any path to the directory you want the file to be in
	 * @param bool $keep_proportions (Optional|true) keep image proportions
	 * @param bool fill_constraints (Optional|true) fill the constraints given
	 * @return bool/string
	 */
	public function upload_image( $image, $new_image_name, $width, $height, $industry, $directory = '', $keep_proportions = true, $fill_constraints = true ) {
        // Get hte image path
        $image_path = ( is_string( $image ) ) ? $image : $image['tmp_name'];

		if ( empty( $image_path ) || empty( $industry ) )
			return false;

		list( $result, $image_file ) = image::resize( $image_path, TMP_PATH . 'media/uploads/images/', $new_image_name, $width, $height, 90, $keep_proportions, $fill_constraints );

		if ( !$result || !$image_file || !is_file( $image_file ) )
			return false;

		// Define the base name
		$base_name = basename( $image_file );

		// Upload the image
		if ( !$this->s3->putObjectFile( $image_file, $industry . $this->bucket, $directory . $base_name, S3::ACL_PUBLIC_READ ) )
			return false;

        // Delete the local image
        unlink( $image_file );

        // Return image name
        return $base_name;
	}

    /**
     * Upload File
     *
     * @param string $file_path
     * @param string $directory
     * @param string $name
     * @return string|bool
     */
    public function upload_file( $file_path, $directory, $name ) {
		if ( !$this->s3->putObjectFile( $directory, $this->bucket, 'attachments/' . $directory . $name, S3::ACL_PUBLIC_READ ) )
            return false;

        unlink( $file_path );

        return 'http://s3.amazonaws.com/' . $this->bucket . "/attachments/{$directory}{$name}";
    }

    /**
	 * Deletes an image from the Amazon S3
	 *
	 * @param string $image_path (key)
	 * @param string $industry
	 * @return bool
	 */
	public function delete_image( $image_path, $industry ) {
		return $this->s3->deleteObject( $industry . $this->bucket, $image_path );
	}
}