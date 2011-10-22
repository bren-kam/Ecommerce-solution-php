<?php
/**
 * @page Autocomplete Products
 * @package Imagine Retailer
 * @subpackage Admin
 */

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'autocomplete' ) ) {
	$p = new Products;
	
	/* Filtering  */
	$where = ( isset( $_SESSION['products']['visibility'] ) ) ? " AND `publish_visibility` = '" . $p->db->escape( $_SESSION['products']['visibility'] ) . "'" : " AND `publish_visibility` <> 'deleted'";
	
	if ( isset( $_SESSION['products']['product-status'] ) && isset( $_SESSION['products']['user'] ) ) {
		switch ( $_SESSION['products']['product-status'] ) {
			case 'created':
				$where .= ' AND `user_id_created` = ' . (int) $_SESSION['products']['user'];
			break;
			
			case 'modified':
				$where .= ' AND `user_id_modified` = ' . (int) $_SESSION['products']['user'];
			break;
		}
	}
	
	if ( isset( $_SESSION['products']['search'] ) ) {
		if ( isset( $_SESSION['products']['type'] ) ) {
			switch ( $_SESSION['products']['type'] ) {
				case 'products':
					$type = 'a.`name`';
				break;
				
				default:
				case 'sku':
					$type = 'a.`sku`';
				break;
				
				case 'brands':
					$type = 'd.`name`';
				break;
			}
		} else {
			$type = 'a.`sku`';
		}
		
		if ( $_SESSION['products']['type'] != $_POST['type'] )
			$where .= " AND ( $type LIKE '" . $p->db->escape( $_SESSION['products']['search'] ) . "%' )";
	}
	
	// Get the right suggestions for the right type
	switch ( $_POST['type'] ) {
		case 'products':
			$results = $p->autocomplete( $_POST['term'] , 'a.`name`', 'products', $where );
		break;
		
		case 'sku':
			$results = $p->autocomplete( $_POST['term'], 'a.`sku`', 'sku', $where );
		break;
		
		case 'brands':
			$results = $p->autocomplete( $_POST['term'], 'd.`name`', 'brands', $where );
		break;
	}
	
	if ( NULL == $results )
		$results = array();
	
	// Needs to be in JSON
	echo json_encode( array( 'objects' => $results ) );
} else {
	echo json_encode( array( 'result' => false, 'error' => _('A verification error occurred. Please refresh the page and try again.') ) );
}