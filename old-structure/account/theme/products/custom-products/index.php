<?php
/**
 * @page Product Catalog > Custom Products
 * @package Grey Suit Retail
 */

// Get current user
global $user;

if ( $user['role'] <= 5 ) {
	// Find out if we can be here
	$w = new Websites;
	
	// Check if they have limited products
	$settings = $w->get_settings('limited-products');
	
	// Make sure they can be here
	if ( '1' == $settings['limited-products'] )
		url::redirect('/products/');
}

// If user is not logged in
if ( !$user )
	login();

add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );
css('products/custom-products/list');
javascript('jquery.datatables', 'products/custom-products/list');

$title = _('Custom Products') . ' | ' . _('Product Catalog') . ' ' . TITLE;
get_header(); 
?>

<div id="content">
	<h1><?php echo _('Custom Products'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/', 'custom_products' ); ?>
	<div id="subcontent">
		<div id="dNarrowSearchContainer">
			<div id="dNarrowSearch">
				<h2><?php echo _('Narrow Your Search'); ?></h2>
				<br />
				<table cellpadding="0" cellspacing="0" id="tNarrowSearch" width="100%" class="form">
					<tr>
						<td width="264">
							<select id="sAutoComplete">
								<option value="sku"><?php echo _('SKU'); ?></option>
								<option value="product"><?php echo _('Product Name'); ?></option>
								<option value="brand"><?php echo _('Brand'); ?></option>
							</select>
						</td>
						<td valign="top"><input type="text" class="tb" id="tAutoComplete" tmpval="<?php echo _('Enter SKU...'); ?>" style="width: 100% !important;" /></td>
						<td class="text-right" width="125"><a href="javascript:;" id="aSearch" title="<?php echo _('Search'); ?>" class="button"><?php echo _('Search'); ?></a></td>
					</tr>
				</table>
				<img id="iNYSArrow" src="/images/narrow-your-search.png" alt="" width="76" height="27" />
			</div>
		</div>
		<br /><br />
		<br /><br />
		<table perPage="100,250,500" cellpadding="0" cellspacing="0" width="100%" id="tViewProducts">
			<thead>
				<tr>
					<th width="40%" sort="1"><?php echo _('Name'); ?></th>
					<th width="15%"><?php echo _('Brand'); ?></th>
					<th width="10%"><?php echo _('SKU'); ?></th>
					<th width="15%"><?php echo _('Category'); ?></th>
					<th width="8%"><?php echo _('Status'); ?></th>
					<th width="12%"><?php echo _('Published'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		<?php nonce::field( 'custom-products-autocomplete', '_ajax_custom_products_autocomplete' ); ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>