<?php
/**
 * Base Class
 *
 * Has basic information for the rest of the classes
 *
 * @package Studio98 Framework
 * @since 1.0
 */
abstract class Base_Class {
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
		if ( !headers_sent() ){
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
}