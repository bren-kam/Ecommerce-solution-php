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
    const USERNAME = 'kerry@greysuitretail.com';
    const PASSWORD = '88692e6d9';
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
	protected $api = NULL;
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
	public function __construct( $customer_id ) {
		// Do we need to debug
		if ( self::DEBUG )
			error_reporting( E_ALL );

        // Load Avid mobile SOAP Client
        library( 'MCSOAPClient' );
        echo 'jer?';exit;
        // Setup API
        $this->api = new AvidMobileSOAPClient( self::URL_API, self::USERNAME, self::PASSWORD, $customer_id );
	}

	/**********************************/
	/* Start: Avid Mobile API Methods */
	/**********************************/

    /**
     * Instantiate Keywords class
     *
     * @param int $customer_id optional
     * @return object
     */
    public function keywords( $customer_id = NULL ) {
        // Load the library
        library('avid-mobile/keywords');

        // Instantiate class
        $this->keywords = new AM_Keywords();

        return $this->keywords;
    }

    /**
     * Instantiate Members class
     *
     * @param int $customer_id optional
     * @return mixed
     */
    public static function members( $customer_id = NULL ) {
        // Load the library
        library('avid-mobile/members');

        if ( isset( $this ) ) {
            $this->members = new AM_Members();
        } else {
            return new AM_Members( $customer_id );
        }
    }

    /**
     * Instantiate Groups class
     *
     * @param int $customer_id optional
     * @return mixed
     */
    public static function groups( $customer_id = NULL ) {
        // Load the library
        library('avid-mobile/groups');

        if ( isset( $this ) ) {
            $this->groups = new AM_Groups();
        } else {
            return new AM_Groups( $customer_id );
        }
    }

    /**
     * Instantiate Keywords class
     *
     * @param int $customer_id optional
     * @return mixed
     */
    public static function optouts( $customer_id = NULL ) {
        // Load the library
        library('avid-mobile/optouts');

        if ( isset( $this ) ) {
            $this->optouts = new AM_Optouts();
        } else {
            return new AM_Optouts( $customer_id );
        }
    }

	/********************************/
	/* End: Avid Mobile API Methods */
	/********************************/

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

        if ( self::OPERATION_PUT == $operation ) {
            // Mark the response
            $this->error_code = $response->ErrorCode;
            $this->error_message = $response->ErrorString;
            $this->error_details = $return = $response->ErrorDetails;
        } else {
            // Mark the response
            $this->error_code = $response->Status->ErrorCode;
            $this->error_message = $response->Status->ErrorString;
            $this->error_message = $response->Status->ErrorString;

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