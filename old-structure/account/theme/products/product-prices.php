<?php
/**
 * @page Product Catalog > All Products
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$b = new Brands;
$brands = $b->get_website_brands();

css( 'products/product-prices' );
javascript( 'jquery.datatables', 'products/product-prices' );

$title = _('Product Prices') . ' | ' . _('Product Catalog') . ' ' . TITLE;
get_header(); 
?>

<div id="content">
	<h1><?php echo _('Product Prices'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/', 'products' ); ?>
	<div id="subcontent">
        <h2><?php echo _('1. Select a brand'); ?></h2>
        <select id="sBrand">
            <option value="">-- <?php echo _('Select Brand'); ?> --</option>
            <?php foreach ( $brands as $brand ) { ?>
                <option value="<?php echo $brand['brand_id']; ?>"><?php echo $brand['name']; ?></option>
            <?php } ?>
        </select>
        <br /><br />
        <p class="float-right" id="pButton"><span class="hidden success" id="sSaveMessage"><?php echo _('Your products have been saved.'); ?></span> <input type="button" class="button" id="bSave" value="<?php echo _('Save'); ?>" /></p>
        <br />
        <h2><?php echo _('2. Enter the values and click Save'); ?></h2>
        <br clear="right" />
        <br /><br />
        <br />
        <?php nonce::field( 'set-values', '_ajax_set_values' ); ?>
		<table width="100%" id="tProductPrices">
			<thead>
				<tr>
					<th><?php echo _('SKU'); ?></th>
					<th><?php echo _('Price'); ?></th>
					<th><?php echo _('Price Notes'); ?></th>
                    <th><?php echo _('Alternate Price Name'); ?></th>
                    <th><?php echo _('Alternate Price'); ?></th>
                    <th><?php echo _('Sale Price'); ?></th>
				</tr>
			</thead>
		</table>
        <br clear="right" /><br />
        <p class="float-right" id="pButton2"><span class="hidden success" id="sSaveMessage2"><?php echo _('Your products have been saved.'); ?></span> <input type="button" class="button" id="bSave2" value="<?php echo _('Save'); ?>" /></p>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>