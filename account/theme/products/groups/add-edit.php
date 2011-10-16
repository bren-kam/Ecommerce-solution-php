<?php
/**
 * @page Add Edit Product Group
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Instantiate Classes
$pg = new Product_Groups;
$v = new Validator;

// Get the website product group id if there is one
$website_product_group_id = (int) $_GET['wpgid'];

$v->form_name = 'fAddEditProductGroup';
$v->add_validation( 'tName', 'req', _('The "Name" field is required') );

// Add validation
add_footer( $v->js_validation() );

// Make sure it's a valid request
if ( nonce::verify( $_POST['_nonce'], 'add-edit-product-group' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) ) {
		if ( $website_product_group_id ) {
			// Update Product Group
			$success = $pg->update( $website_product_group_id, $_POST['tName'], $_POST['products'] );
		} else {
			// Create Product Group
			$success = $pg->create( $_POST['tName'], $_POST['products'] );
		}
	}
}

$product_ids = $products = array();

// Get the email list if necessary
if ( $website_product_group_id ) {
	$product_group = $pg->get( $website_product_group_id );
	$product_ids = $pg->get_products( $website_product_group_id );
}

$product_ids = ( !empty( $_POST['products'] ) ) ? $_POST['products'] : $product_ids;

if( count( $product_ids ) > 0 ) {
	$p = new Products;
	$products = $p->get_products_by_ids( $product_ids );
}

add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );
css( 'products/groups/add-edit' );
javascript( 'jquery.datatables', 'products/groups/add-edit' );

$selected = "products";
$sub_title = ( $website_product_group_id ) ? _('Edit Product Group') : _('Add Product Group');
$title = "$sub_title | " . _('Product Catalog') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/', 'product_groups' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo ( $website_product_group_id ) ? _('Your product group has been updated successfully!') : _('Your product group has been added successfully!'); ?></p>
			<p><?php echo _('Click here to'), ' <a href="/products/groups/" title="', _('Product Groups'), '">', _('view your product groups'), '</a>.'; ?></p>
		</div>
		<?php 
		}
		
		// Allow them to edit the entry they just created
		if ( $success && !$website_product_group_id )
			$website_product_group_id = $success;
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		?>
		<form name="fAddEditProductGroup" action="/products/groups/add-edit/?wpgid=<?php echo $website_product_group_id; ?>" method="post">
			<label for="tName"><?php echo _('Product Group Name'); ?>:</label>
			<input type="text" class="tb" name="tName" id="tName" value="<?php echo ( !$success && $website_product_group_id && empty( $_POST['tName'] ) ) ? $product_group['name'] : $_POST['tName']; ?>" maxlength="50" />
			<br /><br />
					
			<h2><?php echo _('Products'); ?></h2>
			<br clear="all" /><br />
			
			<div id="dNarrowSearchContainer">
				<div id="dNarrowSearch">
					<h2><?php echo _('Narrow Your Search'); ?></h2>
					<br />
					<table cellpadding="0" cellspacing="0" id="tNarrowSearch" width="100%">
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
			<br clear="left" /><br />
			<br /><br />
			<br />
			<table cellpadding="0" cellspacing="0" id="tAddProducts" width="100%">
				<thead>
					<tr>
						<th width="45%"><?php echo _('Name'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
						<th width="25%"><?php echo _('Brand'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
						<th width="15%"><?php echo _('SKU'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
						<th width="15%"><?php echo _('Status'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			<br /><br />
			
			<h2><?php echo _('Selected Products'); ?></h2>
			<div id="dSelectedProducts">
				<?php
				foreach( $products as $product ) {
				?>
				<div id="dProduct_<?php echo $product['product_id']; ?>" class="product">
					<h4><?php echo format::limit_chars( $product['name'], 37 ); ?></h4>
					<p align="center"><img src="http://<?php echo $product['industry']; ?>.retailcatalog.us/products/<?php echo $product['product_id']; ?>/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" height="110" style="margin:10px" /></p>
					<p><?php echo _('Brand'); ?>: <?php echo $product['brand']; ?></p>
					<p class="product-actions" id="pProductAction<?php echo $product['productID']; ?>"><a href="javascript:;" class="remove-product" title="<?php echo _('Remove Product'); ?>"><?php echo _('Remove'); ?></a></p>
					<input type="hidden" name="products[]" class="hidden-product" id="hProduct<?php echo $product['product_id']; ?>" value="<?php echo $product['product_id']; ?>" />
				</div>
				<?php } ?>
			</div>
			<br clear="left" /><br />
			
			<input type="submit" class="button" value="<?php echo ( $website_product_group_id ) ? _('Update Product Group') : _('Add Product Group'); ?>" />
			<?php nonce::field('add-edit-product-group'); ?>
		</form>
		<?php nonce::field( 'products-autocomplete', '_ajax_autocomplete' ); ?>
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>