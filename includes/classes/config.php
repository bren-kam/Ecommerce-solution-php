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
		// Amazon S3
		'aws-access-key'			=> 'AKIAIM64EVOSIJZMTA3Q',
		'aws-secret-key'			=> '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',
		'aws-bucket-domain'			=> '.retailcatalog.us',
		// MailChimp
		'mc-api'					=> '54c6400139c4f457efb941516f903b98-us1'
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