<?php
/**
 * @page Block Products
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
$p = new Products;
$v = new Validator;
$v->form_name = 'fAddBulk';
$v->add_validation( 'taProductSKUs', 'req', _('You must enter SKUs before adding products in bulk') );

add_footer( $v->js_validation() );

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) ) {
    if ( nonce::verify( $_POST['_nonce'], 'block-products' ) ) {
        // Server side validation
        $errs = $v->validate();

        // Add bulk products
        if ( empty( $errs ) )
            $success = $p->block_products( $_POST['taProductSKUs'] );
    } elseif( nonce::verify( $_POST['_nonce'], 'unblock-products' ) && is_array( $_POST['unblock-products']) ) {
        $success = $p->unblock_products( $_POST['unblock-products'] );
    }
}

$blocked_products = $p->get_blocked_products();

$title = _('Add Bulk') . ' | ' . _('Product Catalog') . ' ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Block Products'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/', 'products' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
			<p class="success"><?php echo _('Blocked Products have been successfully updated!'); ?></p>
		<?php
		}
		
		if ( isset( $errs ) )
			echo "<p class='red'>$errs</p>";
		?>
        <p><?php echo _('Separate SKU’s by putting one on each line.'); ?></p>
		<form action="/products/block-products/" method="post" name="fAddBulk">
            <textarea name="taProductSKUs" id="taProductSKUs" cols="50" rows="20" class="col-2"><?php if ( !$success ) echo $_POST['taProductSKUs']; ?></textarea>
            <br /><br />
			<p><input type="submit" class="button" value="<?php echo _('Block Products'); ?>" /></p>
			<?php nonce::field('block-products'); ?>
		</form>
        <br /><br />
        <?php if ( !empty( $blocked_products ) ) { ?>
            <h2><?php echo _('Blocked Products'); ?></h2>
                <br />
            <form action="/products/block-products/" method="post" name="fUnblockProducts">
                <table>
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th><strong><?php echo _('Name'); ?></strong></th>
                            <th><strong><?php echo _('SKU'); ?></strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $blocked_products as $product ) { ?>
                        <tr>
                            <td><input type="checkbox" class="cb" name="unblock-products[]" value="<?php echo $product['product_id']; ?>" /></td>
                            <td><?php echo $product['name']; ?></td>
                            <td><?php echo $product['sku']; ?></td>
                        </tr>
                        <?php } ?>
                        <tr><td colspan="2">&nbsp;</td></tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><input type="submit" class="button" value="<?php echo _('Unblock Products'); ?>" /></td>
                        </tr>
                    </tbody>
                </table>
                <?php nonce::field('unblock-products'); ?>
            </form>
        <?php } ?>
    </div>
	<br /><br />
</div>

<?php get_footer(); ?>