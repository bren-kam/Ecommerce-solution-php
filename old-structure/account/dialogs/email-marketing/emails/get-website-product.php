<?php
/**
 * @page Get Website Product Dialog
 * @type Dialog
 * @package Grey Suit Retail
 */
 
 // Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'get-website-product' );
$ajax->ok( $user, _('You must be signed in to get a website product') );

// Instantiate class
$p = new Products();

$product = $p->get_website_product( $_GET['pid'] );
?>

<div class="float-left"><img src="http://<?php echo $product['industry']; ?>.retailcatalog.us/products/<?php echo $product['product_id'], '/', $product['image']; ?>" alt="<?php echo $product['name']; ?>" width="150" style="padding: 0 10px 10px 0;" /></div>
<div class="float-left">
	<h3><?php echo $product['name']; ?></h3>
	<table cellpadding="0" cellspacing="0" class="float-left">
		<tr>
			<td><strong><?php echo _('SKU'); ?>:</strong></td>
			<td><?php echo $product['sku']; ?></td>
		</tr>
		<tr>
			<td><strong><?php echo _('Brand'); ?>:</strong></td>
			<td><?php echo $product['brand']; ?></td>
		</tr>
		<tr>
			<td><strong><?php echo _('Category'); ?>:</strong></td>
			<td><?php echo $product['category']; ?></td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2" class="text-center"><a href="/ajax/email-marketing/emails/add-website-product/?_nonce=<?php echo nonce::create( 'add-website-product' ); ?>&amp;pid=<?php echo $product['product_id']; ?>" class="button close" title="<?php echo _('Add Product'); ?>" ajax="1"><?php echo _('Add Product'); ?></a></td></tr>
	</table>
</div>
<br clear="left" />