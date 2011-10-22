<?php
/**
 * BuyCart Authorize.net AIM Payment Gateway
 *
 * Used as a class for the Authorize.net Payment gateway. Requires CurlSSL lib
 *
 * @package BuyCart
 * @since 1.0.0
 */

class AIM {
	/**
	 * Whether or not you are testing
	 *
	 * @since 1.0.0
	 * @var bool
	 * @access public
	 */
	 public $testing = false;

	/**
	 * The email receipt
	 *
	 * @since 1.0.0
	 * @var bool
	 * @access public
	 */
	 public $email_receipt = '';

	 /**
	 * Post URL to call CURL function
	 *
	 * @since 1.0.0
	 * @var string
	 * @access private
	 */
	 private $post_url  = 'https://secure.authorize.net/gateway/transact.dll';

	 /**
	 * Test URL to call CURL function
	 *
	 * @since 1.0.0
	 * @var string
	 * @access private
	 */
	 private $post_url_test = 'https://test.authorize.net/gateway/transact.dll';
	 
	 /**
	 * An array of the values to be submitted
	 *
	 * @since 1.0.0
	 * @var array
	 * @access private
	 */
	 private $post_values = array(
		// the API Login ID and Transaction Key must be replaced with valid values
		'x_login'					=> '',
		'x_tran_key'				=> '',

		'x_version'					=> '',
		'x_delim_data'				=> '',
		'x_delim_char'				=> '',
		'x_relay_response'			=> '',

		'x_description'				=> '',
		'x_type'					=> '',
		'x_method'					=> '',

		'x_email_customer' 			=> '',
		'x_merchant_email'			=> '',
		'x_header_email_receipt'	=> '',
		'x_footer_email_receipt'	=> '',

		// Everything below here needs to be set
		'x_card_num'				=> '',
		'x_exp_date'				=> '',
		'x_card_code'				=> '',

		'x_amount'					=> '',

		'x_first_name'				=> '',
		'x_last_name'				=> '',
		'x_address'					=> '',
		'x_city'					=> '',
		'x_state'					=> '',
		'x_zip'						=> '',

		'x_ship_to_first_name'		=> '',
		'x_ship_to_last_name'		=> '',
		'x_ship_to_address'			=> '',
		'x_ship_to_city'			=> '',
		'x_ship_to_zip'				=> '',

		'x_email'					=> '',
		
		'x_invoice_num'				=> '' // This will be the order number
		// Additional fields can be added here as outlined in the AIM integration
		// guide at: http://developer.authorize.net
	);

	/**
	 * An array that will hold the response values
	 *
	 * @since 1.0.0
	 * @var array
	 * @access private
	 */
	 private $post_response = array();

	/**
	 * PHP5 Constructor
	 * 
	 * Sets some of the base variables to be sent
	 *
	 * @since 1.0.0
	 */
	function __construct( $testing = false ) {
		// Set post values
		$this->post_values['x_login']			= '3FpJ4368uH';
		$this->post_values['x_tran_key']		= '3H468AhaTR3pzq3W';
		$this->post_values['x_version'] 		= '3.1';
		$this->post_values['x_delim_data'] 		= 'TRUE';
		$this->post_values['x_delim_char'] 		= '|';
		$this->post_values['x_relay_response'] 	= 'FALSE';
		$this->post_values['x_type'] 			= 'AUTH_CAPTURE';
		$this->post_values['x_method'] 			= 'CC';
		$this->post_values['x_email_customer'] 	= 'TRUE';
		$this->post_values['x_description']		= 'Imagine Retailer - Managed Website Order';

		// If it's a test, set the right data
		if ( $testing ) {
			$this->post_values['x_login']			= '54PB5egZ';
			$this->post_values['x_tran_key']		= '48V258vr55AE8tcg';
			$this->post_values['x_test_request'] 	= 'TRUE';
			$this->testing = true;
		} else {
			$this->post_values['x_merchant_email']	= $this->email_receipt;
			$this->post_values['x_test_request'] 	= 'FALSE';
		}
	}

	/**
	 * Set's all the payment data variables in the post values based on Session data and passed array
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var array $cc_info array of the credit card information
	 * @var float $amount the amount to set the data for
	 * @var int $order_id the order id (invoice number) of the order
	 */
	public function set_parameter( $key, $value ) { 
		if ( array_key_exists( $key, $this->post_values ) ) {
			$this->post_values[$key] = $value;
			return true;
		}
		
		return false;
	}

	/**
	 * Sends the payment information to AUTHORIZE.NET using the AIM service
	 *
	 * Grabs data in post_values and runs fixes each one in a loop and converts into
	 * a string.
	 *
	 * Requires CurlSSL
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return bool
	 */
	public function send_payment() { 
		// Set the post url (different for testing)
		$post_url = ( $this->testing ) ? $this->post_url_test : $this->post_url;

		// Makes string out of array ready to post
		foreach ( $this->post_values as $key => $value ) { 
			$post_string .= "$key=" . urlencode( $value ) . "&";
		}
		$post_string = rtrim( $post_string, "& " );
		//echo $post_string;
		
		if ( empty( $post_string ) )
			return false;

		$ch = curl_init( $post_url ); // initiate curl object
		curl_setopt( $ch, CURLOPT_HEADER, 0 ); // set to 0 to eliminate header info from response
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); // Returns response data instead of TRUE(1)
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_string ); // use HTTP POST to send form data
		//curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE ); // uncomment this line if you get no gateway response.
		$post_response = curl_exec( $ch ); // execute curl post and store results in $this->post_response

		// additional options may be required depending upon your server configuration
		curl_close ( $ch ); // close curl object

		// This line takes the response and breaks it into an array using the specified delimiting character
		$this->post_response = explode( $this->post_values["x_delim_char"], $post_response );

		return ( $this->post_response[0] == '1' ) ? true : false;
	}

	/**
	 * Returns the response message if asked
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @returns string the response of processing payment
	 */
	public function payment_response() {
		return ( !empty( $this->post_response[3] ) ) ? $this->post_response[3] : false;
	}
}
?>