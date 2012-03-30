<?php
/**
 * @page Edit Attribute
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$a = new Attributes;

// Brands
$b = new Brands();
$brands = $b->get_all();

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'edit-attribute' ) ) {
	
	// Parse attribute items
	if ( is_array( $_POST['hListItems'] ) )
	foreach ( $_POST['hListItems'] as $name ) {
		if ( !empty( $name ) )
			$attribute_list[] = $name;
	}
	
	// Create the attribute
	$attribute_id = $a->update( $_POST['hAttributeID'], $_POST['sBrandID'], $_POST['tAttributeTitle'], $_POST['tAttributeName'], $attribute_list );
	
	// If successfull, send to attributes
	if ( $attribute_id )
		url::redirect( '/attributes/' );
}

list( $attribute, $attribute_items ) = $a->get( $_GET['aid'] );

css( 'form', 'attributes/edit' );
javascript( 'jquery', 'jquery.tmp-val', 'attributes/edit' );

$selected = 'products';
$title = _('Edit Attribute') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Edit Attribute'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'attributes/' ); ?>
	<div id="subcontent">
		<p><?php echo _('Edit the attributes, you can drag to arrange the attribute values.'); ?></p>
		<p id="pAddAttribute" class="hidden"></p>
		<form action="/attributes/edit/?aid=<?php echo $_GET['aid']; ?>" name="fAddAttribute" id="fAddAttribute" method="post">
		<div class="row">
            <div class="cell" style="width:15%;"><label for="tAttributeTitle"><?php echo _('Attribute Title'); ?>:</label></div>
            <div class="cell"><input type="text" name="tAttributeTitle" id="tAttributeTitle" maxlength="250" class="tb" value="<?php echo $attribute['title']; ?>" /></div>
		</div>
		<div class="row">
            <div class="cell" style="width:15%;"><label for="tAttributeName"><?php echo _('Attribute Name'); ?>:</label></div>
            <div class="cell"><input type="text" name="tAttributeName" id="tAttributeName" maxlength="50" class="tb" value="<?php echo $attribute['name']; ?>" /></div>
		</div>
        <div class="row">
            <div class="cell" style="width:15%;"><label for="sBrandID"><?php echo _('Brand (Optional)'); ?>:</label></div>
            <div class="cell">
                <select name="sBrandID" id="sBrandID">
                    <option value="">-- <?php echo _('Select a Brand'); ?> --</option>
                    <?php
                    foreach ( $brands as $brand ) {
                        $selected = ( $attribute['brand_id'] == $brand['brand_id'] ) ? ' selected="selected"' : '';
                        ?>
                        <option value="<?php echo $brand['brand_id']; ?>"<?php echo $selected; ?>><?php echo $brand['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
		<br clear="left" /><br />
		<div class="row">
				<div class="cell" style="width:15%"><?php echo _('Attribute Items'); ?></div>
				<div class="cell">
					<div id="dItemsList">
					<?php
					if ( is_array( $attribute_items ) )
					foreach ( $attribute_items as $ai ) {
					?>
					<div id="dEditListItem_<?php echo format::slug( $ai['attribute_item_name'] ); ?>" class="list-item-container">
						<div class="list-item">
							<span class="list-item-name"><?php echo $ai['attribute_item_name']; ?></span>
							<input type="hidden" name="hListItems[]" value="<?php echo $ai['attribute_item_name']; ?>|<?php echo $ai['attribute_item_id']; ?>" />
                                        
							<div style="display:inline;float:right">
								<a href="javascript:;" class="edit-list-item" title='Edit "<?php echo $ai['attribute_item_name']; ?>" List Item'><img src="/images/icons/edit.png" class="edit-list-item" alt='Edit "<?php echo $ai['attribute_item_name']; ?>" List Item' width="15" height="17" /></a>
								<a href="javascript:;" class="delete-list-item" title='Delete "<?php echo $ai['attribute_item_name']; ?>" List Item'><img src="/images/icons/x.png" class="delete-list-item" alt='Delete "<?php echo $ai['attribute_item_name']; ?>" List Item' width="15" height="17" /></a>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
		</div>
		<div class="row">
				<div class="cell" style="width:15%;">&nbsp;</div>
				<div class="cell">
					<input type="text" id="tListItemValue" value="<?php echo _('Item Name'); ?>" class="tb" />
					<a href="javascript:;" id="aAddListItem" title="<?php echo _('Add Item'); ?>"><?php echo _('Add Item...'); ?></a>
				</div>
		</div>
		<br clear="left" /><br />
		<div class="row">
				<div class="cell" style="width:15%;">&nbsp;</div>
				<div class="cell"><input type="submit" class="button" value="<?php echo _('Save Attribute'); ?>" /></div>
		</div>
		<?php nonce::field ( 'edit-attribute', '_nonce' ); ?>
		<input type="hidden" name="hAttributeID" id="hAttributeID" value="<?php echo $attribute['attribute_id']; ?>" />
		</form>
		<br clear="all" />
	</div>
</div>

<?php get_footer(); ?>