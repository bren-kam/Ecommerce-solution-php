<?php
/**
 * Base Class
 *
 * Has basic information for the rest of the classes
 *
 * @package Studio98 Framework
 * @since 1.0
 */
class Base_Class {
	/**
	 * Kill execution and display HTML message with error message.
	 *
	 * Call this function complements the die() PHP function. The difference is that
	 * HTML will be displayed to the user. It is recommended to use this function
	 * only when the execution should not continue any further. It is not
	 * recommended to call this function very often and try to handle as many errors
	 * as possible siliently.
	 *
	 * @since 1.0
	 *
	 * @param string $message Error message.
	 * @param string $title Error title.
	 * @param string|array $args Optional arguements to control behaviour.
	 */
	public function _die( $message, $title = '', $args = array() ) {
		/*
		if ( function_exists( 'is_wp_error' ) && is_wp_error( $message ) ) {
			if ( empty( $title ) ) {
				$error_data = $message->get_error_data();
				if ( is_array( $error_data ) && isset( $error_data['title'] ) )
					$title = $error_data['title'];
			}
			
			$errors = $message->get_error_messages();
			switch ( count( $errors ) ) :
			case 0 :
				$message = '';
				break;
			case 1 :
				$message = "<p>{$errors[0]}</p>";
				break;
			default :
				$message = "<ul>\n\t\t<li>" . join( "</li>\n\t\t<li>", $errors ) . "</li>\n\t</ul>";
				break;
			endswitch;
		} elseif ( is_string( $message ) ) {
			$message = "<p>$message</p>";
		}
		
		if ( isset( $r['back_link'] ) && $r['back_link'] ) {
			$back_text = $have_gettext? __('&laquo; Back') : '&laquo; Back';
			$message .= "\n<p><a href='javascript:history.back()'>$back_text</p>";
		}
		*/
	
		if( !headers_sent() ){
			header::nocache();
			header::send( 'Content-Type: text/html; charset=utf-8' );
		}
	
		if ( empty( $title ) ) {
			$title = 'Studio98 Library &rsaquo; Error';
		}
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $title ?></title>
		<link rel="stylesheet" href="<?php echo $admin_dir; ?>css/install.css" type="text/css" />
	</head>
	<body id="error-page">
		<?php echo $message; ?>
	</body>
	<!-- Ticket #8942, IE bug fix: always pad the error page with enough characters such that it is greater than 512 bytes, even after gzip compression abcdefghijklmnopqrstuvwxyz1234567890aabbccddeeffgghhiijjkkllmmnnooppqqrrssttuuvvwwxxyyzz11223344556677889900abacbcbdcdcededfefegfgfhghgihihjijikjkjlklkmlmlnmnmononpopoqpqprqrqsrsrtstsubcbcdcdedefefgfabcadefbghicjkldmnoepqrfstugvwxhyz1i234j567k890laabmbccnddeoeffpgghqhiirjjksklltmmnunoovppqwqrrxsstytuuzvvw0wxx1yyz2z113223434455666777889890091abc2def3ghi4jkl5mno6pqr7stu8vwx9yz11aab2bcc3dd4ee5ff6gg7hh8ii9j0jk1kl2lmm3nnoo4p5pq6qrr7ss8tt9uuvv0wwx1x2yyzz13aba4cbcb5dcdc6dedfef8egf9gfh0ghg1ihi2hji3jik4jkj5lkl6kml7mln8mnm9ono -->
	</html>
	<?php
		die();
	}

	/**
	 * Starts the timer, for debugging purposes.
	 *
	 * @since 1.0
	 */
	public function timer_start() {
		$this->time_start = microtime( true );
	}

	/**
	 * Stops the debugging timer.
	 *
	 * @since 1.0
	 *
	 * @return int Total time spent on the query, in milliseconds
	 */
	public function timer_stop() {
		return microtime( true ) - $this->time_start;
	}
}