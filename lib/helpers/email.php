<?php
/**
 * Email helper
 */
class Email {
    /**
     * Variables to send out
     */
    public $to, $subject, $message;
    public $from = '';
    public $extra_headers = '';

	/**
	 * Declare a template for the email
	 * @var string
	 */
	protected $template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
     * Send
     *
     * @throws HelperException
     */
    public function send() {
        if ( !isset( $this->to, $this->subject, $this->message ) )
            throw new HelperException( 'All variables were not set' );

        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= ( empty( $this->from ) ) ? 'From: ' . TITLE . ' <info@' . DOMAIN . '>' . "\r\n" : 'From: ' . $this->from . "\r\n";

        $headers .= $this->extra_headers;

        mail( $this->to, $this->subject, str_replace( array( '[logo]', '[content]' ), array( 'logos/' . DOMAIN . '.png', $this->message ), $this->template ), $headers );
    }

	/**
	 * Fills a predefined email with data
	 *
	 * @param string $key the key to the correct email
	 * @param array $replacements
     * @param bool $html [optional]
     * @return string
	 */
	protected function fill( $key, $replacements, $html = true ) {
		if( !is_array( $replacements ) )
			return false;
		
		return str_replace( $this->email[$key]['variables'], $replacements, $this->email[$key][( $html ) ? 'html' : 'text'] );
	}
}