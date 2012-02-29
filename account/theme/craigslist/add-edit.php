<?php
/**
 * @page Craigslist
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	login();

$c = new Craigslist();

// Get the Craigslist Ad Id
$craigslist_ad_id = ( isset( $_GET['caid'] ) ) ? $_GET['caid'] : false;

// Initialize variable
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-craigslist' ) ) {
	$post = '1' == $_POST['hPostAd'];

    if ( empty ( $_POST['hCraigslistAdID'] ) ) {
        // Create ad
        $success = $c->create( $_POST['hProductID'], stripslashes( $_POST['tTitle'] ), stripslashes( $_POST['taDescription'] ), $_POST['tPrice'], $post );
    } else {
        // Update Ad
        $success = $c->update( $_POST['hCraigslistAdID'], $_POST['hProductID'], stripslashes( $_POST['tTitle'] ), stripslashes( $_POST['taDescription'] ), $_POST['tPrice'], $post );
    }
}

// Get the email if necessary
if ( $craigslist_ad_id ) {
	$ad = $c->get( $craigslist_ad_id );
} else {
	// Initialize variable
	$ad = array(
		'product_id' => ''
		, 'title' => ''
		, 'text' => ''
		, 'price' => ''
		, 'product_name' => ''
        , 'sku'
	);
}

$title = _('Craigslist Ads') . ' | ' . TITLE;

add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );
javascript( 'mammoth', 'craigslist/add-edit', 'jquery.autocomplete' );
css( 'craigslist/add-edit', 'jquery.ui' );

get_header();
?>

<div id="content">
	<h1><?php echo ( $craigslist_ad_id ) ? _('Edit Craigslist Ad') : _('Create Craigslist Ad'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'craigslist/' ); ?>
	<div id="subcontent">
        <?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo ( $craigslist_ad_id ) ? _('Your Craigslist Ad has been updated successfully!') : _('Your Craigslist Ad has been created successfully!'); ?></p>
			<p><?php echo _('Click here to'), ' <a href="/craigslist/" title="', _('Craigslist Ads'), '">', _('view your Craigslist ads'), '</a>.'; ?></p>
		</div>
		<?php } ?>
		<form name="fAddCraigslistTemplate" id="fAddCraigslistTemplate" action="" method="post">
			<input id="hCraigslistAdID" name="hCraigslistAdID" type="hidden" value="<?php if ( $craigslist_ad_id ) echo $craigslist_ad_id; ?>" />
            <input id="hProductID" name="hProductID" type="hidden" value="<?php echo ( !$success && isset( $_POST['hProductID'] ) ) ? $_POST['hProductID'] : $ad['product_id'];?>" />
            <input id="hCraigslistAdDescription" name="hCraigslistAdDescription" type="hidden" value="<?php echo ( !$success && isset( $_POST['hCraigslistAdDescription'] ) ) ? $_POST['hCraigslistAdDescription'] : $ad['text'];?>"/>
			<input id="hProductName" name="hProductName" type="hidden" value="<?php echo ( !$success && isset( $_POST['hProductName'] ) ) ? $_POST['hProductName'] : $ad['product_name']; ?>" />
			<input id="hProductCategoryID" type="hidden" value="0" />
			<input id="hProductCategoryName" type="hidden" value="" />
			<input id="hProductSKU" type="hidden" value="<?php echo ( !$success && isset( $_POST['hProductSKU'] ) ) ? $_POST['hProductSKU'] : $ad['sku']; ?>" />
			<input id="hProductBrandName" type="hidden" value="0" />
			<input id="hProductDescription" type="hidden" value="" />
			<input id="hStoreName" type="hidden" value="<?php echo $user['website']['title']; ?>" />
			<input id="hStoreURL" type="hidden" value="<?php echo 'http://', $user['website']['domain']; ?>" />
			<input id="hStoreLogo" type="hidden" value="<?php echo $user['website']['logo']; ?>" />
			<input id="hPostAd" type="hidden" value="0" />

			<div id="dNarrowSearch">
				<?php 
				nonce::field( 'products-autocomplete', '_ajax_autocomplete' ); 
				nonce::field( 'set-product', '_ajax_set_product' );
				?>
				<h2><?php echo _('Select Product');?></h2>
                <select id="sAutoComplete">
                    <option value="sku"><?php echo _('SKU'); ?></option>
                    <option value="product"><?php echo _('Product Name'); ?></option>
                </select>
				<input type="text" class="tb" name="tAutoComplete" id="tAutoComplete" value="<?php if ( $success || !isset( $_POST['hProductSKU'] ) ) echo $ad['sku']; ?>" tmpval="<?php echo _('Enter SKU'); ?>..." />
				<br /><br />
            </div>

            <div id="dProductPhotos" class="hidden"></div>
            
			<div id="dCreateAd" <?php if ( !$craigslist_ad_id ) echo ' class="hidden"'; ?>>
                <h2><?php echo _('Create and Preview Ad'); ?></h2>
                <br />
                <label for="tTitle"><?php echo _('Ad Title'); ?>:</label>
                <input type="text" class="tb" name="tTitle" id="tTitle" value="<?php echo ( !$success && isset( $_POST['tTitle'] ) ) ? $_POST['tTitle'] : $ad['title']; ?>" /> <a href="javascript:;" id="aRandomHeadline" title="<?php echo _('Random Title'); ?>" ajax="1"><?php echo _('Random Title'); ?></a>
                <br /><br />
                <textarea name="taDescription" id="taDescription" rte="1"><?php echo ( !$success && isset( $_POST['taDescription'] ) ) ? $_POST['taDescription'] : $ad['text']; ?></textarea>
                <p>
                    <strong><?php echo _('Syntax Tags'); ?>:</strong> 
                    [<?php echo _('Product Name'); ?>]
                    [<?php echo _('Store Name'); ?>]
                    [<?php echo _('Store Logo'); ?>]
                    [<?php echo _('Category'); ?>]
                    [<?php echo _('Brand'); ?>] 
                    [<?php echo _('Product Description'); ?>]
                    [<?php echo _('SKU'); ?>]
                    [<?php echo _('Photo'); ?>]
                </p>
                <label for="tPrice"><?php echo _('Price'); ?>:</label>
                <input type="text" class="tb" name="tPrice" id="tPrice" value="<?php echo ( !$success && isset( $_POST['tPrice'] ) ) ? $_POST['tPrice'] : $ad['price']; ?>" />
                <br /><br />
                <input type="submit" class="button" value="<?php echo _('Save'); ?>" />
				<br /><br />
				<br />
            </div>

            <div id="dPreviewAd"<?php if ( !$craigslist_ad_id ) echo ' class="hidden"'; ?>>
                <h2><?php echo _('Preview'); ?> - &nbsp;<small><a href="javascript:;" id="aRefresh" title="<?php echo _('Refresh'); ?>"><?php echo _('Refresh'); ?></a></small></h2>
                <div id="dCraigslistCustomPreview">
                    (<?php echo _('Click "Refresh" above to preview your ad'); ?>)
                </div>
                <br />
                <a href="javascript:;" class="button" id="aPostAd" title="<?php echo _('Post Ad'); ?>"><?php echo _('Post Ad'); ?></a>
            </div>
			
            <?php nonce::field('add-edit-craigslist'); ?>
    	</form>
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
	</div>
</div>
<br /><br />
<?php get_footer(); ?>