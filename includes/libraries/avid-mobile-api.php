<?php
/**
 * Avid Mobile (Mobile Marketing) - API Class
 *
 * This handles all mobile marketing API Calls
 *
 * @version 1.0.0
 */
class Avid_Mobile_API {
	/**
	 * Constant paths to include files
	 */
	const URL_API = 'https://login.avidmobile.com/MCSOAP2.1/MarketingCenter2-1.php?wsdl';
	const DEBUG = false;

    /**
     * Constant API Operations
     */
    const OPERATION_PUT = 'Put';
    const OPERATION_GET = 'Get';

    /**
     * Group Constants
     */
    const GROUP_STATIC = 'STATIC';
    const GROUP_DYNAMIC = 'DYNAMIC';

	/**
	 * A few variables that will determine the basic status
	 */
	protected $api;
	protected $success = false;
	protected $error_code = 0;
    protected $error_message = '';
    protected $error_details = '';
    protected $data = array();

    /**
     * Holds the objects for each group
     */
    public $keywords = NULL;
    public $members = NULL;
    public $optouts = NULL;
    public $groups = NULL;

	/**
	 * Construct class will initiate and run everything
	 *
	 * @param int $customer_id
	 */
	public function __construct( $customer_id, $username, $password ) {
		// Do we need to debug
		if ( self::DEBUG )
			error_reporting( E_ALL );

		// Load Avid mobile SOAP Client
		library( 'MCSOAPClient' );
		
		// Setup API
		$this->api = new AvidMobileSOAPClient( self::URL_API, $username, $password, $customer_id );
	}

    /**
     * Get Error Code
     *
     * @return int
     */
    public function get_error_code() {
        return $this->error_code;
    }

    /**
     * Get Error Message
     *
     * @return string
     */
    public function get_error_message() {
        return $this->error_message;
    }

    /**
     * Get Error Details
     *
     * @return string
     */
    public function get_error_details() {
        return $this->error_details;
    }

    /**
     * Get Data
     *
     * @return string
     */
    public function get_data() {
        return $this->data;
    }

    /**
     * Get Success
     *
     * @return bool
     */
    public function success() {
        return $this->success;
    }

    /**
	 * Forms the arguments array
	 *
	 * @access private
	 *
     * @param array $values
     * @return mixed
	 */
	protected function _arguments( array $values ) {
        // Format the arguments
        $arguments = array();

        // Loop through the values
        foreach ( $values as $key => $value ) {
            // Only want actual values, or else we can skip it
            if ( empty( $value ) )
                continue;
            
            $arguments[] = array( 'Key' => $key, 'Value' => $value );
        }

        // Return the arguments
        return $arguments;
	}

	/**
	 * This sends sends the actual call to the API Server and parses the response
	 *
	 * @access private
	 *
     * @param string $operation ('Put' or 'Get')
	 * @param string $method The method being called
	 * @param array $arguments (optional|array) an array of the parameters to be sent
     * @return mixed
	 */
	protected function _execute( $operation, $method, $arguments = NULL ) {
        // Format the arguments properly
        if ( is_array( $arguments ) )
            $arguments = $this->_arguments( $arguments );
		
        // Do the request
		$response = $this->api->DoWebService( $operation, $this->api->CreateWebServiceParams( $method, $arguments ) );
		
		fn::info( $response );
        if ( self::OPERATION_PUT == $operation || !isset( $response->Status ) ) {
            // Mark the response
            $this->error_code = $response->ErrorCode;
            $this->error_message = $response->ErrorString;
            $this->error_details = $return = $response->ErrorDetails;
        } else {
            // Mark the response
            $this->error_code = $response->Status->ErrorCode;
            $this->error_message = $response->Status->ErrorString;
            $this->error_details = $response->Status->ErrorDetails;

            $this->data = $return = $response->Data;
        }

        // Mark the success
		if ( $this->error_code ) {
            // We failed :(
			$this->success = false;

            // Return false
            return false;
        }
		
		// We succeeded! :)
        $this->success = true;
        
        // Return whatever we are supposed to return
		return $return;
	}
}