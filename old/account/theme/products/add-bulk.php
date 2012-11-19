<?php
/**
 * @page Add Bulk
 * @package Grey Suit Retail
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
		
		list( $success, $quantity, $no_industries, $already_existed, $not_added_skus ) = $p->add_bulk( $_POST['taProductSKUs'] );

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
	<?php get_sidebar( 'products/', 'products' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
			<p class="success"><?php echo $quantity, _(' products added successfully!'); ?> <a href="/products/" title="<?php echo _('View Products'); ?>"><?php echo _('View products here.'); ?></a></p>
            <?php
			if ( $already_existed > 0 ) {
				?>
				<p><?php echo number_format( $already_existed ), ' ', _('SKU(s) were already on the website.'); ?></p>
			<?php
			}

			if ( count( $not_added_skus ) > 0 ) {
				?>
				<p><?php echo _('The following'), ' ', $other_not_added_sku_count, ' ', _('SKU(s) were not added for one of the following reasons:'); ?></p>
				<br />
				<ol>
					<li><?php echo _('The SKU is not a valid SKU or does not match the SKU in our master catalog'); ?></li>
					<li><?php echo _('The SKUs are for industries not associated with this account'); ?></li>
					<li><?php echo _('There is no image associated with the SKU'); ?></li>
				</ol>
				<br />
				<blockquote style="border-left: 1px solid #929292; margin-left: 20px; padding-left: 20px">
					<?php echo implode( '<br />', $not_added_skus ); ?>
				</blockquote>
				<br />
				<hr />
				<br /><br />
			<?php
			}
		}
		
		if ( isset( $errs ) )
			echo "<p class='red'>$errs</p>";
		?>
        <p><?php echo _("Separate SKU's by putting one on each line."); ?></p>
		<form action="/products/add-bulk/" method="post" name="fAddBulk">
            <textarea name="taProductSKUs" id="taProductSKUs" cols="50" rows="20" class="col-2"><?php if ( !$success ) echo $_POST['taProductSKUs']; ?></textarea>
            <br /><br />
			<p><input type="submit" class="button" value="<?php echo _('Add Bulk'); ?>" /></p>
			<?php nonce::field('add-bulk'); ?>
		</form>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>