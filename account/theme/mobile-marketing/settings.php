<?php
/**
 * @page Email Settings
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Redirect to main section if they don't have email marketing
if ( !$user['website']['email_marketing'] )
	url::redirect('/email-marketing/subscribers/');

// Instantiate class
$e = new Email_Marketing;

// List of settings
$empty_settings = array(
	'from_name' => '',
	'from_email' => '',
	'timezone' => ''
);

// Validation
$v = new Validator();
$v->form_name = 'fSettings';
$v->add_validation( 'from_email', 'email', 'The "From Email" field must contain a valid email' );

add_footer( $v->js_validation() );

// Initialize variables
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'edit-settings' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) ) {
		$settings = array();
		$setting_keys = array_keys( $empty_settings );
		
		// Assign new values for settings
		foreach ( $setting_keys as $sk ) {
			if ( !isset( $_POST[$sk] ) )
				continue;
			
			$settings[$sk] = $_POST[$sk];
		}
		
		// Set settings
		$success = $e->set_settings( $settings );
	}
}

// Get the settings
$settings = array_merge( $empty_settings, $e->get_settings() );

$selected = "email_marketing";
$title = _('Settings | Email Marketing') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Settings'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'email-marketing/', 'settings' ); ?>
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
		<form name="fSettings" action="/email-marketing/settings/" method="post">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td><label for="from_name"><?php echo _('From Name'); ?>:</label></td>
				<td><input type="text" class="tb" value="<?php echo ( !$success && !empty( $_POST['from_name'] ) ) ? $_POST['from_name'] : $settings['from_name']; ?>" name="from_name" id="from_name" maxlength="50" /></td>
			</tr>
			<tr>
				<td><label for="from_email"><?php echo _('From Email'); ?>:</label></td>
				<td><input type="text" class="tb" value="<?php echo ( !$success && !empty( $_POST['from_email'] ) ) ? $_POST['from_email'] : $settings['from_email']; ?>" name="from_email" id="from_email" maxlength="200" /></td>
			</tr>
			<tr>
				<td><label for="timezone"><?php echo _('Timezone'); ?>:</label></td>
				<td>
					<select name="timezone" id="timezone">
						<?php
						$timezone = ( !$success && !empty( $_POST['timezone'] ) ) ? $_POST['timezone'] : $settings['timezone'];
						data::timezones( true, $timezone );
						?>
					</select>
				</td>
			</tr>
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