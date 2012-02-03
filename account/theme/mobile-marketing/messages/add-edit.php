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

// Redirect to main section if they don't have email marketing
if ( !$user['website']['mobilel_marketing'] )
	url::redirect('/');

// Instantiate Classes
$m = new Mobile_Marketing;
$v = new Validator;
$w = new Websites;

// Get variables
$timezone = $w->get_setting( 'timezone' );

// Figure out what the time is
$now = new DateTime;
$now->setTimestamp( time() - $now->getOffset() + 3600 * $timezone );

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

    // Create message
    $success = $m->create_message( $_POST['taMessage'], $new_date_posted->format('Y-m-d H:i:s'), $future );

}

css( 'jquery.uploadify', 'jquery.timepicker' );
javascript( 'mammoth', 'swfobject', 'jquery.uploadify', 'jquery.timepicker', 'website/page', 'social-media/facebook/posting' );

// Load the jQuery UI CSS
add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );

$selected = "social_media";
$title = _('Posting') . ' | ' . _('Facebook') . ' | ' . _('Social Media') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Posting'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'social-media/', 'posting' ); ?>
	<div id="subcontent">
		<?php if ( 0 == $posting['fb_user_id'] ) { ?>
			<h2 class="title"><?php echo _('Step 1: Go to the Posting application.'); ?></h2>
			<p><?php echo _('Go to the'); ?> <a href="http://apps.facebook.com/op-posting/" title="<?php echo _('Online Platform - Posting'); ?>" target="_blank"><?php echo _('Posting'); ?></a> <?php echo _('application page'); ?>.</p>
			<br /><br />

			<h2 class="title"><?php echo _('Step 2: Connect the application with your dashboard account'); ?></h2>
			<p><?php echo _('Copy the connection key listed below and paste into the Facebook app.'); ?></p>
			<p><?php echo _('Facebook Connection Key'); ?>: <?php echo $posting['key']; ?></p>
			<p><strong><?php echo _('NOTE'); ?></strong>: <?php echo _('You may see a request for permissions. If this is the case, you first need to Allow Permissions to the application before you will be able to move on.'); ?></p>
			<br /><br />

		<?php } else { ?>
			<h2 class="title"><?php echo _('Post To Your Pages'); ?></h2>
			<?php if ( $success ) { ?>
				<p class="success"><?php echo _('Your message has been successfully posted or scheduled to your Facebook page!'); ?></p>
			<?php
			}

			if ( is_array( $pages ) ) { ?>
				<form action="" method="post" name="fFBPost">
					<table>
						<tr>
							<td><strong><?php echo _('Page'); ?>:</strong></td>
							<td><?php echo $pages[$posting['fb_page_id']]['name']; ?></td>
						</tr>
						<tr>
							<td class="top"><label for="taPost"><?php echo _('Post'); ?>:</label></td>
							<td><textarea name="taPost" id="taPost" rows="5" cols="50"></textarea></td>
						</tr>
						<tr>
							<td><label for="tDate"><?php echo _('Send Date'); ?>:</label></td>
							<td><input type="text" class="tb" name="tDate" id="tDate" value="<?php echo ( empty( $date ) ) ? $now->format('Y-m-d') : $date; ?>" maxlength="10" /></td>
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
			<?php } else { ?>
				<p><?php echo _('In order to post to one of your Facebook pages you will need to connect them first.'); ?> <a href="http://apps.facebook.com/op-posting/" title="<?php echo _('Online Platform - Posting'); ?>" target="_blank"><?php echo _('Connect your Facebook pages here.'); ?></a></p>
			<?php
			}
		}
		?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>