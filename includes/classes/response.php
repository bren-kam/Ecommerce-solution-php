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
    private $success;
    private $message;
    private $error_code;
    private $data;

	/**
	 * Construct initializes data
	 *
	 * @param bool $success
     * @param string $message
     * @param int $error_code
	 */
	public function __construct( $success, $message = '', $error_code = 0 ) {
	    $this->success = $success;
        $this->message = $message;
        $this->error_code = $error_code;
	}

    /**
     * Success of response
     *
     * @return bool
     */
    public function success() {
        return $this->success;
    }

	/**
     * Success of response
     *
     * @return string
     */
    public function message() {
        return $this->message;
    }

	/**
     * The error code
     *
     * @return int
     */
    public function error_code() {
        return $this->error_code;
    }

    /**
	 * Add Response
	 *
	 * @param string $key
	 * @param string $value
	 */
	public function add( $key, $value ) {
		// Set the variable
		$this->data[$key] = $value;
	}

    /**
     * Get Response
     *
     * @param string $key
     * @return mixed
     */
    public function get_response( $key ) {
        return ( isset( $this->data[$key] ) ) ? $this->data[$key] : false;
    }
}
