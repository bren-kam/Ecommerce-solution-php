<?php
/**
 * @page Edit Brand
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	url::redirect( '/login/' );

if( empty( $_GET['bid'] ) )
	url::redirect( '/brands/' );

$b = new Brands;
$po = new Product_Options;
$v = new Validator();

$product_options = $po->get_all();
$brand = $b->get( $_GET['bid'] );

// Add Validation
$v->form_name = 'fEditBrand';

$v->add_validation( 'tName', 'req', _('The "Name" field is required') );
$v->add_validation( 'tSlug', 'req', _('The "Slug" field is required') );
$v->add_validation( 'tLink', 'URL', _('The "Website" field must contain a valid link') );
$v->add_validation( 'fPicture', 'img', _('The "Image" field may only hold an image with extensions jpg, jpeg, gif and png') );

add_footer( $v->js_validation() );

if( nonce::verify( $_POST['_nonce'], 'update-brand' ) ) {
	$errs = $v->validate();
	
	if( empty( $errs ) ) {
		// Create the brand
		$success = $b->update( $_POST['hBrandID'], $_POST['tName'], $_POST['tSlug'], $_POST['tLink'], $_FILES['fPicture'], $_POST['hProductOptions'] );
	}
}

css( 'form', 'brands/edit' );
javascript( 'validator', 'jquery', 'jquery.tmp-val', 'brands/edit' );

$selected = 'products';
$title = _('Edit Brand') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Edit Brand'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'brands/' ); ?>
	<div id="subcontent">
		<?php 
		if( !$success ) {
			$main_form_class = '';
			$success_class = ' class="hidden"';
			
			if( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		} else {
			$success_class = '';
			$main_form_class = ' class="hidden"';
		}
		?>
		<div id="dMainForm"<?php echo $main_form_class; ?>>
			<?php 
			if( isset( $errs ) && !empty( $errs ) ) {
				$error_message = '';
				
				foreach( $errs as $e ) {
					$error_message .= ( !empty( $error_message ) ) ? '<br />' . $e : $e;
				}
				
				echo "<p class='red'>$error_message</p>";
			}
			?>
			<form action="/brands/edit/?bid=<?php echo $_GET['bid']; ?>" name="fEditBrand" id="fEditBrand" method="post" enctype="multipart/form-data">
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="80"><label for="tName"><?php echo _('Name'); ?></label></td>
						<td><input type="text" name="tName" id="tName" maxlength="100" class="tb" value="<?php echo ( empty( $_POST['tName'] ) ) ? $brand['name'] : $_POST['tName']; ?>" /></td>
					</tr>
					<tr>
						<td width="80"><label for="tSlug"><?php echo _('Slug'); ?></label></td>
						<td><input type="text" name="tSlug" id="tSlug" maxlength="100" class="tb" value="<?php echo ( empty( $_POST['tSlug'] ) ) ? $brand['slug'] : $_POST['tSlug']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="tLink"><?php echo _('Website'); ?></label></td>
						<td><input type="text" name="tLink" id="tLink" maxlength="200" class="tb" value="<?php echo ( empty( $_POST['tLink'] ) ) ? $brand['link'] : $_POST['tLink']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="sProductOptions"><?php echo _('Product Options'); ?></label></td>
						<td>
							<div id="dProductOptionsList">
								<?php
								if( is_array( $brand['product_options'] ) )
								foreach( $brand['product_options'] as $product_option_id ) {
									$po = $product_options[$product_option_id];
								?>
								<div extra="<?php echo $po['title']; ?>" id="dProductOption<?php echo $po['product_option_id']; ?>" class="product-option-container">
									<div class="product-option">
										<span class="product-option-name"><?php echo $po['option_title']; ?></span>
										<div style="display:inline;float:right">
											<a href="javascript:;" class="delete-product-option" title='Delete "<?php echo $po['title']; ?>" Product Option'><img src="/images/icons/x.png" width="15" height="17" alt='Delete "<?php echo $po['title']; ?>"' /></a>
										</div>
									</div>
								</div>
								<?php } ?>
							</div>
							<select name="sProductOptions" id="sProductOptions" class="dd">
								<option value="">-- <?php echo _('Select a Product Option'); ?> --</option>
								<?php
								if( is_array( $product_options ) )
								foreach( $product_options as $po ) {
									$selected = ( $po['product_option_id'] == $_POST['sProductOptions'] ) ? ' selected="selected"' : '';
									
									echo '<option value="', $po['product_option_id'], '"', $selected, '>', $po['option_title'], '</option>';
								}
								?>
							</select> <a href="javascript:;" id="aAddProductOption" title="<?php echo _('Add Product Option'); ?>"><?php echo _('Add'); ?></a>
							<input type="hidden" name="hProductOptions" id="hProductOptions" />
						</td>
					</tr>
					<tr>
						<td><label for="fPicture"><?php echo _('Picture'); ?></label></td>
						<td>
							<?php if( !empty( $brand['image'] ) ) { ?>
							<div id="dImageContainer">
								<img src="<?php echo $brand['image']; ?>" style="padding-bottom: 5px;" alt="<?php echo $brand['name']; ?>" />
								<br />
							</div>
							<?php } ?>
							<input type="file" name="fPicture" id="fPicture" />
						</td>
					</tr>
					<tr><td colspan="2">&nbsp;</td></tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="submit" class="button" value="<?php echo _('Save Brand'); ?>" /></td>
					</tr>
				</table>
				<input type="hidden" name="hBrandID" id="hBrandID" value="<?php echo $brand['brand_id']; ?>" />
				<?php nonce::field( 'update-brand' ); ?>
			</form>
		</div>
		<div id="dSuccess"<?php echo $success_class; ?>>
			<p><?php echo _('Brand has been successfully created!'); ?></p>
			<p><?php echo _('Click here to <a href="/brands/" title="View Brands">view all brands</a> or <a href="javascript:;" id="aEditAnother" title="Edit a Brand">edit another</a>.'); ?></p>
		</div>
		<br clear="all" />
		<br /><br />
		<br /><br />
	</div>
</div>

<?php get_footer(); ?>