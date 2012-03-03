<?php
/**
 * @page Add Attribute
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Brands
$b = new Brands();
$brands = $b->get_all();

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-attribute' ) ) {
	$a = new Attributes;
	
	// Parse attribute items
	if ( is_array( $_POST['hListItems'] ) ) {
		foreach ( $_POST['hListItems'] as $name ) {
			if ( !empty( $name ) )
				$attribute_list[] = $name;
		}
	}
	
	// Create the attribute
	$attribute_id = $a->create( $_POST['sBrandID'], $_POST['tAttributeTitle'], $_POST['tAttributeName'], $attribute_list );
	
	// If successfull, send to attributes
	if ( $attribute_id )
		url::redirect( '/attributes/' );
}



css( 'form', 'attributes/add' );
javascript( 'jquery', 'jquery.tmp-val', 'attributes/add' );

$selected = 'products';
$title = _('Add Attribute') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Add Attribute'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'attributes/' ); ?>
	<div id="subcontent">
		<p><?php echo _('Add the attributes, you can drag to arrange the attribute values.'); ?></p>
		<p id="pAddAttribute" class="hidden"></p>
		<form action="/attributes/add/" name="fAddAttribute" id="fAddAttribute" method="post">
		<div class="row">
            <div class="cell" style="width:15%;"><label for="tAttributeTitle"><?php echo _('Attribute Title'); ?>:</label></div>
            <div class="cell"><input type="text" name="tAttributeTitle" id="tAttributeTitle" maxlength="250" class="tb" /></div>
		</div>
		<div class="row">
            <div class="cell" style="width:15%;"><label for="tAttributeName"><?php echo _('Attribute Name'); ?>:</label></div>
            <div class="cell"><input type="text" name="tAttributeName" id="tAttributeName" maxlength="50" class="tb" /></div>
		</div>
		<div class="row">
				<div class="cell" style="width:15%;"><label for="sBrandID"><?php echo _('Brand (Optional)'); ?>:</label></div>
            <div class="cell">
                <select name="sBrandID" id="sBrandID">
                    <option value="">-- <?php echo _('Select a Brand'); ?> --</option>
                    <?php foreach ( $brands as $brand ) { ?>
                    <option value="<?php echo $brand['brand_id']; ?>"><?php echo $brand['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
		</div>
		<br clear="left" /><br />
		<div class="row">
				<div class="cell" style="width:15%"><?php echo _('Attribute Items'); ?></div>
				<div class="cell"><div id="dItemsList"></div></div>
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
				<div class="cell"><input type="submit" class="button" value="<?php echo _('Add Attribute'); ?>" /></div>
		</div>
		<?php nonce::field ( 'add-attribute', '_nonce' ); ?>
		</form>
		<br clear="all" />
	</div>
</div>

<?php get_footer(); ?>