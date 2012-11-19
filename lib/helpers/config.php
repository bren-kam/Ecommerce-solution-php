<?php
/**
 * Contains the main config variables
 *
 * @package Grey Suit Retail
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
        , 'aws_iam-secret-key'      => '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'
		// Amazon S3
		, 'aws-access-key'			=> 'AKIAIM64EVOSIJZMTA3Q'
		, 'aws-secret-key'			=> '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'
		, 'aws-bucket-domain'		=> '.retailcatalog.us'
		// MailChimp
		, 'mc-api'					=> '54c6400139c4f457efb941516f903b98-us1'
		// Craigslist
		, 'craigslist-gsr-id'		=> 2
		, 'craigslist-gsr-key'		=> 'uCYS6jfM6XbyBfZJ'
        // Trumpia
        , 'trumpia-admin-username'  => 'greysuitretail'
        , 'trumpia-admin-password'  => 'V5JC7B#v691j'
        // Studio98 PM API
        , 's98-pm-key'              => '6c309c4529c7a979606f9a8b8d0fc668'
        // Real Statistics API Key
        , 'rs-key'                  => '941cb213d6bbf2dd73c1214fad6321e6'
	);

    /**
     * The Settings
     * @access private
     * @var array
     */
    private static $settings = array(
        'server-timezone'       => 'America/Chicago'
        , 'server-ip'           => '199.79.48.137'
        , 'server-username'     => 'root'
        , 'server-password'     => 'WIxp2sDfRgLMDTL5'
        , 'default-timezone'    => 'America/New_York'
    );

    /**
     * Resource links
     * @access private
     * @var array
     */
    private static $resources = array(
        'jquery-ui' => 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/themes/ui-lightness/jquery-ui.css'
    );
		
	/**
	 * Returns a key
	 *
	 * @param string $key
	 * @return bool
	 */
	public static function key( $key ) {
		return self::$keys[$key];
	}

	/**
	 * Returns a setting
	 *
	 * @param string $setting
	 * @return bool
	 */
	public static function setting( $setting ) {
		return self::$settings[$setting];
	}

	/**
	 * Returns a resource
	 *
	 * @param string $resource
	 * @return bool
	 */
	public static function resource( $resource ) {
		return self::$resources[$resource];
	}
}