<?php
/**
 * @page Mobile Settings
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Redirect to main section if they don't have mobile marketing
if ( !$user['website']['mobile_marketing'] )
	url::redirect('/');

// Instantiate class
$m = new Mobile_Marketing;

// List of settings
$empty_settings = array(
);

// Initialize variables
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'edit-settings' ) ) {
    $settings = array();
    $setting_keys = array_keys( $empty_settings );

    // Assign new values for settings
    foreach ( $setting_keys as $sk ) {
        if ( !isset( $_POST[$sk] ) )
            continue;

        $settings[$sk] = $_POST[$sk];
    }

    // Set settings
    $success = $m->set_settings( $settings );
}

// Get the settings
$settings = array_merge( $empty_settings, $m->get_settings() );

$selected = "mobile_marketing";
$title = _('Settings') . ' | ' . _('Mobile Marketing') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Settings'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'mobile-marketing/', 'settings' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo _('Your settings have been successfully updated!'); ?></p>
		</div>
		<?php
		}
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		?>
		<form name="fSettings" action="/mobile-marketing/settings/" method="post">
		<table cellpadding="0" cellspacing="0">
			
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" value="<?php echo _('Save'); ?>" class="button" /></td>
			</tr>
		</table>
		<?php nonce::field( 'edit-settings' ); ?>
		</form>
	</div>
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
</div>

<?php get_footer(); ?>