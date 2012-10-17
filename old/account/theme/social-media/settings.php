<?php
/**
 * @page Social Media Settings
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate Class
$w = new Websites;

// Get the settings
$settings = array( 'timezone' );

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-settings' ) ) {
    $new_settings = array();

    foreach ( $settings as $k ) {
        $new_settings[$k] = $_POST[$k];
    }

    // Update the settings
    $success = $w->update_settings( $new_settings );
}

// Get the settings
$settings = $w->get_settings( 'timezone' );

// Set default settings
if ( isset( $default_settings ) && is_array( $default_settings ) )
	$w->update_settings( $default_settings );


$selected = "social_media";
$title = _('Settings') . ' | ' . _('Social Media') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Settings'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'social-media/', 'settings' ); ?>
	<div id="subcontent">
		<?php if ( isset( $success ) && $success ) { ?>
		<div class="success">
			<p><?php echo _('Your settings have been updated successfully!'); ?></p>
		</div>
		<?php 
		}
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		?>
		<form name="fSettings" action="/social-media/settings/" method="post">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><label for="timezone"><?php echo _('Timezone'); ?>:</label></td>
					<td>
						<select name="timezone" id="timezone">
							<?php
							$timezone = ( !$success && !empty( $_POST['timezone'] ) ) ? $_POST['timezone'] : $settings['timezone'];
							data::timezones( true, $timezone, true );
							?>
						</select>
					</td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="button" value="<?php echo _('Update Settings'); ?>" /></td>
				</tr>
			</table>
			<?php nonce::field( 'update-settings' ); ?>
		</form>
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>