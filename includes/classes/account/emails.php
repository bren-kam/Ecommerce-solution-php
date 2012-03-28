<?php
/**
 * Handles standard emailing
 *
 * @package Grey Suit Retail
 * @since 1.0
 */
class Emails {	
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
}