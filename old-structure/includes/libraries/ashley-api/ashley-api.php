<?php
/**
 * Ashley - API Library
 *
 * Library based on documentation available on 04/26/2012 from
 * @url http://api.ashleyfurniture.com/Ashley.ProductKnowledge.Maintenance.NewService/Services/ProductKnowledgeService.asmx
 *
 */

class Ashley_API {
    /**
	 * Constant paths to include files
	 */
	const URL_API = 'http://api.ashleyfurniture.com/';
    const URL_WSDL = 'http://api.ashleyfurniture.com/Ashley.ProductKnowledge.Maintenance.NewService/Services/ProductKnowledgeService.asmx?WSDL';
	const DEBUG = false;

    /**
	 * A few variables that will determine the basic status
	 */
    private $_method = NULL;
	private $_message = NULL;
	private $_success = false;
	private $_request_headers = NULL;
	private $_request = NULL;
	private $_response_headers = NULL;
	private $_raw_response = NULL;
	private $_full_response = NULL;
	private $_response = NULL;

    /**
     * Ashley Client
     *
     * @acccess private
     * @var SoapClient
     */
    private $ashley;

    /**
     * Construct to setup SOAP module
     */
    public function __construct() {
        // Get classes we need
        library('ashley-api/product-knowledge');

        // Initiate the client
        $this->ashley = new SoapClient( self::URL_WSDL, array( 'trace' => 1 ) );
    }

	/*************************/
	/* Start: Ashley Methods */
	/*************************/

    /**
     * Get Packages
     *
     * @return object
     */
    public function get_packages() {
        // Setup the package request
        $package_request = new PackageRequest();
        $package_request->ExecuteOptions = array( 'PackageExecuteOption' => 'LoadPackages' );

        // Execute the response
        $this->_execute( 'GetPackages', new GetPackages( $package_request ) );

        if ( !$this->_success )
            return false;

        // SimpleXML errors out if it thinks its reading utf-16
        $packages = simplexml_load_string( str_replace( 'utf-16', 'utf-8', $this->_response->PackagesCollection->XmlData ) );

        return $packages;
    }

	/***********************/
	/* END: Ashley Methods */
	/***********************/

    /**
     * Get private message variable
     *
     * @return string
     */
    public function message() {
        return $this->_message;
    }

    /**
     * Get private success variable
     *
     * @return string
     */
    public function success() {
        return $this->_success;
    }

    /**
     * Get private request_headers variable
     *
     * @return string
     */
    public function request_headers() {
        return $this->_request_headers;
    }

    /**
     * Get private request variable
     *
     * @return array Object
     */
    public function request() {
        return $this->_request;
    }

    /**
     * Get private response_headers variable
     *
     * @return string
     */
    public function response_headers() {
        return $this->_response_headers;
    }

    /**
     * Get private raw response variable
     *
     * @return stdClass Object
     */
    public function _raw_response() {
        return $this->_raw_response;
    }

    /**
     * Get private full response variable
     *
     * @return stdClass Object
     */
    public function _full_response() {
        return $this->_full_response;
    }

    /**
     * Get private response variable
     *
     * @return stdClass Object
     */
    public function response() {
        return $this->_response;
    }

    /**
     * Display debug information
     */
    public function debug() {
        echo "<h1>Method</h1>\n<p>" . $this->_method . "</p>\n<hr />\n<br /><br />\n";
        echo "<h1>Request Headers</h1>\n<pre>", $this->_request_headers, "</pre>\n<hr />\n<br /><br />\n";
        echo "<h1>Request</h1>\n\n<textarea style='width:100%;height:150px;' cols='50' rows='5'>", $this->_request, "</textarea>\n<hr />\n<br /><br />\n";
        echo "<h1>Response Headers</h1>\n<pre>", $this->_response_headers, "</pre>\n<hr />\n<br /><br />\n";
        echo "<h1>Raw Response</h1>\n<textarea style='width:100%;height:300px;' cols='50' rows='5'>", $this->_raw_response, "</textarea>\n<hr />\n<br /><br />\n";
        echo "<h1>Full Response</h1>\n<pre>", var_export( $this->_full_response, true ), "</pre>\n<hr />\n<br /><br />\n";
        echo "<h1>Response</h1>\n<pre>", var_export( $this->_response, true ), "</pre>\n<hr />\n<br /><br />\n";
    }

	/**
	 * This sends sends the actual call to the API Server and parses the response
	 *
	 * @param string $method The method being called
	 * @param mixed $params an array of the parameters to be sent
     * @return stdClass object
	 */
	protected function _execute( $method, $params = array() ) {
        // Set the method
        $this->_method = $method;

        // Do the call and get the response
        $this->_full_response = $this->ashley->$method( $params );

        // Set Request Parameters
		$this->_request_headers = $this->ashley->__getLastRequestHeaders();
		$this->_request = $this->ashley->__getLastRequest();

        // Set Response Parameters
        $this->_response_headers = $this->ashley->__getLastResponseHeaders();
		$this->_raw_response = $this->ashley->__getLastResponse();

        $result = $method . 'Result';

        $this->_response = $this->_full_response->$result;

        $this->_success = 'Failure' != $this->_response->Acknowledge;
        $this->_message = $this->_response->Message;

        // If we're debugging lets give as much info as possible
        if ( self::DEBUG )
            $this->debug();
	}
}