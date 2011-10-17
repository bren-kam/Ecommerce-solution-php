<?php
/**
 * @page Products > Groups > Show Products
 * @type Dialog
 * @package Imagine Retailer
 */
 
 // Create new AJAX
$ajax = new AJAX( $_GET['_nonce'], 'show-products' );
$ajax->ok( $user, _('You must be signed in to show products') );

// Instantiate class
$pg = new Product_Groups;

$names = $pg->get_names( $_GET['wpgid'] );
?>
<ul>
<?php
if ( is_array( $names ) )
foreach ( $names as $n ) {
?>
<li><?php echo $n; ?></li>
<?php } ?>
</ul>