<?php
/**
 * Function (fn) class, the random functions go here
 *
 * Functions:
 * info( $object ) - shows the information about an object
 *
 * @package Studio98 Framework
 * @since 1.0
 */

if ( !isset( $s98_cache ) ) {
	global $s98_cache;
	$s98_cache = new Base_Cache();
}

class fn extends Base_Class {
	/**
	 * Shows all the information about an object
	 *
	 * @since 1.0
	 *
	 * @param mixed $object the object to get information about
	 * @param bool $echo (optional) whether to echo the information or not
	 * @return array
	 */
	public static function info( $object, $echo = true ) {
		$info = '<pre>' . var_export( $object, true ) . '</pre>';

		if ( $echo )
			echo $info;

		return $info;
	}

	/**
	 * Sends a mail message
	 *
	 * @since 1.0
	 *
	 * @param string|array $to the addresses to send it to
	 * @param string $subject the subject of the email
	 * @param string $message the subject of the email
	 * @param string $from (optional) the address that it's from. If left empty, uses defaults
	 * @param string $reply_to (optional) the reply-to information. If left empty, uses $from
     * @param bool $text (optional) whether to send text email
     * @param bool $use_html_template (optional) whether to wrap the email message with a default template
     * @param string $override_headers (optional) additional html headers for emails with attachments
	 * @return bool
	 */
    public static function mail( $to, $subject, $message, $from = '', $reply_to = '', $text = true, $use_html_template = true, $cc = null, $bcc = null, $override_headers = null )
    {
        // Find out if they passes a string or array, if they passed an array parse it
        if (is_array($to)) {
            $to_addresses = '';

            foreach ($to as $name => $email_address) {
                $to_addresses .= ",$name <$email_address>";
            }

            $to_addresses = substr($to_addresses, 1);
        } else {
            $to_addresses = $to;
        }
        $to_addresses = html_entity_decode($to_addresses);

        if (empty($from)) {
            $from = (defined('FROM_NAME')) ? FROM_NAME . ' <' . FROM_EMAIL . '>' : FROM_EMAIL;
        }
        $from = html_entity_decode($from);

        if (empty($reply_to))
            $reply_to = $from;

        $subject = html_entity_decode($subject);

        $headers = '';
        if($override_headers != null)
            $headers = $override_headers;

        if ( !$text ) {
			// Headers for HTML emails
          
                $headers .= 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
          
            if ( $use_html_template ) {
                $message = str_replace( array( '[subject]', '[message]' ), array( $subject, $message ), '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<title>[subject]</title>
				<style type="text/css">
				body { width: 800px; font-family:Arial, Helvetica, sans-serif; font-size:13px; margin: 15px auto; }
				p { line-height: 21px; padding-bottom: 7px; }
				h2{ padding:0; margin:0; }
				td{ font-size: 13px; padding-right: 10px; }
				li { padding-top: 7px; }
				</style>
				</head>
				<body>
				[message]
				</body>
				</html>' );
            }
		}
        
        $headers .= "From: $from\r\n";
        $headers .= "Reply-to: $reply_to\r\n";

        if ( $cc ) {
            $headers .= "Cc: $cc\r\n";
        }

        if ( $bcc ) {
            $headers .= "Bcc: $bcc\r\n";
        }

        // Headers for Text emails
        if ( $text ) {
            $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        }

		return mail( $to_addresses, $subject, $message, $headers);
    }

	/**
	 * Figure out what browser is used, its version and the platform it is running on.
	 *
	 * The following code was ported in part from JQuery v1.3.1
	 *
	 * @access public
     *
	 * @return array
	 */
	public static function browser() {
		// Uses caching
		global $s98_cache;

		$b = $s98_cache->get( 'browser' );
		if ( is_array( $b ) )
			return $b;

		$user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : 'undefined';

		// Identify the browser. Check Opera and Safari first in case of spoof. Let Google Chrome be identified as Safari.
		if ( preg_match( '/opera/', $user_agent ) ) {
			$name = 'Opera';
		} elseif ( preg_match( '/webkit/', $user_agent ) ) {
			$name = 'Safari';
		} elseif ( preg_match( '/msie/', $user_agent ) ) {
			$name = 'Msie';
		} elseif ( preg_match( '/mozilla/', $user_agent ) && !preg_match( '/compatible/', $user_agent ) ) {
			$name = 'Mozilla';
		} else {
			$name = 'unrecognized';
		}

		// What version?
		$version = ( preg_match( '/.+(?:firefox|it|ra|ie)[\/: ]?([\d.]+)/', $user_agent, $matches ) ) ? $matches[1] : 'unknown';

		// Running on what platform?
		if ( preg_match('/linux/', $user_agent ) ) {
			$platform = 'Linux';
		} elseif ( preg_match( '/macintosh|mac os x/', $user_agent ) ) {
			$platform = 'Mac';
		} elseif ( preg_match( '/windows|win32/', $user_agent ) ) {
			$platform = 'Windows';
		} else {
			$platform = 'unrecognized';
		}

		$b = array(
			'name'	  		=> $name,
			'version'   	=> $version,
			'platform'  	=> $platform,
			'user_agent' 	=> $user_agent
		);

		// Add it to the cache
		$s98_cache->add( 'browser', $b );

		return $b;
	}

    public static function build_html_with_attachments($html, $from, $domain) {
        preg_match_all( '~<img.*?src=.([\/.a-z0-9:_-]+).*?>~si', $html,$matches);
		
        $paths = array();
        $i = 0;
        foreach ($matches[1] as $img) {
            $img_old = $img;
            if(strpos($img, "//") === 0) {
                $img = str_replace('//', 'http://', $img);
            }

            if(strpos($img, "http://") === false) {
                $uri = parse_url($img);
                $paths[$uri['path']]['path'] = 'http://'.$domain.$uri['path'];
                $content_id = md5($img);
                $paths[$uri['path']]['cid'] = $content_id;
            } else {
                $paths[$img]['path'] = $img;
                $content_id = md5($img);

                $paths[$img]['cid'] = $content_id;
            }
                $html = str_replace($img_old,'cid:'.$content_id,$html);
        }

        $headers = "";
        $boundary = "--".md5(uniqid(time()));
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\n";
        $multipart = "";
        $multipart .= "--$boundary\n";
        $kod = 'utf-8';
        $multipart .= "Content-Type: text/html; charset=$kod\n";
        $multipart .= "Content-Transfer-Encoding: Quot-Printed\n\n";
        $multipart .= "$html\n\n";

        foreach ($paths as $path) {

            $file = file_get_contents($path['path']);
            $imagetype = substr(strrchr($path['path'], '.' ),1);



            $message_part = "";

            switch ($imagetype) {
              case 'png':
              case 'PNG':
                    $message_part .= "Content-Type: image/png";
                    break;
              case 'jpg':
              case 'jpeg':
              case 'JPG':
              case 'JPEG':
                    $message_part .= "Content-Type: image/jpeg";
                    break;
              case 'gif':
              case 'GIF':
                    $message_part .= "Content-Type: image/gif";
                    break;
            }

            $message_part .= "; file_name = \"{$path['path']}\"\n";
            $message_part .= 'Content-ID: <'.$path['cid'].">\n";
            $message_part .= "Content-Transfer-Encoding: base64\n";
            $message_part .= "Content-Disposition: inline; filename = \"".basename($path['path'])."\"\n\n";
            $message_part .= chunk_split(base64_encode($file))."\n";
            $multipart .= "--$boundary\n".$message_part."\n";
                                       
        }

        $multipart .= "--$boundary--\n";
        return array('multipart' => $multipart, 'headers' => $headers);  
    }
    
    
	/**
	 * Check value to find if it was serialized.
	 *
	 * If $data is not an string, then returned value will always be false.
	 * Serialized data is always a string.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data Value to check to see if was serialized.
	 * @return bool
	 */
	public static function is_serialized( $data ) {
		// if it isn't a string, it isn't serialized
		if ( !is_string( $data ) )
			return false;

		$data = trim( $data );

		if ( 'N;' == $data )
			return true;

		if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
			return false;

		switch ( $badions[1] ) {
			case 'a':
			case 'O':
			case 's':
				if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
					return true;
			break;

			case 'b':
			case 'i':
			case 'd':
				if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
					return true;
			break;
		}

		return false;
	}

    /**
     * Check if it's an AJAX request
     *
     * @return bool
     */
    public static function is_ajax() {
        return isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && 'xmlhttprequest' == strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] );
    }
}
