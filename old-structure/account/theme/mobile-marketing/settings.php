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
$w = new Websites;

$settings = array( 'timezone' );

// Initialize variables
$success = false;

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
$settings_array = $w->get_settings( $settings );

// Determine default settings
foreach ( $settings_array as $k => &$val ) {
	if ( !empty( $val ) )
		continue;

	switch ( $k ) {
		case 'timezone':
			$val = '';
		break;
	}

	$default_settings[$k] = $val;
}

// Set default settings
if ( isset( $default_settings ) && is_array( $default_settings ) )
	$w->update_settings( $default_settings );

$settings = $settings_array;

$timezone = ( !$success && !empty( $_POST['timezone'] ) ) ? $_POST['timezone'] : $settings['timezone'];

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
				<td><input type="submit" value="<?php echo _('Save'); ?>" class="button" /></td>
			</tr>
		</table>
		<?php nonce::field( 'update-settings' ); ?>
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