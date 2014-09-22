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
	 * Endpoint configuration
	 */
    protected $debug = false;
    protected $username = 'root';
    protected $url_api;
    protected $hash;

    /**
	 * A few variables that will determine the basic status
	 */
	protected $message = NULL;
	protected $success = false;
	protected $raw_request = NULL;
	protected $request = NULL;
	protected $raw_response = NULL;
	protected $response = NULL;
    protected $error = NULL;
	protected $params = array();

    /**
     * Constructor
     *
     * @param Server $server Where we are connecting to
     */
    public function __construct( Server $server ) {
        $this->url_api = "https://{$server->ip}:2087/json-api/";
        $this->hash = $server->whm_hash;
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
     * @param string $contactemail [optional]
     * @param string $password [optional]
     * @param string $pkgname [optional]
     * @param int $savepkg [optional]
     * @param string $featurelist [optional]
     * @param int $quota [optional]
     * @param string $ip [optional]
     * @param int $cgi [optional]
     * @param int $frontpage [optional]
     * @param int $hasshell [optional]
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
    public function create_account( $username, $domain, $plan = '', $contactemail = '', $password = '', $pkgname = '', $savepkg = 0, $featurelist = '', $quota = 3000, $ip = 'n', $cgi = 1,
                                    $frontpage = 0, $hasshell = 1, $cpmod = 'x3', $maxftp = '0', $maxsql = '0', $maxpop = '0',
                                    $maxlst = '0', $maxsub = '0', $maxpark = '5', $maxaddon = '5', $bwlimit = '25000', $customip = '',
                                    $language = 'en', $useregns = 0, $hasuseregns = 0, $reseller = 0, $forcedns = 0, $mxcheck = 'auto',
                                    $MAX_EMAIL_PER_HOUR = 0, $MAX_DEFER_FAIL_PERCENTAGE = 0, $MIN_DEFER_FAIL_TO_TRIGGER_PROTECTION = 0 ) {

        $this->_execute( 'createacct', compact( 'username', 'domain', 'plan', 'pkgname', 'savepkg', 'featurelist', 'quota', 'password', 'ip', 'cgi', 'frontpage', 'hasshell', 'contactemail', 'cpmod', 'maxftp', 'maxsql', 'maxpop', 'maxlst', 'maxsub', 'maxpark', 'maxaddon', 'bwlimit', 'customip', 'language', 'useregns', 'hasuseregns', 'reseller', 'forcedns', 'mxcheck', 'MAX_EMAIL_PER_HOUR', 'MAX_DEFER_FAIL_PERCENTAGE', 'MIN_DEFER_FAIL_TO_TRIGGER_PROTECTION' ) );

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
	
	/**
	 * Domain User Data
	 *
	 * @param string $domain
	 * @return array
	 */
	public function domain_user_data( $domain ) {
		$response = $this->_execute( 'domainuserdata', compact( 'domain' ) );
		
		return ( $this->success() ) ? $response->userdata : false;
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
	protected function _execute( $method, $params = array() ) {
        // Authorization
		$header[0] = "Authorization: WHM " . $this->username . ':'  . preg_replace( "'(\r|\n)'", "", $this->hash );

        // Set Request Parameters
		$this->request = $params;
        $this->raw_request = http_build_query( $this->request );

        // Set URL
        $url = $this->url_api . "$method";

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
		if ( 'cpanel' == $method ) {
			$this->success = !isset( $this->response->cpanelresult->error );
			$this->message = ( $this->success ) ? $this->response->cpanelresult->error : '';
		} else {
			if ( isset( $this->response->status ) ) {
				$this->success = '1' == $this->response->status;
				$this->message = $this->response->statusmsg;
			} else {
				$this->success = '1' == $this->response->result[0]->status;
				$this->message = $this->response->result[0]->statusmsg;
			}
		}

		$this->error = ( $this->success ) ? NULL : true;
		
        // If we're debugging lets give as much info as possible
        if ( $this->debug ) {
            echo "<h1>URL</h1>\n<p>", $url, "</p>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Request</h1>\n<pre>", $this->raw_request, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Request</h1>\n\n<pre>", var_export( $this->request, true ), "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Response</h1>\n<pre>", $this->raw_response, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Response</h1>\n<pre>", var_export( $this->response, true ), "</pre>\n<hr />\n<br /><br />\n";
        }

		return $this->response;
	}
}