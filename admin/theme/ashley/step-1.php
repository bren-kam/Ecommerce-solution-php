<?php
/**
 * @page Step 1
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	url::redirect( '/login/' );

$p = new Products;
$c = new Categories;
$b = new Brands;
$i = new Industries;
$a = new Attributes;
$ta = new Tags;
$v = new Validator;

$v->form_name = 'fReplaceProduct';
$v->add_validation( 'hNewProductID', 'req', 'There must be a replacement product' );

if( nonce::verify( $_POST['_nonce'], 'replace-product' ) ) {
	$errs = $v->validate();
	
	if( empty( $errs ) ) {
		$success = $p->replace_old_feed_product( $_POST['hOldProductID'], $_POST['hNewProductID'] );
		
		if ( $success ) {
			$c = new Categories;
			$success *= $p->remove_product( $_POST['hOldProductID'], $c );
			$success *= $p->delete( $_POST['hOldProductID'] );
		}
	}
}

if( nonce::verify( $_POST['_remove_product'], 'remove-product' ) ) {
	$c = new Categories;
	$remove_product_success = $p->remove_product( $_POST['hOldProductID'], $c );
	$remove_product_success *= $p->delete( $_POST['hOldProductID'] );
}

add_footer( $v->js_validation() );

$pid = $_GET['pid'];

if ( empty( $pid ) )
	$pid = $p->get_old_ashley_feed_product_id();

// Get categories
$categories = $c->get_list();

// Get brands
$brands = ar::assign_key( $b->get_all(), 'brand_id' );

// Get industries
$industries = ar::assign_key( $i->get_all(), 'industry_id' );

// Get attributes
$attribute_list = $a->get_attribute_items();

// Get product information
$product = $p->get( $pid );
$product['images'] = $p->get_images( $pid );
$product['categories_list'] = $p->get_categories( $pid );
$product['tags'] = $ta->get( 'product', $pid );
$product['attribute_items'] = $a->get_attribute_items_by_product( $pid );

$new_product = $p->get_feed( $p->get_new_ashley_feed_product_id( $pid ) );

if( $new_product ) {
	$new_product['images'] = $p->get_feed_images( $pid );
	$new_product['categories_list'] = false;//$p->get_categories( $pid );
	$new_product['tags'] = false;//$ta->get( 'product', $pid );
	$new_product['attribute_items'] = false;//$a->get_attribute_items_by_product( $pid );
}

$related_websites = $p->get_websites_related_to_product( $pid );

$total = $p->get_total_old_ashley_feed_products();

css( 'form', 'jquery.ui' );
javascript( 'validator', 'jquery', 'jquery.ui', 'ashley-2' );

$title = _('Ashley Replacement') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Ashley Replacement'); ?></h1>
	<br clear="all" /><br />
	<?php if ( $success ) { ?>
	<p>Your product has been successfully replaced across <?php echo $success; ?> websites!</p>
	<?php } else if( $remove_product_success ) { ?>
	<p>Your product has been removed from all the websites!</p>
	<?php } ?>
	<p><strong>Products left:</strong> <?php echo $total; ?></p>
	<br /><br />
	<table class="form">
		<tr>
			<th width="150"><strong>Field</strong></th>
			<th width="500"><strong>Original Product</strong></th>
			<th width="500"><strong>New Product</strong> - <span id="sNewLink"><a href="/products/add-edit/?pid=<?php echo $product['product_id']; ?>" target="_blank">Edit</a></th>
		</tr>
		<tr>
			<td>Name:</td>
			<td><?php echo $product['name']; ?></td>
			<td id="name"><?php echo $new_product['name']; ?></td>
		</tr>
		<tr>
			<td>Product ID:</td>
			<td><?php echo $product['product_id']; ?></td>
			<td id="product_id"><?php echo $new_product['product_id']; ?></td>
		</tr>
		<tr>
			<td>URL:</td>
			<td><?php echo $product['slug']; ?></td>
			<td id="slug"><?php echo $new_product['slug']; ?></td>
		</tr>
		<tr>
			<td valign="top" style="vertical-align:top">Images:</td>
			<td>
				<?php
				// Images
				if( count( $product['images'] ) > 0 )
				foreach( $product['images'] as $swatch => $image_array  ) {
					foreach( $image_array as $img ) {
 						echo '<img src="http://' . $industries[$product['industry_id']]['name'] . '.retailcatalog.us/products/' . $product['product_id'] . "/thumbnail/$img" . '" width="46" height="46" alt="" /> ';
					}
				}
				?>
			</td>
			<td id="images">
				<?php
				// Images
				if( count( $new_product['images'] ) > 0 )
				foreach( $new_product['images'] as $swatch => $image_array  ) {
					foreach( $image_array as $img ) {
 						echo '<img src="http://' . $industries[$product['industry_id']]['name'] . '.retailcatalog.us/products/' . $product['product_id'] . "/thumbnail/$img" . '" width="46" height="46" alt="" /> ';
					}
				}
				?>
			</td>
		</tr>
		<tr>
			<td valign="top" style="vertical-align:top">Description:</td>
			<td valign="top" style="vertical-align:top"><?php echo html_entity_decode( $product['description'], ENT_QUOTES ); ?></td>
			<td valign="top" id="description" style="vertical-align:top"><?php echo $new_product['description']; ?></td>
		</tr>
		<tr>
			<td>Status:</td>
			<td><?php echo ucwords( str_replace( '-', ' ', $product['status'] ) ); ?></td>
			<td id="status"><?php echo ucwords( str_replace( '-', ' ', $new_product['status'] ) ); ?></td>
		</tr>
		<tr>
			<td>Brand:</td>
			<td><?php echo $brands[$product['brand_id']]['name']; ?></td>
			<td id="brand"><?php echo $brands[$new_product['brand_id']]['name']; ?></td>
		</tr>
		<tr>
			<td>SKU:</td>
			<td><?php echo $product['sku']; ?></td>
			<td id="sku"><?php echo $new_product['sku']; ?></td>
		</tr>
		<tr>
			<td>Industry:</td>
			<td><?php echo $industries[$product['industry_id']]['name']; ?></td>
			<td id="industry"><?php echo $industries[$new_product['industry_id']]['name']; ?></td>
		</tr>
		<tr>
			<td>Weight:</td>
			<td><?php echo $product['weight']; ?></td>
			<td id="weight"><?php echo $new_product['weight']; ?></td>
		</tr>
		<tr>
			<td>Categories:</td>
			<td>
				<?php
				if( is_array( $product['categories_list'] ) )
				foreach( $product['categories_list'] as $c ) {
					echo $c['name'], '<br />';
				} 
				?>
			</td>
			<td id="categories">
				<?php
				if( is_array( $new_product['categories_list'] ) )
				foreach( $new_product['categories_list'] as $c ) {
					echo $c['name'], '<br />';
				} 
				?>
			</td>
		</tr>
		<tr>
			<td>Product Specifications:</td>
			<td>
				<?php if( !empty( $product['product_specifications'] ) ) { ?>
				<table>
					<?php
					$specifications = unserialize( html_entity_decode( $product['product_specifications'], ENT_QUOTES, 'UTF-8' ) );
					$new_slugs = 0;
					
					if( is_array( $specifications ) && count( $specifications ) > 0 )
					foreach( $specifications as $ps ) {
						$ps_slug = str_replace( ' ', '-', strtolower( $ps[0] ) );
						if( empty( $ps_slug ) ) {
							$ps_slug = $new_slugs;
							$new_slugs++;
						}
					?>
					<tr>
						<td><?php echo html_entity_decode( $ps[0], ENT_QUOTES, 'UTF-8' ); ?></td>
						<td><?php echo html_entity_decode( $ps[1], ENT_QUOTES, 'UTF-8' ); ?></td>
					</tr>
					<?php } ?>
				</table>
				<?php } ?>
			</td>
			<td id="product_specifications">
				<?php if( !empty( $product['product_specifications'] ) ) { ?>
				<table>
					<?php
					$specifications = unserialize( html_entity_decode( $new_product['product_specifications'], ENT_QUOTES, 'UTF-8' ) );
					$new_slugs = 0;
					
					if( is_array( $specifications ) && count( $specifications ) > 0 )
					foreach( $specifications as $ps ) {
						$ps_slug = str_replace( ' ', '-', strtolower( $ps[0] ) );
						if( empty( $ps_slug ) ) {
							$ps_slug = $new_slugs;
							$new_slugs++;
						}
					?>
					<tr>
						<td><?php echo html_entity_decode( $ps[0], ENT_QUOTES, 'UTF-8' ); ?></td>
						<td><?php echo html_entity_decode( $ps[1], ENT_QUOTES, 'UTF-8' ); ?></td>
					</tr>
					<?php } ?>
				</table>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td>Tags:</td>
			<td>
				<?php
				if( is_array( $product['tags'] ) )
				foreach( $product['tags'] as $t ) {
					echo ucwords( $t ), '<br />';
				}
				?>
			</td>
			<td id="tags">
				<?php
				if( is_array( $new_product['tags'] ) )
				foreach( $new_product['tags'] as $t ) {
					echo ucwords( $t ), '<br />';
				}
				?>
			</td>
		</tr>
		<tr>
			<td>Attributes:</td>
			<td>
				<?php
				foreach( $product['attribute_items'] as $ai ) {
					echo '<strong>', $ai['title'], ' &ndash;</strong> ', $ai['attribute_item_name'], '<br />';
				}
				?>
			</td>
			<td id="attributes">
				<?php
				foreach( $new_product['attribute_items'] as $ai ) {
					echo '<strong>', $ai['title'], ' &ndash;</strong> ', $ai['attribute_item_name'], '<br />';
				}
				?>
			</td>
		</tr>
		<tr>
			<td>Publish Visibility:</td>
			<td><?php echo ucwords( $product['publish_visibility'] ); ?></td>
			<td id="publish_visibility"><?php echo ucwords( $new_product['publish_visibility'] ); ?></td>
		</tr>
		<tr>
			<td>Publish Date:</td>
			<td><?php echo $product['publish_date']; ?></td>
			<td id="publish_date"><?php echo $new_product['publish_date']; ?></td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td><strong>Related Websites:</strong></td>
			<td>
				<?php 
					foreach ( $related_websites as $rw ) {
						echo "<p>$rw</p>";
					}
				?>
			</td>
		</tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr><td colspan="3">&nbsp;</td></tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<div style="float:left">
					<form name="fReplaceProduct" action="" method="post">
					<input type="submit" class="button" value="Replace" />
					<input type="hidden" name="hOldProductID" id="hOldProductID" value="<?php echo $product['product_id']; ?>" />
					<input type="hidden" name="hNewProductID" id="hNewProductID" value="<?php echo $new_product['product_id']; ?>" />
					<?php nonce::field('replace-product'); ?>
					</form>
				</div>
				<div style="float:left; margin-left: 20px">
					<form name="fRemoveProduct" action="" method="post">
						<input type="submit" class="button" value="Remove" onclick="return confirm('<?php echo _('Are you sure you want to remove this product? This cannot be undone.'); ?>');" />
						<input type="hidden" name="hOldProductID" id="hOldProductID" value="<?php echo $product['product_id']; ?>" />
						<?php nonce::field( 'remove-product', '_remove_product' ); ?>
					</form>
				</div>
			</td>
			<td><label for="tNewSKU">Enter SKU:</label> <input type="text" class="tb" id="tNewSKU" /></td>
		</tr>
	</table>
	<?php 
		nonce::field( 'autocomplete', '_ajax_autocomplete' );
		nonce::field( 'get-product', '_ajax_get_product' );
	?>
</div>
			
<?php get_footer(); ?>