<?php
/**
 * @page Add Edit Product Group
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
	
	$settings = $w->get_settings('limited-products');
	
	// Make sure they can be here
	if ( '1' == $settings['limited-products'] )
		url::redirect('/products/');
}

// Instantiate classes
$a = new Attributes;
$b = new Brands;
$c = new Categories( false );
$i = new Industries;
$products = new Products;
$v = new Validator;

// Get categories
$categories = $c->get_list();

// Get brands
$brands = $b->get_all();

// Get industries
$industries = $i->get_all();

// Get attributes
$attribute_list = $a->get_attribute_items();

// Get the Product ID
$product_id = ( isset( $_GET['pid'] ) ) ? $_GET['pid']: '';

if ( empty( $product_id ) )
	$add = true;

// Add Validation
$v->form_name = 'fAddEdit';

$v->add_validation( 'tName', 'req', _('The "Product Name" field is required"') );
$v->add_validation( 'hCategories', 'req', _('You must add a category') );
$v->add_validation( 'tPublishDate', 'date', _('The "Publish Date" field must have a valid date (yyyy-mm-dd)"') );
$v->add_validation( 'sIndustry', 'req', _('The "Product Industry" field is required"') );

add_footer( $v->js_validation() );
	// Check if they have limited pr

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-product' ) ) {
	// Server side validation
	$errs = $v->validate();
	if ( empty( $errs ) ) {
		if ( !isset( $_POST['hProductImages'] ) )
			$_POST['hProductImages'] = '';
		
		$product_id = (int) $_POST['hProductID']; 
		
		// Update the product
		$success = $products->update( $_POST['tName'], $_POST['tProductSlug'], $_POST['taDescription'], $_POST['sProductStatus'],
				$_POST['tSKU'], $_POST['tPrice'], $_POST['tListPrice'], $_POST['hSpecs'], $_POST['sBrand'], $_POST['sIndustry'],  $_POST['sPublishVisibility'],
				$_POST['tPublishDate'], $product_id, $_POST['tWeight'] );
		
		if ( $success ) {
			// Removes the product properly from the websites and returns the website ids
			$website_ids = $products->remove_product( $product_id, $c );
			
			// Add categories
			$products->empty_categories( $product_id );
			$categories_array = explode( '|', $_POST['hCategories'] );
			$products->add_categories( $product_id, $categories_array );
			
			// Add the products back to their categories and websites
			$products->add_product( $product_id, $categories_array, $c );
			
			// Add tags
			$ta = new Tags;
			
			$ta->delete( 'product', $product_id );
			$ta->add( 'product', $product_id, explode( '|', $_POST['hTags'] ) );
			
			// Add attributes
			$a->delete_item_relations( $product_id );
			$a->add_attribute_item_relations( $_POST['hAttributes'], $product_id );
			
			// Add images
			$products->empty_product_images( $product_id );
			$products->add_product_images( $_POST['hProductImages'], $product_id );
		}
	}
}

// Get everything
if ( !empty( $product_id ) ) {
	// Add tags
	if ( !isset( $ta ) )
		$ta = new Tags;
	
	$p = $products->get_custom_product( $product_id );
	
	if ( !$p )
		url::redirect('/products/custom-products/');
	
	$images = $products->get_images( $product_id );
	$categories_list = $products->get_categories( $product_id );
	$tags = $ta->get( 'product', $product_id );
	$attribute_items = $a->get_attribute_items_by_product( $product_id );
}

add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );
css( 'products/custom-products/add-edit' );
javascript( 'mammoth', 'swfobject', 'jquery.uploadify', 'products/custom-products/add-edit' );

$selected = "products";
$sub_title = ( $product_id ) ? _('Edit Custom Product') : _('Add Custom Product');
$title = "$sub_title | " . _('Product Catalog') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/', 'custom_products' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo ( $add ) ? _('Your product has been added successfully!') : _('Your product has been updated successfully!'); ?></p>
			<p><?php echo _('Click here to'), ' <a href="/products/custom-products/" title="', _('Products'), '">', _('view your products'), '</a>.'; ?></p>
		</div>
		<?php 
		}
		
		nonce::field( 'remove-image', '_ajax_remove_image' );
		nonce::field( 'upload-image', '_ajax_upload_image' );
		nonce::field( 'autocomplete', '_ajax_autocomplete' );
		nonce::field( 'category-attribute-items', '_ajax_category_attribute_items' );
		nonce::field( 'change-industry', '_ajax_change_industry' );
		nonce::field( 'create-custom-product', '_ajax_create_custom_product' );
		nonce::field( 'autocomplete-tags', '_ajax_autocomplete_tags' );
		?>
		<form name="fAddEdit" id="fAddEdit" action="/products/custom-products/add-edit/?pid=<?php echo $product_id; ?>" method="post">
		<div id="right-sidebar">
			<!-- Box Categories -->
			<div class="box">
				<h2><?php echo _('Categories'); ?></h2>
				<div class="box-content">
					<div id="dCategoryList">
						<?php
						if ( isset( $categories_list ) && is_array( $categories_list ) )
						foreach ( $categories_list as $c ) {
							if ( empty( $c['name'] ) )
								continue;
						?>
							<div id="dCategory<?php echo $c['category_id']; ?>" class="product-category">
								<?php echo $c['name']; ?>
								<a href="javascript:;" class="delete-category" id="aDel<?php echo $c['category_id']; ?>" title='<?php echo _('Delete'); ?> "<?php echo $c['name']; ?>"'><img class="delete-category" src="/images/icons/x.png" alt='<?php echo _('Delete'); ?> "<?php echo $c['name']; ?>"' /></a>
							</div>
						<?php } ?>
					</div>
					<select name="sProductCategory" id="sProductCategory">
						<option value="">-- <?php echo _('Select a Category'); ?> --</option>
						<?php echo $categories; ?>
					</select>
					<div class="box-action"><a href="javascript:;" id="aAddCategory" title="<?php echo _('Add Category'); ?>" error="<?php echo _('Please select a category'); ?>"><?php echo _('Add Category'); ?></a></div>
					<br clear="all" />
					<input type="hidden" name="hCategories" id="hCategories" />
				</div>
			</div>
		<!-- End of Box Categories -->
		
		<!-- Box Product Specification -->
			<div class="box">
				<h2><?php echo _('Product Specifications'); ?></h2>
				<div class="box-content">
					<div class="widget-content" id="dSpecificationsWidget">
						<div id="dSpecificationsList">
						<?php
						if ( !empty( $p['product_specifications'] ) ) {
							$specifications = unserialize( html_entity_decode( $p['product_specifications'], ENT_QUOTES, 'UTF-8' ) );
							$new_slugs = 0;
							
							if ( is_array( $specifications ) && count( $specifications ) > 0 )
							foreach ( $specifications as $ps ) {
								$ps_slug = str_replace( ' ', '-', strtolower( $ps[0] ) );
								if ( empty( $ps_slug ) ) {
									$ps_slug = $new_slugs;
									$new_slugs++;
								}
							?>
							<div class="specification" id="dSpec_<?php echo $ps_slug; ?>">
								<div class="specification-name"><?php echo html_entity_decode( $ps[0], ENT_QUOTES, 'UTF-8' ); ?></div>
								<div class="specification-value"><?php echo html_entity_decode( $ps[1], ENT_QUOTES, 'UTF-8' ); ?></div>
								<a href="javascript:;" class="delete-spec" id="aDel_spec_<?php echo $ps_slug; ?>" title='Delete "<?php echo $ps[0]; ?>" <?php echo _('Specification'); ?>'><img src="/images/icons/x.png" alt='Delete "<?php echo $ps[0]; ?>" <?php echo _('Specification'); ?>' /></a>
							</div>
							<?php } } ?>
						</div>
						<table cellpadding="0" cellspacing="0" width="100%" style="clear:left;">
							<tr>
								<td width="49%"><input type="text" class="tb" id="tAddSpecName" maxlength="50" tmpval="<?php echo _('Name'); ?>" /><br /><small> <?php echo _('(example: Width)'); ?></small></td>
								<td width="49%"><textarea class="tb" id="taAddSpecValue" cols="1" rows="1" tmpval="<?php echo _('Value'); ?>"></textarea><br /><small> <?php echo _('(example: 22 in.)'); ?></small></td>
							</tr>
						</table>
						<div class="box-action"><a href="javascript:;" id="aAddSpec" title="<?php echo _('Add Specification'); ?>"><?php echo _('Add Specification'); ?></a></div>
						<input type="hidden" name="hSpecs" id="hSpecs" />
					</div>
					<br clear="all" />
				</div>
			</div>
		<!-- End of Box Product Specification -->
		
		
		<!-- Box Tags -->
			<div class="box">
				<h2><?php echo _('Tags'); ?></h2>
				<div class="box-content">
					<div id="dTagList">
					<?php
					if ( isset( $tags ) && is_array( $tags ) )
					foreach ( $tags as $tag ) {
						$t_name = ucwords( $tag )
						?>
						<div id="dTag_<?php echo $tag; ?>" class="product-tag">
							<?php echo $t_name; ?>
							<a href="javascript:;" class="delete-tag" id="aDel_<?php echo $tag; ?>" title='<?php echo _('Delete'); ?> "<?php echo $t_name; ?>"'><img class="delete-tag" src="/images/icons/x.png" alt="<?php echo _('Delete'), ' ', $t_name; ?>" /></a>
						</div>
					<?php } ?>
					</div>
					<input type="text" class="tb" id="tAddTags" name="tAddTags" />
					<div class="box-action"><a href="javascript:;" id="aAddTags" title="<?php echo _('Add Tags'); ?>"><?php echo _('Add Tags'); ?></a></div>
					<br clear="all" />
					<input type="hidden" name="hTags" id="hTags" />
				</div>
			</div>
		<!-- End of Box Tags -->
		
		<!-- Box Attributes -->
			<div class="box">
				<h2><?php echo _('Attributes'); ?></h2>
				<div class="box-content">
					<div id="dAttributeList">
					<?php
					$disable_attributes = array();
					
					if ( isset( $attribute_items ) && is_array( $attribute_items ) )
					foreach ( $attribute_items as $ai ) {
						$disable_attributes[] = $ai['attribute_item_id'];
						?>
						<div id="dAttributeItem_<?php echo $ai['attribute_item_id']; ?>" class="attribute">
							<strong><?php echo $ai['title']; ?> &ndash;</strong> 
							<?php echo $ai['attribute_item_name']; ?>
							<a href="javascript:;" class="delete-attribute" id="aDel_<?php echo $ai['attribute_item_id']; ?>" title='<?php echo _('Delete'); ?> "<?php echo $ai['attribute_item_name']; ?>"'><img class="delete-attribute" src="/images/icons/x.png" alt='<?php echo _('Delete'); ?> "<?php echo $ai['attribute_item_name']; ?>"' /></a>
						</div>
					<?php } ?>
					</div>
					<select id="sAttributes" multiple="multiple" size="10">
						<?php
						
						$attributes = array_keys( $attribute_list );
						
						foreach ( $attributes as $a ) {
							echo '<optgroup label="', $a, '">';
							
							foreach ( $attribute_list[$a] as $ai ) {
								$disabled = ( in_array( $ai['attribute_item_id'], $disable_attributes ) ) ? ' disabled="disabled"' : '';
								echo '<option value="', $ai['attribute_item_id'], '"', $disabled , '>', $ai['attribute_item_name'], '</option>';
							}
							
							echo '</optgroup>';
						}
						?>
					</select>
					<div class="box-action"><a href="javascript:;" id="aAddAttribute" title="<?php echo _('Add Attribute'); ?>"><?php echo _('Add Attribute'); ?></a></div>
					
					<br clear="all" />
					<input type="hidden" name="hAttributes" id="hAttributes" />
				</div>
			</div>
		<!-- End of Box Attributes -->
		
		<!-- Box Publish  -->
			<div class="box">
				<h2><?php echo _('Publish'); ?></h2>
				<div class="box-content">
					<table cellpadding="0" cellspacing="0" width="100%" style="padding: 5px 5px 0;">
						<tr>
							<td><label for="sPublishVisibility"><?php echo _('Visibility'); ?>:</label></td>
							<td>
								<select name="sPublishVisibility" id="sPublishVisibility" style="width:155px">
									<option value="public"<?php if ( isset( $p['status'] ) && 'public' == $p['publish_visibility'] ) echo ' selected="selected"'; ?>><?php echo _('Public'); ?></option>
									<option value="private"<?php if ( isset( $p['status'] ) && 'private' == $p['publish_visibility'] ) echo ' selected="selected"'; ?>><?php echo _('Private'); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="last"><label for="tPublishDate"><?php echo _('Publish Date'); ?>:</label></td>
							<td class="last"><div class="date-container"><input type="text" class="tb" name="tPublishDate" id="tPublishDate" value="<?php echo ( isset( $p['publish_date'] ) && '0000-00-00 00:00:00' != $p['publish_date'] ) ? str_replace( ' 00:00:00', '', $p['publish_date'] ) : dt::date('Y-m-d'); ?>" extra="<?php echo dt::date('Y-m-d'); ?>" style="width: 150px;" /></div></td>
						</tr>
					</table>
					<div class="box-action"><input type="submit" class="button" id="iPublish" value="<?php echo _('Publish'); ?>" /></div>
				</div>
				<!-- End of Box Publish -->
			</div>
		</div>

		<div class="page-content">
			<?php 
			if ( isset( $errors ) ) 
				echo "<p class='error'>$errors</p>";
			?>
			<input type="hidden" id="hProductID" name="hProductID" value="<?php if ( $product_id ) echo $product_id; ?>" />
			<div id="dNameContainer"><input type="text" name="tName" id="tName" tmpval="<?php echo _('Product Name'); ?>" value="<?php if ( isset( $p['name'] ) ) echo str_replace( '"', '&quot;', $p['name'] ); ?>" maxlength="200" /></div>
			<div id="dProductSlug" <?php echo ( isset( $p['slug'] ) ) ? '' : "class='hidden'"; ?>>
                <span style="float:right;">
                	<a href="javascript:;" id="aCancelProductSlug" title="Cancel" class="hidden"><?php echo _('Cancel'); ?></a>&nbsp;&nbsp;
                	<a href="javascript:;" id="aEditProductSlug" title="<?php echo _('Edit Link'); ?>" class="button round"><?php echo _('Edit'); ?></a>
                    <a href="javascript:;" id="aSaveProductSlug" title="<?php echo _('Save Link'); ?>" class="button hidden round"><?php echo _('Save'); ?></a>
                </span>
            	<span style="clear:right;"><strong><?php echo _('Link:'); ?></strong> <?php echo _('http://www.website.com/'); ?><span id="sCategorySlug"><?php echo _('products'); ?></span>/<span id="sProductSlug"><?php echo ( isset( $p['slug'] ) ) ? $p['slug'] : ''; ?></span><input type="text" name="tProductSlug" id="tProductSlug" maxlength="50" class="tb hidden" value="<?php echo ( isset( $p['slug'] ) ) ? $p['slug'] : ''; ?>" />/</span>
            </div>
			<input type="hidden" name="hCategorySlug" id="hCategorySlug" maxlength="50" />
			
			<br />
			<textarea name="taDescription" id="taDescription" rows="12" cols="50" rte="1"><?php if ( isset( $p['description'] ) ) echo $p['description']; ?></textarea>
			
			<div class="page-widget" id="dBasicProductInfo">
				<h2><?php echo _('Basic Product Info'); ?></h2>
				<br />
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="50%">
							<select name="sProductStatus" id="sProductStatus">
								<option value="in-stock"<?php if ( isset( $p['status'] ) && 'in-stock' == $p['status'] ) echo ' selected="selected"'; ?>><?php echo _('In Stock'); ?></option>
								<option value="special-order"<?php if ( isset( $p['status'] ) && 'special-order' == $p['status'] ) echo ' selected="selected"'; ?>><?php echo _('Special Order'); ?></option>
								<option value="out-of-stock"<?php if ( isset( $p['status'] ) && 'out-of-stock' == $p['status'] ) echo ' selected="selected"'; ?>><?php echo _('Out of Stock'); ?></option>
								<option value="discontinued"<?php if ( isset( $p['status'] ) && 'discontinued' == $p['status'] ) echo ' selected="selected"'; ?>><?php echo _('Discontinued'); ?></option>
							</select>
						</td>
						<td width="50%">
							<input class="hidden" type="text" name="tPrice" id="tPrice" maxlength="20" tmpval="<?php echo _('Price'); ?>" value="<?php if ( isset( $p['price'] ) ) echo $p['price']; ?>" style="width:50%" />
							<select name="sBrand" id="sBrand">
								<option value="">-- <?php echo _('Select a Brand'); ?> --</option>
								<?php 
									$brand_id = ( isset( $p['brand_id'] ) ) ? $p['brand_id'] : '';
									
									if ( is_array( $brands ) )
									foreach ( $brands as $b ) {
										$selected = ( $brand_id == $b['brand_id'] ) ? ' selected="selected"' : '';
								?>
									<option value="<?php echo $b['brand_id']; ?>"<?php echo $selected; ?>><?php echo $b['name']; ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<td><input type="text" class="tb" name="tSKU" id="tSKU" maxlength="100" tmpval="<?php echo _('SKU'); ?>" value="<?php if ( isset( $p['sku'] ) ) echo $p['sku']; ?>" style="width:50%" /></td>
						<td>
							<input class="hidden" type="text" name="tListPrice" id="tListPrice" maxlength="20" tmpval="<?php echo _('List Price (Optional)'); ?>" value="<?php if ( isset( $p['list_price'] ) ) echo $p['list_price']; ?>" style="width:50%" />
							<select name="sIndustry" id="sIndustry" error="<?php echo _('You must select an industry before you can upload an image.'); ?>">
								<option value="">-- <?php echo _('Select an Industry'); ?> --</option>
								<?php
									if ( is_array( $industries ) )
									foreach ( $industries as $i ) {
										$selected = ( isset( $p['industry_id'] ) && $p['industry_id'] == $i['industry_id'] ) ? ' selected="selected"' : '';
								?>
								<option value="<?php echo $i['industry_id']; ?>"<?php echo $selected; ?>><?php echo ucwords( $i['name'] ); ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
                    <tr>
                    	<td><input type="text" class="tb" name="tWeight" tmpval="<?php echo _('Weight'); ?>" value="<?php if ( isset( $p['weight'] ) && $p['weight'] != 0 ) echo $p['weight']; ?>" style="width:50%" /></td>
                    </tr>
				</table>
			</div>
			<div class="divider"></div>
			<div class="page-widget" id="dUploadImages">
				<h2><?php echo _('Upload Images'); ?></h2>
				<p id="pUploadImagesMessage"><?php echo _('You can upload up to 10 images per product. Please ensure images are at least 500px wide or tall as a minimum.'); ?></p>
				<div id="dUploadedImages"></div>
				<br clear="left" />
				
				<?php
				// Put the industry somewhere
				if ( isset( $p['industry'] ) )
					echo '<input type="hidden" id="hIndustryName" value="', str_replace( ' ', '', $p['industry'] ), '" />';
				
				// Images
				if ( isset( $images ) && count( $images ) > 0 )
				foreach ( $images as $swatch => $image_array  ) {
					$image_set = '';
					foreach ( $image_array as $img ) {
						$image_set .= '|' . $img;
					}
					echo '<input type="hidden" value="' . substr( $image_set, 1 ) . '" class="img" id="sw" />';
				}
				?>
				<input type="file" name="fUploadImages" id="fUploadImages" />
				<br />
			</div>
			<br /><br />
			<?php nonce::field( 'add-edit-product' ); ?>
		</div>
		</form>
		<input type="hidden" id="hWebsiteID" value="<?php echo $user['website']['website_id']; ?>" />
		<br clear="all" />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>