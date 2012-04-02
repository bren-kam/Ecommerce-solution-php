<?php
/**
 * @page Get Products
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Don't display anything
if ( !nonce::verify( $_POST['_nonce'], 'get-products' ) )
	exit;

// Instantiate Classes
$p = new Products;
$wc = new Website_Categories;
$w = new Websites;

// Type Juggling
$category_id = (int) $_POST['cid'];

$where = '';
$settings = $w->get_settings( 'limited-products' );

// Category ID
if ( $category_id )
	$where .= ' AND c.`category_id` IN (' . preg_replace( '/[^0-9,]/', '', implode( ',', array_merge( array( $category_id ), $wc->get_all_child_categories( $category_id ) ) ) ) . ')';

// If they only want discontinued products, then only grab them
if ( '1' == $_POST['od'] )
    $where .= " AND a.`status` = 'discontinued'";

// Search type
if ( !empty( $_POST['v'] ) ) {
	switch ( $_POST['s'] ) {
		case 'sku':
			if ( _('Enter SKU...') != $_POST['v'] )
				$where .= " AND a.`sku` LIKE '" . $p->db->escape( $_POST['v'] ) . "%'";
		break;

		case 'product':
			if ( _('Enter Product Name...') != $_POST['v'] ) 
				$where .= " AND a.`name` LIKE '" . $p->db->escape( $_POST['v'] ) . "%'";
		break;
		
		case 'brand':
			if ( _('Enter Brand...') != $_POST['v'] ) 
				$where .= " AND d.`name` LIKE '" . $p->db->escape( $_POST['v'] ) . "%'";
		break;
	}
}

$per_page = ( $_POST['n'] > 100 ) ? 20 : (int) $_POST['n'];
$page = ( empty( $_POST['p'] ) ) ? 1 : (int) $_POST['p'];

$products = $p->get_website_products( $per_page, $where, $page );

$product_count = $p->get_website_products_count( $where );
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
            <option value="0">All</option>
        </select>
    </div>
</div>
<?php
if ( is_array( $products ) ) {
	$remove_product_nonce = nonce::create('remove-product');
	$confirm_remove_product = _('Are you sure you want to remove this product? It cannot be undone.');
	$set_category_image_nonce = nonce::create('set-category-image');
	
foreach ( $products as $product ) {
	$image_url = 'http://' . str_replace( ' ', '', $product['industry'] ) . '.retailcatalog.us/products/' . $product['product_id'] . '/';
	
	if ( !empty(	$product['swatch'] ) ) 
		$image_url .= $product['swatch'] . '/';
	
	$image_url .= $product['image'];
?>
<div id="dProduct_<?php echo $product['product_id']; ?>" class="product">
	<h4><?php echo format::limit_chars( $product['name'], 37 ); ?></h4>
	<p align="center">
		<span id="sLoadingMsg<?php echo $product['product_id']; ?>" style="margin: 10px;"><?php echo _('Loading Image'); ?>...</span>
		<img id="pImage<?php echo $product['product_id']; ?>" class="product-image hidden" src="<?php echo $image_url; ?>" alt="<?php echo $product['name']; ?>" style="margin: 10px;" />
	</p>
	<p>
		<?php echo _('SKU'); ?>: <?php echo $product['sku']; ?><br />
		<?php echo _('Brand'); ?>: <?php echo $product['brand']; ?><br />
		<?php echo _('Price'); ?>: $ <span id="sPrice<?php echo $product['product_id']; ?>"><?php echo $product['price']; ?></span><br />
		<span id="sAlternatePriceName<?php echo $product['product_id']; ?>"><?php echo $product['alternate_price_name']; ?></span>: $ <span id="sAlternatePrice<?php echo $product['product_id']; ?>"><?php echo $product['alternate_price']; ?></span>
	</p>
	<p class="product-actions" id="pProductAction<?php echo $product['product_id']; ?>">
		<a href="<?php echo $product['link']; ?>" title='View "<?php echo $product['name']; ?>"' target="_blank"><?php echo _('View'); ?></a> | 
		<a href="/ajax/products/remove/?_nonce=<?php echo $remove_product_nonce; ?>&amp;pid=<?php echo $product['product_id']; ?>" title="<?php echo _('Remove Product'); ?>" ajax="1" confirm="<?php echo $confirm_remove_product; ?>"><?php echo _('Remove'); ?></a> |
		<a href="javascript:;" class="edit-product" title="<?php echo _('Edit Product'); ?>"><?php echo _('Edit'); ?></a><br />
		<a href="/ajax/products/set-category-image/?_nonce=<?php echo $set_category_image_nonce; ?>&amp;i=<?php echo urlencode( $image_url ); ?>&amp;cid=<?php echo $_POST['cid']; ?>" title="<?php echo _('Set as Category Picture'); ?>" ajax="1"><?php echo _('Set as Category Picture'); ?></a>
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