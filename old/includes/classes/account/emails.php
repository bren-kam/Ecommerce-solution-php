<?php
/**
 * Handles standard emailing
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Emails extends Base_Class {	
	/**
	 * Declare a template for the email
	 * @var string
	 */
	public static $template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Website Manager Message</title>
<style type="text/css">
body { width: 800px; font-family:Arial, Helvetica, sans-serif; color:#616268; font-size:13px; margin: 15px auto; }
p { line-height: 21px; padding-bottom: 7px; }
h2{ padding:0; margin:0; }
td{ font-size: 13px; padding-right: 10px; }
li { padding-top: 7px; }
</style>
</head>
<body>
<img src="http://www.imagineretailer.com/images/[logo]" width="314" height="48" alt="Website Manager" /><br />

[content]
</body>
</html>';
	
	/**
	 * Construct initializes data
	 */
	public function __construct() {
		// Need to load the parent constructor
		if( !parent::__construct() )
			return false;
		
		// Load emails
		require inc( 'emails', false );
		$this->email = $email;
	}
	
	/**
	 * Apply the template and send it out
	 *
	 * @param string $to the to email addresses as CSV
	 * @param string $subject the subject line of the email
	 * @param string $message the entire message (HTML)
	 * @param string $from the email address it's from
	 * @return bool
	 */
	public static function template( $to, $subject, $message , $from = '' , $extra_headers = '' ) {
		$logo = 'logos/' . DOMAIN . '.png';
		
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		$headers .= ( empty( $from ) ) ? 'From: ' . TITLE . ' <info@' . DOMAIN . '>' . "\r\n" : 'From: ' . $from . "\r\n";
		
		$headers .= $extra_headers;
		
		return mail( $to, $subject, str_replace( array( '[logo]', '[content]' ), array( $logo, $message ), self::$template ), $headers );
	}
	
	/**
	 * Reset password
	 *
	 * @since 1.0.0
	 *
	 * @param int $user_id
	 * @param string $name
	 * @param string $email
	 * @return bool|int
	 */
	public function reset_password( $user_id, $name, $email ) {
		$token = new Tokens();
		
		$token_hash = $token->create( $user_id, 'reset-password' );
		
		$result = fn::mail( $email, $this->email['reset-password']['subject'], $this->fill( 'reset-password', array( $name, $email, 'http://' . SUBDOMAIN . '.' . DOMAIN . "/reset-password/?uID=$user_id" . '&t=' . $token_hash ), false ), TITLE . ' <info@' . DOMAIN . '>' );
		
		// Make sure it was sent
		if( !$result )
			$this->_err( "Failed to send reset password email.\n\nError messsage: " . $m->ErrorInfo, __LINE__, __METHOD__ );
		
		return $result;
	}
	
	/**
	 * Fills a predefined email with data
	 *
	 * @param string $key the key to the correct email
	 * @param array $replacements
     * @param bool $html [optional]
     * @return string
	 */
	private function fill( $key, $replacements, $html = true ) {
		if( !is_array( $replacements ) )
			return false;
		
		return str_replace( $this->email[$key]['variables'], $replacements, $this->email[$key][( $html ) ? 'html' : 'text'] );
	}

	/**
	 * Report an error
	 *
	 * Make the parent error function a little less complicated
	 *
	 * @param string $message the error message
	 * @param int $line (optional) the line number
	 * @param string $method (optional) the class method that is being called
     * @return string
	 */
	private function _err( $message, $line = 0, $method = '' ) {
		return $this->error( $message, $line, __FILE__, dirname(__FILE__), '', __CLASS__, $method );
	}
}