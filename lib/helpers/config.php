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
        // Google Analytics API
        , 'ga-client-id-greysuitretail.com'            => '460745244735-8jvkv7s18i40h87ajv56oho34vfr8ksc.apps.googleusercontent.com'
        , 'ga-client-secret-greysuitretail.com'        => 'KhCS6ZYFl84txgtZ3wDYqJ1Q'
        , 'ga-redirect-uri-greysuitretail.com'         => 'http://account.greysuitretail.com/analytics/oauth2callback/'
        , 'ga-client-id-imagineretailer.com'            => '460745244735-oc1ofqo0hrcd9jerd92s53j63s4cdprl.apps.googleusercontent.com'
        , 'ga-client-secret-imagineretailer.com'        => 'O2xxHxt1_i8TTKRnQMoPKnzA'
        , 'ga-redirect-uri-imagineretailer.com'         => 'http://account.imagineretailer.com/analytics/oauth2callback/'
            // Sendgrid
        , 'sendgrid-timezone'       => 'America/Chicago'

        // Facebook
        , 'facebook-key'        => '1613357988885586'
        , 'facebook-secret'     => 'e57ed9886666f429d358355b9be89b06'
        , 'facebook-redirect'   => 'http://account.testing.greysuitretail.com/sm/facebook-callback/'

        // Twitter
        , 'twitter-key'         => 'bC39I7UBwjTfF7FBsg1NxF9GL'
        , 'twitter-secret'      => 'IZFZSKFQGPGw0vXa8MzV2vshmRWt2OQy2Sm4H7ayH5LJIUupiF'
        , 'twitter-redirect'    => 'http://account.testing.greysuitretail.com/sm/twitter-callback/'

        // Foursquare
        , 'foursquare-client-id' => 'F0PYNIMLTK1NJD32JV40G53SH1OJC4BWPJZWTFGYWXZK4AWG'
        , 'foursquare-secret'    => 'LU32XUXDS54FTSIDMC3GPMOEH3U22SAHLTW4VUOKVJCES5VQ'
        , 'foursquare-redirect'  => 'http://account.testing.greysuitretail.com/sm/foursquare-callback/'

        // Encryption-Decryption
        , 'crypto-key'           => 'e4r+wYPQC4bQKikzN4ExKw=='

        , 'stripe-client-id'    => 'ca_63df2cCDX4B8LDPihRd777FxJWCascHM'
        , 'stripe-secret-key'   => 'sk_test_IPB2In890BQD893dJe46yoRh'
	);

    /**
     * The Settings
     * @access private
     * @var array
     */
    private static $settings = array(
        'server-timezone'       => 'America/Chicago'
        , 'server-ip'           => '172.24.32.132'
        , 'server-username'     => 'rpendery'
        , 'server-password'     => 'ZBqgaDxfkR2I'
        , 'server-port'         => 22522
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
            , 'port'        => '22'
        )
        , '162.218.139.218' => array(
            'ip'            => '162.218.139.218'
            , 'username'    => 'root'
            , 'password'    => 'ZjhNCaLL9YhYD5Rf'
            , 'port'        => '22'
        )
        , 'mail09.blinkyblinky.me' => array(
            'ip'            => '162.218.139.218'
            , 'username'    => 'root'
            , 'password'    => 'ZjhNCaLL9YhYD5Rf'
            , 'port'        => '22'
        )
        , '162.218.139.219' => array(
            'ip'            => '162.218.139.219'
            , 'username'    => 'root'
            , 'password'    => 'IlkWJGkM8e75uYxh'
            , 'port'        => '22'
        )
        , '207.97.247.132' => array(
            'ip'            => '172.24.32.132'
            , 'username'    => 'rpendery'
            , 'password'    => 'ZBqgaDxfkR2I'
            , 'port'        => '22522'
        )
        , 'mail10.blinkyblinky.me' => array(
            'ip'            => '162.218.139.219'
            , 'username'    => 'root'
            , 'password'    => 'IlkWJGkM8e75uYxh'
            , 'port'        => '22'
        )
    );

    /**
     * Resource links
     * @access private
     * @var array
     */
    private static $resources = array(
        /* CSS */
        'bootstrap-datepicker-css' => '//cdn.jsdelivr.net/bootstrap.datepicker-fork/1.3.0/css/datepicker3.css'
        , 'jquery-ui' => '//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css'
        , 'videojs-css' => '//vjs.zencdn.net/4.6/video-js.css'
        , 'timepicker-css' => '//cdn.jsdelivr.net/jquery.timepicker/1.3.7/jquery.timepicker.css'
        , 'bootstrap-select-css' => '//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.4/css/bootstrap-select.css'

        /* JS */
        , 'ace-js' => '//ajaxorg.github.io/ace-builds/src-min-noconflict/ace.js'
        , 'bootstrap-datepicker-js' => '//cdn.jsdelivr.net/bootstrap.datepicker-fork/1.3.0/js/bootstrap-datepicker.js'
        , 'bootstrap-validator-js' => '//cdn.jsdelivr.net/jquery.bootstrapvalidator/0.4.5/js/bootstrapValidator.min.js'
        , 'jqueryui-js' => '//code.jquery.com/ui/1.11.0/jquery-ui.min.js'
        , 'typeahead-js' => '//cdn.jsdelivr.net/typeahead.js/0.10.2/typeahead.bundle.js'
        , 'videojs-js' => '//vjs.zencdn.net/4.6/video.js'
        , 'timepicker-js' => '//cdn.jsdelivr.net/jquery.timepicker/1.3.7/jquery.timepicker.js'
        , 'ckeditor-js' => '/ckeditor/ckeditor.js'
        , 'bootstrap-select-js' => '//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.4/js/bootstrap-select.js'
        , 'handlebars-js' => '//cdnjs.cloudflare.com/ajax/libs/handlebars.js/3.0.0/handlebars.min.js'
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