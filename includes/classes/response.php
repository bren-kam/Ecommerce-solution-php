<?php
/**
 * Handles standard response
 *
 * @package Studio98 Framework
 * @since 1.0
 */
class Response {
    /**
     * Variables
     */
    protected $_success;
    protected $_message;
    protected $_error_code;
    protected $_data;

	/**
	 * Construct initializes data
	 *
	 * @param bool $success
     * @param string $message
     * @param int $error_code
	 */
	public function __construct( $success, $message = '', $error_code = 0 ) {
	    $this->_success = $success;
        $this->_message = $message;
        $this->_error_code = $error_code;
	}

    /**
     * Success of response
     *
     * @return bool
     */
    public function success() {
        return $this->_success;
    }

	/**
     * Success of response
     *
     * @return string
     */
    public function message() {
        return $this->_message;
    }

	/**
     * The error code
     *
     * @return int
     */
    public function error_code() {
        return $this->_error_code;
    }

    /**
	 * Add Response
	 *
	 * @param string $key
	 * @param string $value
	 */
	public function add( $key, $value ) {
		// Set the variable
		$this->_data[$key] = $value;
	}

    /**
     * Get Response
     *
     * @param string $key
     * @return mixed
     */
    public function get( $key ) {
        return ( isset( $this->_data[$key] ) ) ? $this->_data[$key] : false;
    }
	
	/**
	 * Exception
	 *
     * @param FacebookApiException $exception
	 * @return Response
	 */
	public static function fb_exception( $exception ) {
		$result = $exception->getResult();
		
		return new Response( false, $result['error']['message'], $result['error']['code'] );
	}
}