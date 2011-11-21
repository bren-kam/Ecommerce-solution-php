<?php
/**
 * @page Product Catalog > All Products
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate Products -- this will be used everywhere
$p = new Products;
$products = $p->get_all_products();

$title = _('All Products') . ' | ' . _('Product Catalog') . ' ' . TITLE;
get_header(); 
?>

<div id="content">
	<h1><?php echo _('All Products'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/', 'products' ); ?>
	<div id="subcontent">
		<table class="dt" width="100%" perPage="100,250,500">
			<thead>
				<tr>
					<th width="30%" sort="1"><?php echo _('Product Name'); ?></th>
                    <th width="20%"><?php echo _('SKU'); ?></th>
					<th width="25%"><?php echo _('Category'); ?></th>
					<th width="25%"><?php echo _('Brand'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ( is_array( $products ) )
				foreach ( $products as $product ) { ?>
					<tr>
						<td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['sku']; ?></td>
						<td><?php echo $product['category']; ?></td>
						<td><?php echo $product['brand']; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>