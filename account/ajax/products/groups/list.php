<?php
/**
 * @page List Product Groups
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Instantiate classes
$pg = new Product_Groups;
$dt = new Data_Table;

// Set variables
$dt->order_by( '`name`' );
$dt->add_where( " AND `website_id` = " . $user['website']['website_id'] );
$dt->search( array( '`name`' => true ) );

// Get groups
$groups = $pg->list_groups( $dt->get_variables() );
$dt->set_row_count( $pg->count_groups( $dt->get_where() ) );

$confirm = _('Are you sure you want to delete this product group? This cannot be undone.');
$delete_product_group_nonce = nonce::create( 'delete-product-group' );
$show_products_nonce = nonce::create( 'show-products' );
								
// Create output
if ( is_array( $groups ) )
foreach ( $groups as $g ) {
	$actions = '<a href="/products/groups/add-edit/?wpgid=' . $g['website_product_group_id'] . '" title="' . _('Edit Product Group') . '">' . _('Edit') . '</a>';
	$actions .= ' | <a href="/ajax/products/groups/delete/?wpgid=' . $g['website_product_group_id'] . '&amp;_nonce=' . $delete_product_group_nonce . '" title="' . _('Delete Product Group') . '" ajax="1" confirm="' . $confirm . '">' . _('Delete') . '</a>';
	$actions .= ' | <a href="/dialogs/products/groups/show/?_nonce=' . $show_products_nonce . '&amp;wpgid=' . $g['website_product_group_id'] . '#dShowGroups' . $g['website_product_group_id'] . '" title="' . _("Product Group's Products") . '" rel="dialog">' . _('Show Products') . '</a>';
	
	$data[] = array( $g['name'] . '<div class="actions">' . $actions . '</div>' );
}

// Send response
echo $dt->get_response( $data );