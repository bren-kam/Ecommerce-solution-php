<?php
/**
 * @page Social Media - Facebook - Auto Posting
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate Classes
$sm = new Social_Media;
$v = new Validator;
$fb = new FB( '268649406514419', '6ca6df4c7e9d909a58d95ce7360adbf3' );

// Get variables
$auto_posting = $sm->get_auto_posting();

if ( $auto_posting ) {
	$fb->setAccessToken( $auto_posting['access_token'] );
	$accounts = $fb->api( '/' . $auto_posting['fb_user_id'] . '/accounts' );
	$pages = ar::assign_key( $accounts['data'], 'id' );
} else {
	$auto_posting = array(
		'key' => $sm->create_auto_posting()
	);
}

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'fb-post' ) ) {
	$fb->setAccessToken( $pages[$_POST['sFBPageID']]['access_token'] );
	
	// Information:
	// http://developers.facebook.com/docs/reference/api/page/#posts
	$success = $fb->api( $_POST['sFBPageID'] . '/feed', 'POST', array( 'message' => $_POST['taPost'] ) );
}

css( 'jquery.uploadify' );
javascript( 'mammoth', 'swfobject', 'jquery.uploadify', 'website/page' );

$selected = "social_media";
$title = _('Auto Posting') . ' | ' . _('Facebook') . ' | ' . _('Social Media') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Auto Posting'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'social-media/' ); ?>
	<div id="subcontent">
		<?php if ( 0 == $auto_posting['fb_user_id'] ) { ?>
			<h2 class="title"><?php echo _('Step 1: Go to the Auto Posting application.'); ?></h2>
			<p><?php echo _('Go to the'); ?> <a href="http://apps.facebook.com/op-auto-posting/" title="<?php echo _('Online Platform - Auto Posting'); ?>" target="_blank"><?php echo _('Auto Posting'); ?></a> <?php echo _('application page'); ?>.</p>
			<br /><br />
			
			<h2 class="title"><?php echo _('Step 2: Connect the application with your dashboard account'); ?></h2>
			<p><?php echo _('Copy the connection key listed below and paste into the Facebook app.'); ?></p>
			<p><?php echo _('Facebook Connection Key'); ?>: <?php echo $auto_posting['key']; ?></p>
			<p><strong><?php echo _('NOTE'); ?></strong>: <?php echo _('You may see a request for permissions. If this is the case, you first need to Allow Permissions to the application before you will be able to move on.'); ?></p>
			<br /><br />
			
		<?php } else { ?>
			<h2 class="title"><?php echo _('Post To Your Pages'); ?></h2>
			<?php if ( $success ) { ?>
				<p class="success"><?php echo _('Your message has been successfully posted to your Facebook page!'); ?></p>
			<?php
			}
			
			if ( is_array( $pages ) ) { ?>
				<form action="" method="post" name="fFBPost">
					<table>
						<tr>
							<td><label for="sFBPageID"><?php echo _('Page'); ?></label></td>
							<td>
								<select name="sFBPageID" id="sFBPageID">
								<?php 
								if ( is_array( $pages ) )
								foreach ( $pages as $p ) {
								?>
									<option value="<?php echo $p['id']; ?>"><?php echo $p['name']; ?></option>
								<?php } ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><label for="taPost"><?php echo _('Post'); ?></label></td>
							<td valign="top"><textarea name="taPost" id="taPost" rows="5" cols="50"></textarea></td>
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
				<p><?php echo _('In order to post to one of your Facebook pages you will need to connect them first.'); ?> <a href="http://apps.facebook.com/op-auto-posting/" title="<?php echo _('Online Platform - Auto Posting'); ?>" target="_blank"><?php echo _('Connect your Facebook pages here.'); ?></a></p>
			<?php 
			} 
		}
		?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>