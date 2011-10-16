<?php
/**
 * @page Firehost Test
 * @package Imagine Retailer
 */

if ( 1 != $user['user_id'] )
	url::redirect('/');

$p = new Products;
$p->remove_discontinued_products();