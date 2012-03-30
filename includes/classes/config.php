<?php
/**
 * Contains the main config variables
 *
 * @package Imagine Retailer
 * @since 1.0.0
 */

class Config {
	/**
	 * The keys
	 * @access private
	 * @var array
	 */
	private static $keys = array(
        // Amazon IAM
        'aws_iam-access-key'        => 'AKIAJAQUNFY65EV5HJ7A'
        , 'aws_iam-secret-key'        => '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'
		// Amazon S3
		, 'aws-access-key'			=> 'AKIAIM64EVOSIJZMTA3Q'
		, 'aws-secret-key'			=> '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'
		, 'aws-bucket-domain'			=> '.retailcatalog.us'
		// MailChimp
		, 'mc-api'					=> '54c6400139c4f457efb941516f903b98-us1'
		// Craigslist
		, 'craigslist-gsr-id'			=> 2
		, 'craigslist-gsr-key'		=> 'uCYS6jfM6XbyBfZJ'
	);
		
	/**
	 * Returns a key
	 *
	 * @since 1.0.0
	 *
	 * @param string $key
	 * @return bool
	 */
	public static function key( $key ) {
		return self::$keys[$key];
	}
}