<?php
/**
 * @page Send Mobile Message
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Redirect to main section if they don't have mobile marketing
if ( !$user['website']['mobile_marketing'] )
	url::redirect('/');

// Instantiate Classes
$m = new Mobile_Marketing;
$v = new Validator;
$w = new Websites;

// Get the mobile message id if there is one
$mobile_message_id = ( isset( $_GET['mmid'] ) ) ? $_GET['mmid'] : false;

// Get variables
$timezone = $w->get_setting( 'timezone' );

// Figure out what the time is
$now = new DateTime;
$now->setTimestamp( time() - $now->getOffset() + 3600 * $timezone );

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'mobile-message' ) ) {
	$date_posted = $_POST['tDate'];

	// Turn it into machine-readable time
	if ( !empty( $_POST['tTime'] ) ) {
		list( $time, $am_pm ) = explode( ' ', $_POST['tTime'] );

		if ( 'pm' == strtolower( $am_pm ) ) {
			list( $hour, $minute ) = explode( ':', $time );

			$date_posted .= ' ' . ( $hour + 12 ) . ':' . $minute . ':00';
		} else {
			$date_posted .= ' ' . $time . ':00';
		}
	}

	// Adjust for time zone
	$new_date_posted = new DateTime;
	$new_date_posted->setTimestamp( strtotime( $date_posted ) - (  $timezone * 3600 ) + $now->getOffset() );

	// Make sure we don't have anything extra
	$_POST['taMessage'] = stripslashes( $_POST['taMessage'] );

    // Do we future date?
    $future = time() >= $new_date_posted->getTimestamp();

    if ( $mobile_message_id ) {
        // Update message
        $success = $m->update_message( $mobile_message_id, $_POST['taMessage'], $new_date_posted->format('Y-m-d H:i:s'), $_POST['cbMobileLists'], $future );
    } else {
        // Create message
        $success = $m->create_message( $_POST['taMessage'], $new_date_posted->format('Y-m-d H:i:s'), $_POST['cbMobileLists'], $future );
    }
}

// Get the subscriber if necessary
if ( $mobile_message_id ) {
	$message = $m->get_message( $mobile_message_id );
} else {
	// Initialize variable
	$message = array(
		'message' => ''
		, 'mobile_lists' => ''
	);
}

css( 'jquery.timepicker' );
javascript( 'mammoth', 'jquery.timepicker', 'mobile-marketing/messages/posting' );

// Load the jQuery UI CSS
add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );

$selected = "social_media";
$title = _('Posting') . ' | ' . _('Facebook') . ' | ' . _('Social Media') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Create Message'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'mobile-marketing/' ); ?>
	<div id="subcontent">

        <?php if ( $success ) { ?>
            <p class="success"><?php echo _('Your message has been successfully posted or scheduled to your Facebook page!'); ?></p>
        <?php } ?>

        <form action="" method="post" name="fMobileMessage">
            <table>
                <tr>
                    <td class="top"><label for="taMessage"><?php echo _('Message'); ?>:</label></td>
                    <td><textarea name="taMessage" id="taMessage" rows="5" cols="50"></textarea></td>
                </tr>
                <tr>
                    <td class="top"><label><?php echo _('Lists'); ?>:</label></td>
                    <td>
                        <?php
                            $mobile_lists = $m->get_mobile_lists();

                            if ( is_array( $mobile_lists ) )
                            foreach ( $mobile_lists as $ml ) {
                                ?>
                                <input type="checkbox" class="cb" name="cbMobileLists[]" id="cbMobileList<?php echo $ml['mobile_list_id']; ?>" value="<?php echo $ml['mobile_list_id']; ?>" /> <label for="cbMobileList<?php echo $ml['mobile_list_id']; ?>"><?php echo $ml['name']; ?></label>
                                <?php
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><label for="tDate"><?php echo _('Send Date'); ?>:</label></td>
                    <td><input type="text" class="tb" name="tDate" id="tDate" value="<?php echo ( empty( $date_posted ) ) ? $now->format('m/d/Y') : $date_posted; ?>" maxlength="10" /></td>
                    <td><label for="tTime"><?php echo _('Time'); ?></label>:</td>
                    <td><input type="text" class="tb" name="tTime" id="tTime" style="width: 75px;" value="<?php echo ( empty( $time ) ) ? $now->format('h:i a') : dt::date( 'h:i a', strtotime( $time ) ); ?>" maxlength="8" /></td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" class="button" value="<?php echo _('Post to Facebook'); ?>" /></td>
                </tr>
            </table>
            <?php nonce::field('fb-post'); ?>
        </form>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>