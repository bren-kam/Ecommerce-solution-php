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
		// Amazon S3 - Manager: http://www.s3fm.com/
		, 'aws-access-key'			=> 'AKIAIM64EVOSIJZMTA3Q'
		, 'aws-secret-key'			=> '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'
		, 'aws-bucket-domain'		=> '.retailcatalog.us'
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
        // Google Analytics
        , 'ga-username'             => 'web@imagineretailer.com'
        , 'ga-password'             => 'imagine1010'
        // Sendgrid
        , 'sendgrid-timezone'       => 'America/Chicago'
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
     * Servers
     * @access private
     * @var array
     */
    private static $servers = array(
        'legacy' => array(
            'ip'            => '199.79.48.137'
            , 'username'    => 'root'
            , 'password'    => 'WIxp2sDfRgLMDTL5'
        )
        , '162.218.139.218' => array(
            'ip'            => '162.218.139.218'
            , 'username'    => 'root'
            , 'password'    => 'ZjhNCaLL9YhYD5Rf'
        )
        , 'mail09.blinkyblinky.me' => array(
            'ip'            => '162.218.139.218'
            , 'username'    => 'root'
            , 'password'    => 'ZjhNCaLL9YhYD5Rf'
        )
        , '162.218.139.219' => array(
            'ip'            => '162.218.139.219'
            , 'username'    => 'root'
            , 'password'    => 'IlkWJGkM8e75uYxh'
        )
        , 'mail10.blinkyblinky.me' => array(
            'ip'            => '162.218.139.219'
            , 'username'    => 'root'
            , 'password'    => 'IlkWJGkM8e75uYxh'
        )
    );

    /**
     * Resource links
     * @access private
     * @var array
     */
    private static $resources = array(
        /* CSS */
        'bootstrap-datepicker-css' => '//cdn.jsdelivr.net/bootstrap.datepicker/0.1/css/datepicker.css'
        , 'jquery-ui' => '//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css'

        /* JS */
        , 'ace-js' => '//ajaxorg.github.io/ace-builds/src-min-noconflict/ace.js'
        , 'bootstrap-datepicker-js' => '//cdn.jsdelivr.net/bootstrap.datepicker/0.1/js/bootstrap-datepicker.js'
        , 'bootstrap-validator-js' => '//cdn.jsdelivr.net/jquery.bootstrapvalidator/0.4.5/js/bootstrapValidator.min.js'
        , 'jqueryui-js' => '//code.jquery.com/ui/1.11.0/jquery-ui.min.js'
        , 'typeahead-js' => '//cdn.jsdelivr.net/typeahead.js/0.10.2/typeahead.bundle.js'
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
	 * @return mixed
	 */
	public static function setting( $setting ) {
		return self::$settings[$setting];
	}

	/**
	 * Returns a setting
	 *
	 * @param string $setting
     * @param string $server
	 * @return mixed
	 */
	public static function server( $setting, $server = NULL ) {
        if ( is_null( $server ) )
            $server = $_SERVER['HOSTNAME'];

		return self::$servers[$server][$setting];
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