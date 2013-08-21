<?php
/**
 * Active Campaign - API Library
 *
 * Library based on documentation available on 07/03/2013 from
 * @url http://www.activecampaign.com/api/overview.php
 *
 */

class ActiveCampaignAPI {
    /**
     * Constant paths to include files
     */
    const DEBUG = false;
    const API_OUTPUT = 'json';
    const REQUEST_TYPE_GET = 0;
    const REQUEST_TYPE_POST = 1;

    /**
   	 * Hold the api data
   	 *
   	 * @var string
   	 */
   	protected $api_url, $api_key;

    /**
     * Hold list
     *
     * @var ActiveCampaignListAPI
     */
    public $list;

    /**
     * Hold contact
     *
     * @var ActiveCampaignContactAPI
     */
    public $contact;

    /**
     * Hold webhook
     *
     * @var ActiveCampaignWebhookAPI
     */
    public $webhook;

    /**
     * Hold campaign
     *
     * @var ActiveCampaignCampaignAPI
     */
    public $campaign;

    /**
     * Hold message
     *
     * @var ActiveCampaignMessageAPI
     */
    public $message;

    /**
     * A few variables that will determine the basic status
     */
    protected $response_message = NULL;
    protected $success = false;
    protected $raw_request = NULL;
    protected $request = NULL;
    protected $raw_response = NULL;
    protected $response = NULL;
    protected $error = NULL;
    protected $params = array();
	 
	/**
	 * Construct class will initiate and run everything
     *
     * @param string $api_url
     * @param string $api_key
	 */
	public function __construct( $api_url, $api_key ) {
        $this->api_url = $api_url;
		$this->api_key = $api_key;
	}

    /**
     * Get private message variable
     *
     * @return string
     */
    public function message() {
        return $this->response_message;
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
     * Setup a sub section
     */
    public function setup_list() {
        $this->_setup( 'list' );
    }

    /**
     * Setup a sub section
     */
    public function setup_contact() {
        $this->_setup( 'contact' );
    }

    /**
     * Setup a sub section
     */
    public function setup_webhook() {
        $this->_setup( 'webhook' );
    }

    /**
     * Setup a sub section
     */
    public function setup_campaign() {
        $this->_setup( 'campaign' );
    }

    /**
     * Setup a sub section
     */
    public function setup_message() {
        $this->_setup( 'message' );
    }

    /**
     * This sends sends the actual call to the API Server and parses the response
     *
     * @param string $method The method being called
     * @param array $params an array of the parameters to be sent
     * @param int $request_type [optional]
     * @return stdClass object
     */
    public function execute( $method, $params = array(), $request_type = self::REQUEST_TYPE_GET ) {
        // Set Request Parameters
        $this->request = array_merge( array(
            'api_key' => $this->api_key
            , 'api_action' => $method
            , 'api_output' => self::API_OUTPUT
        ), $params);

        $this->raw_request = http_build_query( $this->request );

        // Set URL
        $url = $this->api_url . '/admin/api.php?';

        if ( self::REQUEST_TYPE_GET == $request_type )
            $url .=  $this->raw_request;

        // Initialize cURL and set options
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array("Expect:") );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );

        if ( self::REQUEST_TYPE_POST == $request_type )
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $this->raw_request );

        curl_setopt( $ch, CURLOPT_URL, $url );

        // Perform the request and get the response
        $this->raw_response = curl_exec( $ch );

        // Decode the response
        $this->response = json_decode( $this->raw_response );

        // Close cURL
        curl_close($ch);

        // Set the response
        $this->success = 1 == $this->response->result_code;
        $this->response_message = $this->response->result_message;

        $this->error = ( $this->success ) ? NULL : true;

        // If we're debugging lets give as much info as possible
        if ( self::DEBUG ) {
            echo "<h1>URL</h1>\n<p>", $url, "</p>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Request</h1>\n<pre>", $this->raw_request, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Request</h1>\n\n<pre>", var_export( $this->request, true ), "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Response</h1>\n<pre>", $this->raw_response, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Response</h1>\n<pre>", var_export( $this->response, true ), "</pre>\n<hr />\n<br /><br />\n";
        }

        $api_log = new ApiExtLog();
        $api_log->api = 'Active Campaign API';
        $api_log->method = $method;
        $api_log->url = $url;
        $api_log->request = json_encode( $this->request );
        $api_log->raw_request = $this->raw_request;
        $api_log->response = json_encode( $this->response );
        $api_log->raw_response = $this->raw_response;
        $api_log->create();


        return $this->response;
    }

    /**
     * Setup a section
     *
     * @param string $section
     */
    private function _setup( $section ) {
        if ( is_null( $this->$section ) ) {
            library( "ac-api/$section" );
            $class_name = 'ActiveCampaign' . ucwords( $section ) . 'API';
            $this->$section = new $class_name( $this );
        }
    }
}