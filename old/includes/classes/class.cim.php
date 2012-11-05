<?php
/**
 * Authorize.net CIM (Customer Information Manager) API
 *
 * Used as a class for the Authorize.net CIM API. Handles profile creation and so on.
 */

class CIM {
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
	 private $post_url  = 'https://apitest.authorize.net/xml/v1/request.api';

	/**
	 * Test URL to call CURL function
	 *
	 * @since 1.0.0
	 * @var string
	 * @access private
	 */
	 private $post_url_test = 'https://api.authorize.net/xml/v1/request.api';
	 
	/**
	 * An array of the values to be submitted
	 *
	 * @since 1.0.0
	 * @var array
	 * @access private
	 */
	 private $post_values = array();
	 
	 /**
	 * An array of the values to be submitted
	 *
	 * @since 1.0.0
	 * @var array
	 * @access private
	 */
	private $createCustomerProfileRequest = array(
		'refId' => '', 							// Optional | 20
		'profile' => array( 					// Requires: merchantCustomerId|description|email
			'merchantCustomerId' => '',			// Conditional | 20
			'description' => '', 				// Conditional | 255
			'email' => '', 						// Conditional | 255 
			'paymentProfiles' => array(			// Optional
				'customerType' => '',			// Optional		
				'billTo' => array (
					'firstName' => '',			// Optional | 50 (no symbols)
					'lastName' => '',			// Optional | 50 (no symbols)
					'company' => '',			// Optional | 50 (no symbols)
					'address' => '',			// Optional | 60 (no symbols)
					'city' => '',				// Optional | 40 (no symbols)
					'state' => '',				// Optional | A valid two-characer state code
					'zip' => '',				// Optional | 20 (no symbols)
					'country' => '',			// Optional | 60 (no symbols)
					'phoneNumber' => '',		// Optional | 25 (no letters)
					'faxNumber' => ''			// Optional | 25 (no letters)
				),
				'payment' => array(				// Can contain creditCard or bankAccount
					'creditCard' => array(
						'cardNumber' => '',		// 13-16 digits
						'expirationDate' => '',	// YYYY-MM
						'cardCode' => ''		// Optional | 3-4 digits (CCV/CVV)
					),
					'bankAccount' => array(
						'accountType' => '',	// Optional | checkings|savings|businessChecking
						'routingNumber' => '',	// 9 digits
						'accountNumber' => '',	// 5-17 digits
						'nameOnAccount' => '',	// up to 22
						'echeckType'	=> '',	// Optional | CCD|PPD|TEL|WEB
						'bankName'		=> ''
						
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
		$this->post_values['x_login']			= '';
		$this->post_values['x_tran_key']		= '';
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
	 * Adds authentication variables
	 *
	 * @access private
	 *
	 * @param string $api_login_id a unique login key provided by Authorize.net
	 * @param string $transaction_key a unique transaction key provided by Authorize.net
	 * @returns bool
	 */
	private function authentication( $api_login_id, $transaction_key ) {
		if ( !empty( $api_login_id ) && !empty( $transaction_key ) ) {
			$this->post_values['merchant_authentication'] = "<merchantAuthentication><name>$api_login_id</name><transactionKey>$transaction_key</transactionKey></merchantAuthentication>";
			return true;
		}
		
		return false;
	}
	
	/**
	 * Create Customer Profile
	 *
	 * This function is used to create a new customer profile along with any customer payment profiles 
	 * and customer shipping addresses for the customer profile. 
	 * 
	 * The following table lists the input elements for executing an API call to the 
	 * createCustomerProfileRequest function. 
	 *
	 * @param array $data an associative array of all the options
	 * @param string $transaction_key a unique transaction key provided by Authorize.net
	 * @returns bool
	 */
	public function create_customer_profile( $data ) {
		$request = '<?xml version="1.0" encoding="utf-8"?> 
<createCustomerProfileRequest xmlns="AnetApi/xml/v1/schema/AnetApiSchema.xsd">';
		$request .= $this->post_values['merchant_authentication'];
		
		if ( is_array( $data['refId'] ) )
		$request .= '<refID>' . $data['refID']

	   <profile> 
     <merchantCustomerId>Merchant Customer ID    
      here</merchantCustomerId> 
     <description>Profile description here</description> 
     <email>customer profile email address here</email> 
     <paymentProfiles> 
		   <customerType>individual</customerType> 
           <payment> 
              <creditCard> 
                 <cardNumber>Credit card number here</cardNumber> 
                 <expirationDate>Credit card expiration date   
                   here</expirationDate> 
              </creditCard> 
           </payment> 
      </paymentProfiles> 
    </profile> 
  <validationMode>liveMode</validationMode> 
  </createCustomerProfileRequest>
		return false;
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
	public function set_payment_data( $cc_info, $amount, $order_id ) { 
		// The amount to be processed
		$this->post_values['x_amount'] = $amount;

		// Credit card number
		$this->post_values['x_card_num'] = $cc_info['number'];

		// Expiration Date, MMYYYY format
		$this->post_values['x_exp_date'] = $cc_info['expiration_month'] . $cc_info['expiration_year'];

		// The CVV
		$this->post_values['x_card_code'] = $cc_info['cvv'];

		// Billing information
		$this->post_values['x_first_name'] = $_SESSION['billing_first_name'];
		$this->post_values['x_last_name'] = $_SESSION['billing_last_name'];
		$this->post_values['x_address'] = $_SESSION['billing_address1'];
		$this->post_values['x_city'] = $_SESSION['billing_city'];
		$this->post_values['x_state'] = $_SESSION['billing_state'];
		$this->post_values['x_zip'] = $_SESSION['billing_zip'];

		// Shipping information
		$this->post_values['x_ship_to_first_name'] = $_SESSION['shipping_first_name'];
		$this->post_values['x_ship_to_last_name'] = $_SESSION['shipping_last_name'];
		$this->post_values['x_ship_to_address'] = $_SESSION['shipping_address1'];
		$this->post_values['x_ship_to_city'] = $_SESSION['shipping_city'];
		$this->post_values['x_ship_to_state'] = $_SESSION['shipping_state'];
		$this->post_values['x_ship_to_zip'] = $_SESSION['shipping_zip'];
		
		// Email for receipt
		$this->post_values['x_email'] = $_SESSION['email'];
		
		// Invoice Number (Order ID)
		$this->post_values['x_invoice_num'] = $order_id;
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
	 * @var array $cc_info holds all the credit card information
	 * @var float $amount holds the price of the order
	 * @var int $order_id the order_id (invoice number) of the order
	 * @return bool
	 */
	public function send_payment( $cc_info, $amount, $order_id ) { 
		// Set payment data
		$this->set_payment_data( $cc_info, $amount, $order_id );

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