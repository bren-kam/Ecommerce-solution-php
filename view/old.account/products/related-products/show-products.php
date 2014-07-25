<?php
/**
 * @page Show Products | Related Products | Products
 * @type Dialog
 * @package Grey Suit Retail
 *
 * @var Product[] $products
 */
 ?>

<ul>
<?php
if ( is_array( $products ) )
foreach ( $products as $product ) {
?>
<li><?php echo $product->name; ?></li>
<?php } ?>
</ul>