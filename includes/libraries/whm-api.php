<?php
/**
 * WHM - API Library
 *
 * Library based on documentation available on 04/26/2012 from
 * @url http://docs.cpanel.net/twiki/bin/view/SoftwareDevelopmentKit/LivePHP#The%20CPANEL%20PHP%20Class
 *
 */
class WHM_API {
    /**
	 * Constant paths to include files
	 */
	const URL_API = 'https://199.79.48.137:2087/json-api/';
	const DEBUG = false;
    const USERNAME = 'root';
    CONST HASH = '9943eb7e9b6bfa89d7ddea127cdb0b6a
493a235756c165c95f009d1256aa66a2
89f9cb5ac3d47cdc326c6f44b921918a
3c606a91c08715af6903dc3625192ac2
52778a5c4e6c08a2e81e2e91df1a9169
2950db9b6be4cee7c81022a530e5033f
beccd34cd80db9c5c9a3931a3a574986
94c67ef677908feeaa062fe95b9795f7
98a79efee3afa8ddcff8319f552f63ba
aa055b0f1af87a3f11f8bb49e3703fbd
f66469c6b1f5ab56f8ccbc83206b2de4
85412c9dd23fa8ae6237cfdfb2991f14
010b1ade0436a3e058a3b375b593826b
563966df1dbc50f4f00b4e55483a2cc7
c856e7110145d0a7d4afe0380a63fc30
a8add9eae50d38cd428f3d765adb3e6a
2bcfe70caff0174418e8fc4314581831
7b21bf086bc769eb1d0b07015abf171d
e83e1e3d1c3fe57a68be4baabf4c8e6c
b9625d900559c72003c557f931d0643b
8fb9448f1b0cde9f0c80550f94135558
d6d041ffd56a3369f493556895548372
c2377908426a7a215b3ff13ba8453493
609555163237e1b714d34f3cb87fbf89
d2d1eea3b3e14b0eea236a336800777b
8a3d4497e453b665a39165085e35c6c3
6549cdd220216be2627ec41601a2d89f
44e96947c379d9ded9303fd4482cc3cc
e530ebbdf4dacc8e383b51e1d3009c5d';

    /**
	 * A few variables that will determine the basic status
	 */
	private $message = NULL;
	private $success = false;
	private $raw_request = NULL;
	private $request = NULL;
	private $raw_response = NULL;
	private $response = NULL;
    private $error = NULL;

	/**
	 * Construct class will initiate and run everything
	 */
	public function __construct() {
	}

	/**************************/
	/* Start: WHM API Methods */
	/**************************/

    /**
     * App List
     *
     * @return array
     */
    public function app_list() {
        $response = $this->_execute( 'applist' );

        return ( is_array( $response->app ) ) ? $response->app : false;
    }

    /**
     * Create Account
     *
     * @param string $username
     * @param string $domain
     * @param string $plan [optional]
     * @param string $pkgname [optional]
     * @param int $savepkg [optional]
     * @param string $featurelist [optional]
     * @param int $quota [optional]
     * @param string $ip [optional]
     * @param int $cgi [optional]
     * @param int $frontpage [optional]
     * @param int $hasshell [optional]
     * @param string $contactemail [optional]
     * @param string $cpmod [optional]
     * @param string $maxftp [optional]
     * @param string $maxsql [optional]
     * @param string $maxpop [optional]
     * @param string $maxlst [optional]
     * @param string $maxsub [optional]
     * @param string $maxpark [optional]
     * @param string $maxaddon [optional]
     * @param string $bwlimit [optional]
     * @param string $customip [optional]
     * @param string $language [optional]
     * @param int $useregns [optional]
     * @param int $hasuseregns [optional]
     * @param int $reseller [optional]
     * @param int $forcedns [optional]
     * @param string $mxcheck [optional]
     * @param int $MAX_EMAIL_PER_HOUR [optional]
     * @param int $MAX_DEFER_FAIL_PERCENTAGE [optional]
     * @param int $MIN_DEFER_FAIL_TO_TRIGGER_PROTECTION [optional]
     * @return bool
     */
    public function create_account( $username, $domain, $plan = '', $pkgname = '', $savepkg = 0, $featurelist = '', $quota = 3000, $ip = 'n', $cgi = 1,
                                    $frontpage = 0, $hasshell = 1, $contactemail = '', $cpmod = 'x3', $maxftp = '0', $maxsql = '0', $maxpop = '0',
                                    $maxlst = '0', $maxsub = '0', $maxpark = '5', $maxaddon = '5', $bwlimit = '25000', $customip = '',
                                    $language = 'en', $useregns = 0, $hasuseregns = 0, $reseller = 0, $forcedns = 0, $mxcheck = 'auto',
                                    $MAX_EMAIL_PER_HOUR = 0, $MAX_DEFER_FAIL_PERCENTAGE = 0, $MIN_DEFER_FAIL_TO_TRIGGER_PROTECTION = 0 ) {

        $this->_execute( 'createacct', compact( 'username', 'domain', 'plan', 'pkgname', 'savepkg', 'featurelist', 'quota', 'ip', 'cgi', 'frontpage', 'hasshell', 'contactemail', 'cpmod', 'maxftp', 'maxsql', 'maxpop', 'maxlst', 'maxsub', 'maxpark', 'maxaddon', 'bwlimit', 'customip', 'language', 'useregns', 'hasuseregns', 'reseller', 'forcedns', 'mxcheck', 'MAX_EMAIL_PER_HOUR', 'MAX_DEFER_FAIL_PERCENTAGE', 'MIN_DEFER_FAIL_TO_TRIGGER_PROTECTION' ) );

        return $this->success();
    }

    /**
     * Account Summary
     *
     * @param string $user the username
     * @return array
     */
    public function account_summary( $user ) {
        $response = $this->_execute( 'accountsummary', compact( 'user' ) );

        return ( $this->success() ) ? $response->acct : false;
    }

	/************************/
	/* END: WHM API Methods */
	/************************/

    /**
     * Get private message variable
     *
     * @return string
     */
    public function message() {
        return $this->message;
    }

    /**
     * Get private success variable
     *
     * @return string
     */
    public function success() {
        return $this->success;
    }

    /**
     * Get private raw_request variable
     *
     * @return string
     */
    public function raw_request() {
        return $this->raw_request();
    }

    /**
     * Get private request variable
     *
     * @return array Object
     */
    public function request() {
        return $this->request;
    }

    /**
     * Get private raw_response variable
     *
     * @return string
     */
    public function raw_response() {
        return $this->raw_response;
    }

    /**
     * Get private response variable
     *
     * @return stdClass Object
     */
    public function response() {
        return $this->response;
    }

    /**
     * Get private error variable
     *
     * @return string
     */
    public function error() {
        return $this->error;
    }

	/**
	 * This sends sends the actual call to the API Server and parses the response
	 *
	 * @param string $method The method being called
	 * @param array $params an array of the parameters to be sent
     * @return stdClass object
	 */
	private function _execute( $method, $params = array() ) {
        // Authorization
		$header[0] = "Authorization: WHM " . self::USERNAME . ':'  .preg_replace( "'(\r|\n)'", "", self::HASH );

        // Set Request Parameters
		$this->request = $params;
        $this->raw_request = http_build_query( $this->request );

        // Set URL
        $url = self::URL_API . "$method";

        if ( count( $this->request ) > 0 )
            $url .= '?' . $this->raw_request;

        // Initialize cURL and set options
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $header );
		curl_setopt( $ch, CURLOPT_URL, $url );

        // Perform the request and get the response
        $this->raw_response = curl_exec( $ch );

        // Decode the response
        $this->response = json_decode( $this->raw_response );

        // Close cURL
        curl_close($ch);

        // Set the response
        $this->success = '1' == $this->response->status;
        $this->message = $this->response->statusmsg;
        $this->error = ( $this->success ) ? NULL : true;

        // If we're debugging lets give as much info as possible
        if ( self::DEBUG ) {
            echo "<h1>URL</h1>\n<p>", $url, "</p>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Request</h1>\n<pre>", $this->raw_request, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Request</h1>\n\n<pre>", var_export( $this->request, true ), "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Response</h1>\n<pre>", $this->raw_response, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Response</h1>\n<pre>", var_export( $this->response, true ), "</pre>\n<hr />\n<br /><br />\n";
        }

		return $this->response;
	}
}