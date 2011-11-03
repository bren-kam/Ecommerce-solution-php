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

$title = _('Product Prices') . ' | ' . _('Product Catalog') . ' ' . TITLE;
get_header(); 
?>

<div id="content">
	<h1><?php echo _('Product Prices'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/', 'products' ); ?>
    <h1><?php echo _('Summary'); ?></h1>
	<h2><?php echo _('1. Select a date'); ?></h2>
	<label for="date"><?php echo _('Date'); ?>:</label>
	<input type="text" id="date" value="<?php echo date_time::date( 'm-d-Y' ); ?>" />
    <br /><br />
    <p class="float-right" id="pButton"><span class="hidden success" id="sSaveMessage"><?php echo _('Your graphs have been saved.'); ?></span><input type="button" class="button" id="bSave" value="<?php echo _('Save'); ?>" /></p>
	<br />
	<h2><?php echo _('2. Enter the values and click Save'); ?></h2>
    <br clear="right" /><br />
	<?php
		nonce::field( 'get-summary-graphs', '_ajax_get_summary_graphs' );
		nonce::field( 'set-values', '_ajax_set_values' );
	?>
	<div id="subcontent">
		<table class="dt" width="100%" perPage="20,50,100">
			<thead>
				<tr>
					<th width="40%" sort="1"><?php echo _('Product Name'); ?></th>
					<th width="30%"><?php echo _('Category'); ?></th>
					<th width="30%"><?php echo _('Brand'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ( is_array( $products ) )
				foreach ( $products as $product ) { ?>
					<tr>
						<td><?php echo $product['name']; ?></td>
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