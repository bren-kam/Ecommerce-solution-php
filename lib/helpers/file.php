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
		$this->s3 = new S3( config::key('aws-access-key'), config::key('aws-secret-key') );
		$this->bucket = config::key('aws-bucket-domain');
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