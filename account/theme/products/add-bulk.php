<?php
/**
 * @page Add Bulk
 * @package Imagine Retailer
 *
 * @since 1.0.3
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Add validation
$v = new Validator;
$v->form_name = 'fAddBulk';
$v->add_validation( 'taProductSKUs', 'req', _('You must enter SKUs before adding products in bulk') );

add_footer( $v->js_validation() );

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-bulk' ) ) {
	// Server side validation
	$errs = $v->validate();
	
	// Dump brand i there was no errors
	if ( empty( $errs ) ) {
		$p = new Products;
		
		list( $success, $quantity, $no_industries ) = $p->add_bulk( $_POST['taProductSKUs'] );

		if ( !$success ) {
			if ( $no_industries ) {
				$errs .= _("This website has no industries.  Please contact your online specialist for assistance with this issue.");
			} else {
				$errs .= _("There is not enough free space to add this brand. Delete at least $quantity products, or expand the size of the product catalog.");
			}
		}
	}
}

$title = _('Add Bulk') . ' | ' . _('Product Catalog') . ' ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Add Bulk'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
			<p class="success"><?php echo $quantity, _(' products added successfully!'); ?></p>
		<?php
		}
		
		if ( isset( $errs ) )
			echo "<p class='red'>$errs</p>";
		?>
        <p><?php echo _('Separate SKU’s by putting one on each line.'); ?></p>
		<form action="/products/add-bulk/" method="post" name="fAddBulk">
            <textarea name="taProductSKUs" id="taProductSKUs" cols="50" rows="20" class="col-2"><?php echo $_POST['taProductSKUs']; ?></textarea>
            <br /><br />
			<p><input type="submit" class="button" value="<?php echo _('Add Bulk'); ?>" /></p>
			<?php nonce::field('add-bulk'); ?>
		</form>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>