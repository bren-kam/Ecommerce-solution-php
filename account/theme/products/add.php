<?php
/**
 * @page Product Catalog > Add Products
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

if ( $user['role'] <= 5 ) {
	// Find out if we can be here
	$w = new Websites;
	
	// Check if they have limited products
	$settings = $w->get_settings('limited-products');
	
	// Make sure they can be here
	if ( '1' == $settings['limited-products'] )
		url::redirect('/products/');
}

// Instantiate Products -- this will be used everywhere
$p = new Products;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-products' ) ) {
	if ( $p->add_products( $_POST['products'] ) )
		url::redirect('/products/?m=1');
}

// Instantiate Classes
$c = new Categories;
$b = new Brands;

// Get variables
$product_count = $p->count_website_products( ' AND ( a.`website_id` = 0 || a.`website_id` = ' . (int) $user['website']['website_id'] . ' ) AND c.`website_id` = ' . (int) $user['website']['website_id'] . " AND a.`publish_visibility` = 'public' AND a.`publish_date` <> '0000-00-00 00:00:00'" );
$categories = $c->get_list();
$brands = $b->get_all();

add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );
css('products/add');
javascript( 'mammoth', 'jquery.boxy', 'jquery.datatables', 'products/add');

$title = _('Add Products') . ' | ' . _('Product Catalog') . ' ' . TITLE;
get_header(); 
?>

<div id="content">
	<h1><?php echo _('Add Products'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/', 'products' ); ?>
	<div id="subcontent">
		<div id="right-sidebar">
			<!-- Box Categories -->
			<div class="box">
				<h2><?php echo _('Products List'); ?></h2>
				<div class="box-content">
					<p><?php echo _('Product Usage'); ?>: <span id="sProductCount"><?php echo number_format( $product_count ); ?></span>/<span id="sAllowedProducts"><?php echo number_format( $user['website']['products'] ); ?></span></p>
					<div id="dProductsList"></div>
					<p id="pNoProducts"><?php echo _('You have not selected any products to add.'); ?></p>
					<p id="pNewCount" class="hidden"><?php echo _('You are adding'), ' <span></span> ', _('new product(s).'); ?></p>
					<form id="fAddProducts" method="post" action="/products/add/">
						<input type="submit" class="button hidden" id="bAddProducts" value="<?php echo _('Add Products'); ?>" />
						<?php nonce::field('add-products'); ?>
					</form>
				</div>
			</div>
			
			<!-- Box Categories -->
			<div class="box">
				<h2><?php echo _('Request a Product'); ?></h2>
				<div class="box-content">
					<p>
						<?php echo _("Don't see a product you want?"); ?>
						<br />
						<a href="#dProductRequest" title="<?php echo _('Request for Product(s)'); ?>" rel="dialog"><?php echo _('Request a product'); ?></a> <?php echo _('and we will add it for you.'); ?>
					</p>
				</div>
			</div>
		</div>
		<div class="page-content">
			<div id="dNarrowSearchContainer">
				<div id="dNarrowSearch">
					<h2><?php echo _('Narrow Your Search'); ?></h2>
					<br />
					<p id="pAdditionalProducts" class="red<?php if ( $product_count < $user['website']['products'] ) echo ' hidden'; ?>"><?php echo _('Please contact your Online Specialist to add additional products. Product Usage has exceeded the number of items allowed.'); ?></p>
					<table cellpadding="0" cellspacing="0" id="tNarrowSearch" width="100%" class="form">
						<tr>
							<td style="width:270px"><label for="sCategory"><?php echo _('Category'); ?></label></td>
							<td class="text-right">
								<select name="sCategory" id="sCategory">
									<option value="">-- <?php echo _('Select a Category'); ?> --</option>
									<?php echo $categories; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<select id="sAutoComplete">
									<option value="sku"><?php echo _('SKU'); ?></option>
									<option value="product"><?php echo _('Product Name'); ?></option>
									<option value="brand"><?php echo _('Brand'); ?></option>
								</select>
							</td>
							<td valign="top" class="text-right"><input type="text" style="width:100%;" class="tb" id="tAutoComplete" tmpval="<?php echo _('Enter SKU...'); ?>" /></td>
						</tr>
					</table>
					<a href="javascript:;" id="aSearch" title="<?php echo _('Search'); ?>" class="button"><?php echo _('Search'); ?></a>
					<br />
					<img id="iNYSArrow" src="/images/narrow-your-search.png" alt="" width="76" height="27" />
				</div>
			</div>
			<br /><br />
			<br /><br />
			<table cellpadding="0" cellspacing="0" id="tAddProducts" width="100%">
				<thead>
					<tr>
						<th width="50%"><?php echo _('Name'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
						<th width="20%"><?php echo _('Brand'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
						<th width="15%"><?php echo _('SKU'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
						<th width="15%"><?php echo _('Status'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
	<?php
	nonce::field( 'products-autocomplete', '_ajax_autocomplete' );
	nonce::field( 'sku-exists', '_ajax_sku_exists' );
	?>
</div>

<div id="dProductRequest" class="hidden">
	<h3><?php echo _('New Request'); ?></h3>
	<br />
	<form name="fProductRequest" action="/ajax/products/product-request/" method="post" ajax="1">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td><label for="sRequestBrand"><?php echo _('Brand'); ?>:</label></td>
				<td>
					<select name="sRequestBrand" id="sRequestBrand" error='<?php echo _('The "Brand" field is required'); ?>'>
						<option value="">-- <?php echo _('Select a Brand'); ?> --</option>
						<?php
						if ( is_array( $brands ) )
						foreach ( $brands as $b ) {
						?>
						<option value="<?php echo $b['brand_id']; ?>"><?php echo $b['name']; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="tRequestSKU"><?php echo _('SKU'); ?>:</label></td>
				<td><input type="text" class="tb" name="tRequestSKU" id="tRequestSKU" error='<?php echo _('The "SKU" field is required'); ?>' /></td>
			</tr>
			<tr>
				<td><label for="tCollection"><?php echo _('Collections/Product'); ?>:</label></td>
				<td><input type="text" class="tb" name="tCollection" id="tCollection" error='<?php echo _('The "Collections/Product" field is required'); ?>' /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><a href="javascript:;" id="aAddRequest" title="<?php echo _('Add Request'); ?>"><?php echo _('Add Request'); ?></a></td>
			</tr>
			<tr><td colspan="2">
				<div id="dRequestList"></div>
				<hr />
			</td></tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" class="button" value="<?php echo _('Send Request'); ?>" /></td>
			</tr>
		</table>
		<a href="javascript:;" id="aClose" class="close hidden">&nbsp;</a>
		<?php nonce::field( 'product-request' , '_ajax_product_request' ); ?>
	</form>
</div>

<?php get_footer(); ?>