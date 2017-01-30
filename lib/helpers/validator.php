<?php
/**
 * Created by Phoenix Development (http://www.phoenixdev.net/) (c) 02/26/08
 * Blogged at http://phoenixdev.wordpress.com/
 *
 * Class to add server-side and client-side validation
 *
 *
 * @example
 * <code[head]>
 * $validator = new Validator();
 * $validator->form_name = 'fSignUp';
 * $validator->add_validation("tEmail","req", "The \"Email Address\" field is required");
 * $validator->add_validation("tEmail","email", "The \"Email Address\" field must contain a valid email");
 * if ( !empty( $_POST)) {
 * 		$errs = $validator->validate();
 * }
 * $validator->include_js();
 * </code>
 * <code[javascriptValidation]>
 * </form>
 * <?= $validator->js_validation(); //Put directly after form ?>
 * </code>
 */

class Validator {
	/**
	 * Sets the variable for client-side validation
	 * @var bool
	 */
	 public $js		 			= true;
	 
	/**
	 * Sets whether you want the function to validate on a javascript trigger
	 * @var bool
	 */
	 public $trigger 			= false;

	/**
	 * Sets the form name variable for the Javascript validation
	 * @var string
	 */
	 private $form_name 			= '';

	/**
	 * Sets the random number to make the javascript class unique
	 * @var int
	 */
	 private $random;

	/**
	 * Sets the string that will contain all the javascript validation
	 * @var string
	 */
	 private $js_elements 		= '';

	/**
	 * Sets the string that will contain the javascript error code
	 * @var string
	 */
	 private $js_errors 		= '';

	/**
	 * Sets the array to contain the serverside validations
	 * @var array
	 */
	 private $elements 			= array();
	
	/**
	 * Sets the array of regex patterns to be used in the server-side validation
	 * @var array
	 */
	 private $patterns 			= array(
									'alnum' 		=> "/[^A-Za-z0-9\\ ]/",
									'alnumhyphen' 	=> "/[^A-Za-z0-9\\-_]/",
									'alpha'			=> "/[^A-Za-z\\ ]/",
									'author'		=> "/[^A-Za-z.\\ ]/",
									'cc' 			=> "/^(3[47]|4|5[1-5]|6011)/", // Credit card number
									'csv'			=> "/[^-a-zA-Z0-9,\\s]/", // Comma separated values
									'custom'		=> "/[^A-Za-z0-9\\042\\047\\055\\057\\ _\$.,!?()]/", // Custom Text Selection
									'date'			=> '/^[\d]{4}-[\d]{2}-[\d]{2}$/',
									'email'			=> "/^([a-zA-Z0-9_\\-\\.]+)@((\\[[0-9]{1,3}\\.[0-9]{1,3}\\.[0-9]{1,3}\\.)|(([a-zA-Z0-9\\-]+\\.)+))([a-zA-Z]{2,10}|[0-9]{1,3})(\\]?)\$/",
									'float'			=> "/[^0-9\\.]/",
									'img'			=> "/^[0-9A-Za-z_ \\-]+(.[jJ][pP][gG]|.[jJ][pP][eE][gG]|.[gG][iI][fF]|.[pP][nN][gG])\$/",
									'ip'			=> "/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/",
									'num'			=> "/[^0-9]/",
									'phone'			=> "/[^0-9\\- ()]/",
									'URL'			=> "/(([\w]+:)?\\/\\/)?(([\\d\\w]|%[a-fA-f\\d]{2,2})+(:([\\d\\w]|%[a-fA-f\\d]{2,2})+)?@)?([\\d\\w][-\\d\\w]{0,253}[\\d\\w]\\.)+[\\w]{2,4}(:[\\d]+)?(\\/([-+_~.\\d\\w]|%[a-fA-f\\d]{2,2})*)*(\\?(&?([-+_~.\\d\\w]|%[a-fA-f\\d]{2,2})=?)*)?(#([-+_~.\\d\\w]|%[a-fA-f\\d]{2,2})*)?/",
									'zip'			=> "/[^-0-9]/"
								);
 
	/**
	 * PHP5 Constructor
	 * Includes Javascript
     *
     * @param string $form_name
	 */
	function __construct( $form_name ) {
        $this->form_name = $form_name;
		$this->random = mt_rand(0, 1000);
	}
	
	/**
	 * Add a single validation to list
	 * @access public
	 * @param string $element_name the name of the form element
	 * @param string $descriptor the name of the validation to be applied
	 * @param string $err the text of the error string (optional)
	 * @return bool
	 */
	 public function add_validation($element_name, $descriptor, $err = "", $server_side = true ) {
		if ( $server_side )
	 		$this->elements[] = array($element_name, $descriptor, $err);
		
		if ( $this->js )
			$this->js_elements .= 'fv' . $this->random . '.addValidation("' . $element_name . '","' . $descriptor . '", "' . addslashes($err) . '");';
		return true;
	 }
	 
	 /**
	 * Perform Server-Side Validation
	 * @access public
	 * @return string
	 */
	public function validate() {
		$error_string = '';

		foreach ( $this->elements as $element ) {
			$error_string .= $this->check_validation( $element[0], $element[1], $element[2] );
		}

		return $error_string;
	 }
	 
	/**
	 * Returns HTML to include javascript validator
	 * @access public
	 * @return string
	 */
	 public function include_js() {
		return ( $this->js ) ? '<script type="text/javascript" language="javascript" src="' . $this->js_path . '"></script>' : '';
	 }

	/**
	 * Adds code to highlight textbox when there is an error
	 * @param string $element_name the name of the form element
	 * @access private
	 * @return bool
	 */
	 private function highlight_error($element_name) {
	 	$this->js_errors .= "document." . $this->form_name . "." . $element_name . ".style.borderColor = '#FF0000';";
		return true;
	 }
	
	/**
	 * Returns HTML to include javascript after form
	 * @access public
	 * @return string
	 */
	 public function js_validation() {
		if ( $this->js ) {
			$start = '<script type="text/javascript" language="javascript">head.load( "/resources/js_single/?f=validator", function() {';
			$trigger = ($this->trigger) ? ', true' : '';
			$start .= ($this->form_name != 'document.forms[0].name') ? 'var fv' . $this->random . '=new Validator("' . $this->form_name . '"' . $trigger . ');document.' . $this->form_name . '.validator="fv' . $this->random . '";' : 'var fv' . $this->random . '=new Validator(' . $this->form_name . $trigger . ');document.forms[0].validator="fv' . $this->random . '";' ;
			$end = "});</script>\n";
			return $start . $this->js_elements . $this->js_errors . $end;
		}
	 }
	 
	/**
	 * Checks a single validation element and returns an error string if it failed
	 * @access public
	 * @param string $element_name the name of the form element
	 * @param string $descriptor the name of the validation to be applied
	 * @param string $err the text of the error string (optional)
	 * 
	 */
	 
	 public function call_validation($element_name, $descriptor, $err = "") {
	    $this->elements[] = array($element_name, $descriptor, $err);
		if ($this->js) $this->js_elements .= 'fv.addValidation("' . $element_name . '","' . $descriptor . '", "' . addslashes($this->check_validation($element_name, $descriptor, $err)) . '");';
		return true;
	 }
	
	/**
	 * Checks a single validation element and returns an error string if it failed
	 * @access private
	 * @param string $element_name the name of the form element
	 * @param string $descriptor the name of the validation to be applied
	 * @param string $err the text of the error string (optional)
	 * @return string
	 */
	 private function check_validation( $element_name, $descriptor, $err = '' ) {
	 	$descriptor_array = explode( '=', $descriptor );

		$cmd = $descriptor_array[0];
		
		// If it exists, set it
		if ( isset( $descriptor_array[1] ) )
			$command_value = $descriptor_array[1];
		
		$t = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		
		switch ( $cmd ) {
			case 'alnum':
			case 'alphanumeric':
				if ( !empty( $_POST[$element_name] ) && preg_match( $this->patterns['alnum'], $_POST[$element_name] ) > 0 ) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " must be alpha-numeric.\n<br />" : $err . ".\n<br />";
				}
			break;

			case 'alnumhyphen':
				if ( !empty( $_POST[$element_name] ) && preg_match( $this->patterns['alnumhyphen'], $_POST[$element_name] ) > 0 ) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " may only contain alpha-numeric, hyphen and underscore characters.\n<br />" : $err . ".\n<br />";
				}
			break;

			case 'alphabetic': 
			case 'alpha':
				if ( !empty( $_POST[$element_name] ) && preg_match( $this->patterns['alpha'], $_POST[$element_name] ) > 0 ) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " may only contain letters (no symbols or numbers).\n<br />" : $err . ".\n<br />";
				}
			break;

			case 'author':
				if ( !empty( $_POST[$element_name] ) && preg_match( $this->patterns['author'], $_POST[$element_name] ) > 0 ) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " may only contain letters and a period.\n<br />" : $err . ".\n<br />";
				}
			break;

			case 'cc':
			case 'credit':
			case 'creditcard':
				// Credit Card error message
				$cc_error = "The credit card number you entered is not valid. Please try again.\n<br />";

				// Get object value
				$obj_value = preg_replace( '/\\D/', '', $_POST[$element_name] );
	
				// Get the length of the object
				$obj_length = strlen( $obj_value );
	
				// Get the first number
				$first_num = $obj_value[0];

				// Make sure it's valid credit card type
				if ( 0 != preg_match( $this->patterns['cc'], $obj_value, $matches ) ) {
					switch ( $first_num ) {
						// AmEx
						case '3':
							if ( 15 != $obj_length )
								return $cc_error;
							break;
	
						// Visa
						case '4':
							if ( 13 != $obj_length  && 16 != $obj_length )
								return $cc_error;
							break;
	
						// MasterCard
						case '5':
							if ( 16 != $obj_length )
								return $cc_error;
							break;
	
						// Discover
						case '6':
							if ( 16 != $obj_length )
								return $cc_error;
							break;
					}
					
					if ( !$this->luhn_check( $obj_value ) )
						return $cc_error;
				} else {
					return $cc_error;
				}
			break;

			case 'csv': 
				if ( !empty( $_POST[$element_name] ) && preg_match( $this->patterns['csv'], $_POST[$element_name] ) > 0 ) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " must only contain alpha-numeric characters, '-' and '_', separated by commas.\n<br />" : $err . ".\n<br />";
				}
			break;
			
			case 'custom': 
				if ( !empty( $_POST[$element_name] ) && preg_match( $this->patterns['custom'], $_POST[$element_name] ) > 0 ) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " must only contain alpha-numeric, {'}, {,}, {.}, {&}, {/}, {-}, {\"}, {?}, {(}, {)}, {_} and {!} characters.\n<br />" : $err . ".\n<br />";
				}
			break;

			case 'date': 
				if ( !empty( $_POST[$element_name] ) && preg_match( $this->patterns['date'], $_POST[$element_name] ) == 0 ) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " must contain a valid date.\n<br />" : $err . ".\n<br />";
				}
			break;

			case 'email':
				if ( !empty( $_POST[$element_name] ) && preg_match( $this->patterns['email'], $_POST[$element_name] ) == 0 ) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " must contain a valid email address.\n<br />" : $err . ".\n<br />";
				}
			break;

			case 'extension': 
			case 'ext':
				$accept = explode( '|', $command_value );
				$file = explode( '.', $_POST[$element_name] . split('.') );
				$ext = strtolower( $file[count( $file ) - 1] );
				
				if ( false == array_search( $ext, $accept ) ) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " may only contain the following file types: " . implode(", ", $accept) . ".\n<br />" : $err . ".\n<br />";
				}
			break;

			case 'float':
				if ( !empty( $_POST[$element_name] ) && preg_match( $this->patterns['float'], $_POST[$element_name] ) > 0 ) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " may only contain numbers and a period.\n<br />" : $err . ".\n<br />";
				}
			break;

			case 'gt': 
			case 'greaterthan': 
				if ( preg_match( $patterns['num'], $_POST[$element_name] ) > 0 && !empty( $_POST[$element_name] ) ) { 
					$this->highlight_error( $element_name );
					if ( $_POST[$element_name] <= $command_value ) return ( empty( $err ) ) ? $element_name . " must contain a number greater than " . $command_value . ".\n<br />" : $err . ".\n<br />";
				} else {
					if ( empty( $_POST[$element_name] ) ) {
						$this->highlight_error( $element_name );
						return $element_name . " must be numeric.\n<br />";
					}
				}
			break;

			case 'image':
			case 'img':
				$file = explode( '\\', $_POST[$element_name] );
				$img = $file[count( $file ) - 1];
				if ( !empty( $_POST[$element_name] ) && preg_match( $this->patterns['img'], $_POST[$element_name] ) == 0 ) {
					return ( empty( $err ) ) ? $element_name . " may only hold an image with extensions jpg, jpeg, gif and png.\n<br />" : $err . ".\n<br />";
					$this->highlight_error( $element_name );
				}
			break;

            case 'ip':
				if ( !empty( $_POST[$element_name] ) && preg_match( $this->patterns['ip'], $_POST[$element_name] ) > 0) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " must be a valid IP Address.\n<br />" : $err . ".\n<br />";
				}
            break;

			case 'lt': 
			case 'lessthan':
				if ( preg_match( $patterns['num'], $_POST[$element_name] ) > 0 && !empty( $_POST[$element_name] ) ) { 
					$this->highlight_error( $element_name );
					if ( $_POST[$element_name] >= $command_value ) return ( empty( $err ) ) ? $element_name . " must contain a number less than " . $command_value . ".\n<br />" : $err . ".\n<br />";
				} else {
					if (empty($_POST[$element_name] )) {
						$this->highlight_error( $element_name );
						return $element_name . " must contain a number.\n<br />" ;
					}
				}
			break;

			case 'match':
				list( $element1, $element2 ) = explode( '|', $element_name );
				
				if ( $_POST[$element1] != $_POST[$element2] ) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element1 . " and " . $element2 . " must match.\n<br />" : $err . ".\n<br />";
				}
			break;

			case 'maxlength': 
			case 'maxlen': 
				if ( strlen( $_POST[$element_name] ) > $command_value ) { 
					$this->highlight_error( $element_name );
					$return_value = ( empty( $err ) ) ? $element_name . " may not be longer than " . $command_value . " characters" : $err;
					return $return_value . ".<br />$t" . "[Current length = " . strlen( $_POST[$element_name] ) . "]<br />\n";
				}
			break;

			case 'maxwordlength': 
			case 'maxwordlen': 
				$words = explode(" ", $_POST[$element_name] );
				if ( count( $words ) > $command_value ) {
					$this->highlight_error( $element_name );
					$return_value = ( empty( $err ) ) ? $element_name . " may not be longer than " . $command_value . " words" : $err;
					return $return_value . ".<br />$t" . "[Current length = " . count( $words ) . "]<br />\n";
				}
			break;

			case 'minlength': 
			case 'minlen': 
				if ( strlen( $_POST[$element_name] ) < $command_value ) {
					$this->highlight_error( $element_name );
					$return_value = ( empty( $err ) ) ? $element_name . " may not be shorter than " . $command_value . " characters" : $err;
					return $return_value . ".<br />$t" . "[Current length = " . strlen( $_POST[$element_name] ) . "]<br />\n";
				}
			break;

			case 'minwordlength': 
			case 'minwordlen': 
				$words = explode( ' ', $_POST[$element_name] );
				if ( count( $words ) < $command_value ) {
					$this->highlight_error( $element_name );
					$return_value = ( empty( $err ) ) ? $element_name . " may not be shorter than " . $command_value . " words" : $err;
					return $return_value . ".<br />$t" . "[Current length = " . count( $words ) . "]<br />\n";
				}
			break;

			case 'num': 
			case 'numeric': 
				if ( !empty( $_POST[$element_name] ) && preg_match( $this->patterns['num'], $_POST[$element_name] ) > 0) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " must be a number.\n<br />" : $err . ".\n<br />";
				}
			break;

			case 'phone':
				if ( !empty( $_POST[$element_name] ) && preg_match( $this->patterns['phone'], $_POST[$element_name] ) > 0) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " must contain a valid phone number.\n<br />" : $err . ".\n<br />";
				}
			break;

			case 'regexp': 
				if ( !empty( $_POST[$element_name] ) && !preg_match( '/' . $command_value . '/', $_POST[$element_name] ) > 0) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " contains invalid characters.\n<br />" : $err . ".\n<br />";
				}
			break;

			case 'req': 
			case 'required': 
				if ( empty( $_POST[$element_name] ) && '0' !== $_POST[$element_name] ) { 
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " is a required field.\n<br />" : $err . ".\n<br />";
				}
			break;

			case 'URL': 
				if ( !empty( $_POST[$element_name] ) && preg_match( $this->patterns['URL'], $_POST[$element_name] ) == 0) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " must contain a valid URL.\n<br />" : $err . ".\n<br />";
				}
				break;

			case 'val': 
				if ( $_POST[$element_name] != $command_value && !empty( $_POST[$element_name] ) ) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " must contain the follow value " . $command_value . "\n<br />" : $err . ".\n<br />";
				}
			break;
			
			case '!val': 
				if ( $_POST[$element_name] == $command_value && !empty( $_POST[$element_name] ) ) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " must not contain the follow value " . $command_value . "\n<br />" : $err . ".\n<br />";
				}
			break;

			case 'zip': 
				if ( !empty( $_POST[$element_name] ) && preg_match( $this->patterns['zip'], $_POST[$element_name] ) > 0) {
					$this->highlight_error( $element_name );
					return ( empty( $err ) ) ? $element_name . " must contain a valid zip code.\n<br />" : $err . ".\n<br />";
				}
			break;

				default: break;
			}
	 }
	 
	 /* Luhn algorithm number checker - (c) 2005-2008 - planzero.org           *
	 * This code has been released into the public domain, however please      *
	 * give credit to the original author where possible.                      */
	 private function luhn_check( $number ) {

		// Strip any non-digits (useful for credit card numbers with spaces and hyphens)
		$number = preg_replace( '/\D/', '', $number );
	
		// Set the string length and parity
		$number_length = strlen( $number );
		$parity = $number_length % 2;
		
		// Loop through each digit and do the maths
		$total = 0;
		for ( $i = 0; $i < $number_length; $i++ ) {
			$digit = $number[$i];

			// Multiply alternate digits by two
			if ( $i % 2 == $parity ) {
				$digit *= 2;
			
				// If the sum is two digits, add them together (in effect)
				if ( $digit > 9 )
					$digit -= 9;
			}

			// Total up the digits
			$total += $digit;
		}
		
		// If the total mod 10 equals 0, the number is valid
		return ( $total % 10 == 0 ) ? true : false;
	}
}