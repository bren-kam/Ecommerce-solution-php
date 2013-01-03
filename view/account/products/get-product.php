<?php
/**
 * @page Get Product Dialog
 * @type Dialog
 * @package Grey Suit Retail
 *
 * @var Product $product
 * @var Category $category
 */
 ?>

<div class="float-left"><img src="http://<?php echo $product->industry; ?>.retailcatalog.us/products/<?php echo $product->id, '/small/', reset( $product->images ); ?>" alt="<?php echo $product->name; ?>" width="200" height="200" style="padding: 0 10px 10px 0;" /></div>
<div class="float-left">
	<h3><?php echo $product->name; ?></h3>
	<table cellpadding="0" cellspacing="0" class="float-left form">
		<tr>
			<td><strong><?php echo _('SKU'); ?>:</strong></td>
			<td><?php echo $product->sku; ?></td>
		</tr>
		<tr>
			<td><strong><?php echo _('Brand'); ?>:</strong></td>
			<td><?php echo $product->brand; ?></td>
		</tr>
		<tr>
			<td><strong><?php echo _('Category'); ?>:</strong></td>
			<td><?php echo $category->name; ?></td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td colspan="2" class="text-center"><a href="#" class="button add-product" id="aAddProduct<?php echo $product->id; ?>" name="<?php echo $product->name; ?>" title="<?php echo _('Add'); ?>"><?php echo _('Add Product'); ?></a></td></tr>
	</table>
</div>
<br clear="left" />