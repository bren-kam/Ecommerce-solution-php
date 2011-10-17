<?php
/**
 * @page Website Settings
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate Class
$w = new Websites;

$settings = array( 'banner-width', 'banner-height', 'banner-speed', 'banner-background-color', 'banner-loading-color' );
		
$v = new Validator();
$v->form_name = 'fSettings';
$v->add_validation( 'banner-width', 'req', _('The "Banners - Width" field is required') );
$v->add_validation( 'banner-width', 'num', _('The "Banner - Width" field may only contain a number') );

$v->add_validation( 'banner-height', 'req', _('The "Banners - Height" field is required') );
$v->add_validation( 'banner-height', 'num', _('The "Banners - Height" field may only contain a number') );

$v->add_validation( 'banner-speed', 'req', _('The "Banners - Speed" field is required') );
$v->add_validation( 'banner-speed', 'num', _('The "Banners - Speed" field may only contain a number') );

// Add validation
add_footer( $v->js_validation() );

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-settings' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) ) {
		$new_settings = array();
		
		foreach ( $settings as $k ) {
			// Don't need to update this one
			if ( 'banner-loading-color' == $k )
				continue;
			
			$new_settings[$k] = $_POST[$k];
		}
		
		// Update the settings
		$success = $w->update_settings( $new_settings );
	}
}

// Get the settings
$settings_array = $w->get_settings( $settings );

// Determine default settings
foreach ( $settings_array as $k => &$val ) {
	if ( !empty( $val ) )
		continue;
		
	switch ( $k ) {
		case 'banner-background-color':
			$val = 'FFFFFF';
		break;
		
		case 'banner-loading-color':
			$val = '4C86B0';
		break;
		
		case 'banner-width':
			$val = 680;
		break;
		
		case 'banner-height':
			$val = 300;
		break;
		
		case 'banner-speed':
			$val = 3;
		break;
	}
	
	$default_settings[$k] = $val;
}

// Set default settings
if ( isset( $default_settings ) && is_array( $default_settings ) )
	$w->update_settings( $default_settings );
	
$settings = $settings_array;

$selected = "website";
$title = _('Settings | Website ') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Settings'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'website/', 'settings' ); ?>
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
		<form name="fSettings" action="/website/settings/" method="post">
			<table cellpadding="0" cellspacing="0">
				<tr><td colspan="2" class="title"><strong><?php echo _('Banners'); ?></strong></td>
				<tr>
					<td width="150"><label for="banner-width"><?php echo _('Width'); ?>:</label></td>
					<td><input type="text" class="tb" name="banner-width" id="banner-width" value="<?php echo ( isset( $success ) && !$success ) ? $_POST['banner-width'] : $settings['banner-width']; ?>" maxlength="4" /></td>
				</tr>
				<tr>
					<td><label for="banner-height"><?php echo _('Height'); ?>:</label></td>
					<td><input type="text" class="tb" name="banner-height" id="banner-height" value="<?php echo ( isset( $success ) && !$success ) ? $_POST['banner-height'] : $settings['banner-height']; ?>" maxlength="3" /></td>
				</tr>
				<tr>
					<td><label for="banner-speed"><?php echo _('Speed'); ?>:</label></td>
					<td><input type="text" class="tb" name="banner-speed" id="banner-speed" value="<?php echo ( isset( $success ) && !$success ) ? $_POST['banner-speed'] : $settings['banner-speed']; ?>" maxlength="2" /></td>
				</tr>
				<tr>
					<td><label for="banner-background-color"><?php echo _('Background Color'); ?>:</label></td>
					<td><input type="text" class="tb" name="banner-background-color" id="banner-background-color" value="<?php echo ( isset( $success ) && !$success ) ? $_POST['banner-background-color'] : $settings['banner-background-color']; ?>" maxlength="6" /></td>
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