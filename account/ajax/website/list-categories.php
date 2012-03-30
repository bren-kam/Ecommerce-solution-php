<?php
/**
 * @page List Categories
 * @package Grey Suit Retail
 * @subpackage Account
 */

// Instantiate classes
$c = new Categories;
$dt = new Data_Table();

// Set variables
$dt->order_by( 'title', 'a.`date_updated`' );
$dt->add_where( " AND a.`website_id` = " . $user['website']['website_id'] . " AND b.`parent_category_id` = " . (int) $_GET['pcid'] );
$dt->search( array( 'title' => false ) );

// Get pages
$categories = $c->list_categories( $dt->get_variables() );
$dt->set_row_count( $c->count_categories( $dt->get_where() ) );

// Initialize variable
$data = array();

// Create output
if ( is_array( $categories ) )
foreach ( $categories as $cat ) {
	$data[] = array( $cat['title'] . '<br />
        <div class="actions">
            <a href="' . $c->category_url( $cat['category_id'] ) . '" title="' . _('View Category') . '" target="_blank">' . _('View') . '</a> |
            <a href="/website/edit-category/?cid=' . $cat['category_id'] . '" title="' . _('Edit Category') . '">' . _('Edit') . '</a>' . $actions .
        '</div>',
        dt::date( 'F jS, Y', $cat['date_updated'] )
	);
}

// Send response
echo $dt->get_response( $data );