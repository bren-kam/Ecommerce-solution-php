<?php
/**
 * @package Grey Suit Retail
 * @page Products - Search
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AccountProduct[] $products
 * @var int $product_count
 * @var int $page
 * @var int $per_age
 */

?>

<input type="hidden" id="hCurrentPage" value="<?php echo $page; ?>" />
<input type="hidden" id="hPerPage" value="<?php echo $per_page; ?>" />
<input type="hidden" id="hWebsiteProductsCount" value="<?php echo $product_count; ?>" />
<div class="top">
    <div id="tListProducts_length" class="dataTables_length">
        <?php echo _('Products per Page'); ?>:
        <select id="sProductsPerPage">
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="0"><?php echo _('All'); ?></option>
        </select>
    </div>
</div>
<?php
if ( is_array( $products ) ) {
	$remove_product_nonce = nonce::create('remove');
	$block_product_nonce = nonce::create('block');
	$set_category_image_nonce = nonce::create('set_category_image');
	$confirm_remove_product = _('Are you sure you want to remove this product? It cannot be undone.');
	$confirm_block_product = _('Are you sure you want to block this product?');
	
    foreach ( $products as $product ) {
        $image_url = 'http://' . str_replace( ' ', '', $product->industry ) . '.retailcatalog.us/products/' . $product->product_id . '/' . $product->image;
    ?>
    <div id="dProduct_<?php echo $product->product_id; ?>" class="product">
        <h4><?php echo format::limit_chars( $product->name, 37 ); ?></h4>
        <p align="center">
            <span id="sLoadingMsg<?php echo $product->product_id; ?>" style="margin: 10px;"><?php echo _('Loading Image'); ?>...</span>
            <img id="pImage<?php echo $product->product_id; ?>" class="product-image hidden" src="<?php echo $image_url; ?>" alt="<?php echo $product->name; ?>" style="margin: 10px;" />
        </p>
        <p>
            <?php echo _('SKU'); ?>: <?php echo $product->sku; ?><br />
            <?php echo _('Brand'); ?>: <?php echo $product->brand; ?><br />
            <?php echo _('Price'); ?>: $ <span id="sPrice<?php echo $product->product_id; ?>"><?php echo $product->price; ?></span><br />
            <span id="sAlternatePriceName<?php echo $product->product_id; ?>"><?php echo $product->alternate_price_name; ?></span>: $ <span id="sAlternatePrice<?php echo $product->product_id; ?>"><?php echo $product->alternate_price; ?></span>
        </p>
        <p class="product-actions" id="pProductAction<?php echo $product->product_id; ?>">
            <a href="http://<?php echo $user->account->domain . $product->link; ?>" title='View "<?php echo $product->name; ?>"' target="_blank"><?php echo _('View'); ?></a> |
            <a href="<?php echo url::add_query_arg( array( '_nonce' => $remove_product_nonce, 'pid' => $product->product_id ), '/products/remove/' ); ?>" title="<?php echo _('Remove Product'); ?>" ajax="1" confirm="<?php echo $confirm_remove_product; ?>"><?php echo _('Remove'); ?></a> |
            <a href="#" class="edit-product" title="<?php echo _('Edit Product'); ?>"><?php echo _('Edit'); ?></a> |
            <a href="<?php echo url::add_query_arg( array( '_nonce' => $block_product_nonce, 'pid' => $product->product_id ), '/products/block/' ); ?>" title="<?php echo _('Block Product'); ?>" ajax="1" confirm="<?php echo $confirm_block_product; ?>"><?php echo _('Block'); ?></a><br />
            <a href="<?php echo url::add_query_arg( array( '_nonce' => $set_category_image_nonce, 'i' => urlencode( $image_url ), 'cid' => $_POST['cid'] ), '/products/set-category-image/' ); ?>" title="<?php echo _('Set as Category Picture'); ?>" ajax="1"><?php echo _('Set as Category Picture'); ?></a>
        </p>
    </div>
    <?php
	}
}

$previous_class = ( $page > 1 ) ? 'paginate_enabled_previous' : 'paginate_disabled_previous';
$next_class = ( $page * $per_page <= $product_count ) ? 'paginate_enabled_next' : 'paginate_disabled_next';

// Determine the total products
if ( ( $page - 1 ) * $per_page + $per_page < $product_count ) {
	$total = ( $page - 1 ) * $per_page + $per_page;
} else {
	$total = $product_count;
}

if ( 0 == $total )
    $total = $product_count;

?>

<div id="bottom">
	<div class="dataTables_paginate paging_two_button" id="tListRequests_paginate">
		<div class="<?php echo $previous_class; ?>" id="tListRequests_previous"></div>
		<div class="<?php echo $next_class; ?>" id="tListRequests_next"></div>
	</div>

	<div class="dataTables_info" id="tListRequests_info">
		<?php echo _('Items'), ': ', ( $page - 1 ) * $per_page + 1, ' - ', $total, ' ', _('of'), ' ', $product_count; ?>
	</div>

	<input class="hidden" id="doNotRefresh" value="0" />
</div>