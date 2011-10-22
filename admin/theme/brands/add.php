<?php
/**
 * @page Add Brand
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$po = new Product_Options;
$v = new Validator();

$product_options = $po->get_all();

// Add Validation
$v->form_name = 'fAddBrand';

$v->add_validation( 'tName', 'req', _('The "Name" field is required') );
$v->add_validation( 'tSlug', 'req', _('The "Slug" field is required') );
$v->add_validation( 'tLink', 'URL', _('The "Website" field must contain a valid link') );
$v->add_validation( 'fPicture', 'img', _('The "Image" field may only hold an image with extensions jpg, jpeg, gif and png') );

add_footer( $v->js_validation() );

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-brand' ) ) {
	$errs = $v->validate();
	
	if ( empty( $errs ) ) {
		$b = new Brands;
		
		// Create the brand
		$success = $b->create( $_POST['tName'], $_POST['tSlug'], $_POST['tLink'], $_FILES['fPicture'], $_POST['hProductOptions'] );
	}
}

css( 'form', 'brands/add' );
javascript( 'validator', 'jquery', 'jquery.tmp-val', 'brands/add' );

$selected = 'products';
$title = _('Add Brand') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Add Brand'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'brands/' ); ?>
	<div id="subcontent">
		<?php 
		if ( !$success ) {
			$main_form_class = '';
			$success_class = ' class="hidden"';
			
			if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		} else {
			$success_class = '';
			$main_form_class = ' class="hidden"';
		}
		?>
		<div id="dMainForm"<?php echo $main_form_class; ?>>
			<?php 
			if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
			?>
			<form action="/brands/add/" name="fAddBrand" id="fAddBrand" method="post" enctype="multipart/form-data">
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="80"><label for="tName"><?php echo _('Name'); ?></label></td>
						<td><input type="text" name="tName" id="tName" maxlength="100" class="tb" value="<?php if ( !$success && isset( $_POST['tName'] ) ) echo $_POST['tName']; ?>" /></td>
					</tr>
					<tr>
						<td width="80"><label for="tSlug"><?php echo _('Slug'); ?></label></td>
						<td><input type="text" name="tSlug" id="tSlug" maxlength="100" class="tb" value="<?php if ( !$success && isset( $_POST['tSlug'] ) ) echo $_POST['tSlug']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="tLink"><?php echo _('Website'); ?></label></td>
						<td><input type="text" name="tLink" id="tLink" maxlength="200" class="tb" value="<?php if ( !$success && isset( $_POST['tLink'] ) ) echo $_POST['tLink']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="sProductOptions"><?php echo _('Product Options'); ?></label></td>
						<td>
							<div id="dProductOptionsList"></div>
							<select name="sProductOptions" id="sProductOptions" class="dd">
								<option value="">-- <?php echo _('Select a Product Option'); ?> --</option>
								<?php
								$product_option_id = ( isset( $_POST['sProductOptions'] ) ) ? $_POST['sProductOptions'] : '';
								
								if ( is_array( $product_options ) )
								foreach ( $product_options as $po ) {
									$selected = ( !$success && $po['product_option_id'] == $product_option_id ) ? ' selected="selected"' : '';
									
									echo '<option value="', $po['product_option_id'], '"', $selected, '>', $po['option_title'], '</option>';
								}
								?>
							</select> <a href="javascript:;" id="aAddProductOption" title="<?php echo _('Add Product Option'); ?>"><?php echo _('Add'); ?></a>
							<input type="hidden" name="hProductOptions" id="hProductOptions" />
						</td>
					</tr>
					<tr>
						<td><label for="fPicture"><?php echo _('Picture'); ?></label></td>
						<td><input type="file" name="fPicture" id="fPicture" /></td>
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="submit" class="button" value="<?php echo _('Add Brand'); ?>" /></td>
					</tr>
				</table>
				<?php nonce::field( 'add-brand' ); ?>
			</form>
		</div>
		<div id="dSuccess"<?php echo $success_class; ?>>
			<p><?php echo _('Brand has been successfully created!'); ?></p>
			<p><?php echo _('Click here to <a href="/brands/" title="View Brands">view all brands</a> or <a href="javascript:;" id="aAddAnother" title="Add a Brand">add another</a>.'); ?></p>
			<br /><br />
			<br /><br />
			<br /><br />
			<br /><br />
			<br /><br />
			<br /><br />
			<br /><br />
		</div>
		<br clear="all" />
		<br /><br />
		<br /><br />
	</div>
</div>

<?php get_footer(); ?>