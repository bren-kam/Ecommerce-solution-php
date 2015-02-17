<?php
/**
 * CloudFlare Host API
 *
 * @author Kerry Jones
 * @date 2/16/2015
 * @url https://www.cloudflare.com/docs/host-api.html
 */

class CloudFlareHostAPI {
    const DEBUG = false;
    const URL = 'https://api.cloudflare.com/host-gw.html';
    const HOST_API_KEY = 'replace_me';

    /**
     * @var Account
     */
    protected $account;

    /**
     * Construct class
     *
     * @param Account $account This is for logging
     */
    public function __construct( Account $account ) {
        $this->account = $account;
    }

    /**
     * A few variables that will determine the basic status
     */
    protected $message = NULL;
    protected $success = false;
    protected $request = NULL;
    protected $raw_response = NULL;
    protected $response = NULL;
    protected $error = NULL;
    protected $params = array();

    /**
     * Basic Error messages
     */
    protected $errors = array(
        'E_UNAUTH' => 'Authentication could not be completed'
        , 'E_INVLDINPUT' => 'Input was not valid'
        , 'E_MAXAPI' => 'You have exceeded your allowed number of API calls'
    );

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
     * User Create
     *
     * @date 2/16/2015
     *
     * @param string $email
     * @param string $password
     * @param int $user_id
     * @return string $user_key
     */
    public function user_create( $email, $password, $user_id ) {
        $this->execute( 'user_create', array(
            'cloudflare_email' => $email
            , 'cloudflare_pass' => $password
            , 'cloudflare_username' => $email
            , 'unique_id' => $user_id
        ) );

        return $this->response->response->user_key;
    }

    /**
     * This sends sends the actual call to the API Server and parses the response
     *
     * @param string $method The method being called
     * @param array $params an array of the parameters to be sent
     * @return stdClass object
     */
    public function execute( $method, $params = array() ) {
        // Set Request Parameters
        $this->request = array_merge( array(
                'host_key'       => self::HOST_API_KEY
                , 'act'       => $method
            ), $params
        ) ;

        // Initialize cURL and set options
        $ch = curl_init();

		$url = self::URL;
        curl_setopt( $ch, CURLOPT_FORBID_REUSE, true );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $this->request );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

        // Perform the request and get the response
        $this->raw_response = curl_exec( $ch );

        // Decode the response
        $this->response = json_decode( $this->raw_response );

        // Close cURL
        curl_close($ch);

        $this->success = 'success' == $this->response->result;

        // Set the response
        if ( $this->success ) {
            $this->message = 'Success!';
        } else {
            $this->error = true;
            $this->message = ( isset( $this->errors[$this->response->msg] ) ) ? $this->errors[$this->response->msg] : $this->response->msg;
        }

        // If we're debugging lets give as much info as possible
        if ( self::DEBUG ) {
            echo "<h1>URL</h1>\n<p>", self::URL, "</p>\n<hr />\n<br /><br />\n";
            echo "<h1>Request</h1>\n\n<pre>", var_export( $this->request, true ), "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Raw Response</h1>\n<pre>", $this->raw_response, "</pre>\n<hr />\n<br /><br />\n";
            echo "<h1>Response</h1>\n<pre>", var_export( $this->response, true ), "</pre>\n<hr />\n<br /><br />\n";
        }

        $api_log = new ApiExtLog();
        $api_log->website_id = $this->account->id;
        $api_log->api = 'CloudFlare Client API';
        $api_log->method = $method;
        $api_log->url = self::URL;
        $api_log->request = json_encode( $this->request );
        $api_log->raw_request = 'N/A';
        $api_log->response = json_encode( $this->response );
        $api_log->raw_response = $this->raw_response;
        $api_log->create();

        return $this->response;
    }
}