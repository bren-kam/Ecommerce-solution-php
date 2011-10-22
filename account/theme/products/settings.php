<?php
/**
 * @page Product Catalog > Settings
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate class
$w = new Websites;

$settings_array = array( 'request-a-quote-email', 'category-show-price-note', 'add-product-popup', 'hide-skus', 'hide-request-quote', 'hide-customer-ratings' );

$v = new Validator();
$v->form_name = 'fSettings';
$v->add_validation( 'request-a-quote-email', 'email', _('The "Request-a-Quote Email" field must contain a valid email') );

// Add validation
add_footer( $v->js_validation() );

// Initialize variable
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-product-settings' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) ) {
		$new_settings = array();
		
		foreach ( $settings_array as $k ) {
			$new_settings[$k] = $_POST[$k];
		}
		
		// Update the settings
		$success = $w->update_settings( $new_settings );
	}
}

// Make sure the settings exist
$settings['request-a-quote-email'] = '';

// Get the settings
$settings = $w->get_settings( $settings_array );

$checkboxes = array(
	'category-show-price-note' 	=> _('Categories - Show Price Note?'),
	'add-product-popup' 		=> _('Add Product - Popup'), 
	'hide-skus' 				=> _('Hide Manufacturer SKUs'), 
	'hide-request-quote' 		=> _('Hide "Request a Quote" Button'), 
	'hide-customer-ratings' 	=> _('Hide Customer Ratings')
);

$title = _('Settings') . ' | ' . _('Product Catalog') . ' ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Settings'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo _('Your settings has been successfully updated!'); ?></p>
		</div>
		<?php 
		}
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		?>
		<form action="/products/settings/" method="post" name="fSettings">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td width="175"><label for="request-a-quote-email"><?php echo _('Request-a-Quote Email'); ?>:</label></td>
					<td><input type="text" class="tb" name="request-a-quote-email" id="request-a-quote-email" value="<?php echo ( $success || !isset( $_POST['request-a-quote-email'] ) ) ? $settings['request-a-quote-email'] : $_POST['request-a-quote-email']; ?>" maxlength="150" /></td>
				</tr>
				<?php
				foreach ( $checkboxes as $k => $v ) {
					$checked = ( ( isset( $_POST['k'] ) && '1' == $_POST[$k] ) || ( !isset( $_POST[$k] ) && $settings[$k] ) );
					?>
					<tr>
						<td>&nbsp;</td>
						<td><input type="checkbox" name="<?php echo $k; ?>" id="<?php echo $k; ?>" value="1"<?php echo ( $checked ) ? ' checked="checked"' : ''; ?> /> <label for="<?php echo $k; ?>"><?php echo $v; ?></label></td>
					</tr>
				<?php } ?>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" value="<?php echo _('Update Information'); ?>" class="button" /></td>
				</tr>
			</table>
			<?php nonce::field('update-product-settings'); ?>
		</form>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>