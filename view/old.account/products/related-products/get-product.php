<?php
/**
 * @page Get Product | Related Products | Products
 * @type Dialog
 * @package Grey Suit Retail
 *
 * @var Product $product
 * @var string $image
 */
 ?>

<div class="float-left"><img src="http://<?php echo $product->industry; ?>.retailcatalog.us/products/<?php echo $product->id; ?>/small/<?php echo $image; ?>" alt="<?php echo $product->name; ?>" width="150" style="padding: 0 10px 10px 0;" /></div>
<div class="float-left">
	<h3><?php echo $product->name; ?></h3>
	<table class="float-left width-auto">
		<tr>
			<td><strong><?php echo _('SKU'); ?>:</strong></td>
			<td><?php echo $product->sku; ?></td>
		</tr>
		<tr>
			<td><strong><?php echo _('Brand'); ?>:</strong></td>
			<td><?php echo $product->brand; ?></td>
		</tr>
		<tr>
			<td width="100"><strong><?php echo _('Category'); ?>:</strong></td>
			<td><?php echo $product->category; ?></td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2" class="text-center"><a href="<?php echo url::add_query_arg( array( '_nonce' => nonce::create('add_product'), 'pid' => $product->id ), '/products/groups/add-product/' ); ?>" class="button close" title="<?php echo _('Add'); ?>" ajax="1"><?php echo _('Add Product'); ?></a></td></tr>
	</table>
</div>
<br clear="left" />