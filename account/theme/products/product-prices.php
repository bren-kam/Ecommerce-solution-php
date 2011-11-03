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

$b = new Brands;
$brands = $b->get_website_brands();

$title = _('Product Prices') . ' | ' . _('Product Catalog') . ' ' . TITLE;
get_header(); 
?>

<div id="content">
	<h1><?php echo _('Product Prices'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/', 'products' ); ?>
	<div id="subcontent">
        <h2><?php echo _('1. Select a brand'); ?></h2>
        <select id="sBrands">
            <option value="">-- <?php echo _('Select Brand'); ?> --</option>
            <?php foreach ( $brands as $brand ) { ?>
                <option value="<?php echo $brand['brand_id']; ?>"><?php echo $brand['name']; ?></option>
            <?php } ?>
        </select>
        <br /><br />
        <p class="float-right" id="pButton"><span class="hidden success" id="sSaveMessage"><?php echo _('Your products have been saved.'); ?></span><input type="button" class="button" id="bSave" value="<?php echo _('Save'); ?>" /></p>
        <br />
        <h2><?php echo _('2. Enter the values and click Save'); ?></h2>
        <br clear="right" />
        <br /><br />
        <br />
        <?php
            nonce::field( 'get-product-prices', '_ajax_get_product_prices' );
            nonce::field( 'set-values', '_ajax_set_values' );
        ?>
		<table class="dt" width="100%" perPage="20,50,100">
			<thead>
				<tr>
					<th sort="1"><?php echo _('SKU'); ?></th>
					<th><?php echo _('Price'); ?></th>
					<th><?php echo _('Price Notes'); ?></th>
                    <th><?php echo _('Alternate Price Name'); ?></th>
                    <th><?php echo _('Alternate Price'); ?></th>
                    <th><?php echo _('Sale Price'); ?></th>
				</tr>
			</thead>
		</table>
        <br clear="right" /><br />
        <p class="float-right" id="pButton2"><span class="hidden success" id="sSaveMessage2"><?php echo _('Your products have been saved.'); ?></span><input type="button" class="button" id="bSave2" value="<?php echo _('Save'); ?>" /></p>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>