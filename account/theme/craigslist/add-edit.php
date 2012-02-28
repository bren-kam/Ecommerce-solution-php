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

// Initialize variable
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-craigslist' ) ) {
	$publish = '1' == $_POST['hPublishConfirm'];

    if ( empty ( $_POST['hCraigslistAdID'] ) ) {
        // Create ad
        $result = $c->create( $_POST['hProductID'], $user['website']['website_id'], $_POST['sChooseDays'], stripslashes( $_POST['tTitle'] ), stripslashes( $_POST['hCraigslistAdDescription'] ), 1, $publish );

        url::redirect('/craigslist/?m=1');
    } else {
        // Update Ad
        $result = $c->update( $_POST['hCraigslistAdID'], $_POST['hProductID'], $user['website']['website_id'], $_POST['sChooseDays'], stripslashes( $_POST['tTitle'] ), stripslashes( $_POST['hCraigslistAdDescription'] ), 1, $publish );

        url::redirect('/craigslist/?m=2');
    }
} else {
    $publish = 0;
}

// Get the category ID
$caid = ( isset( $_GET['caid'] ) ) ? $_GET['caid'] : false;

// Get the ad
$ad = ( $caid ) ? $c->get( $caid ) : false;

$title = _('Craigslist Ads') . ' | ' . TITLE;

add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );
javascript( 'mammoth', 'craigslist/add-edit', 'jquery.autocomplete' );
css( 'craigslist/add-edit', 'jquery.ui' );

get_header();
?>

<div id="content">
	<h1><?php echo ( $caid ) ? _('Edit Craigslist Ad') : _('Create Craigslist Ad'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'craigslist/' ); ?>
	<div id="subcontent">
		<form name="fAddCraigslistTemplate" id="fAddCraigslistTemplate" action="" method="post">
			<input id="hCraigslistAdID" name="hCraigslistAdID" type="hidden" value="<?php if ( $caid ) echo $caid; ?>" />
            <input id="hProductID" name="hProductID" type="hidden" value="<?php if ( isset( $_POST['hProductID'] ) ) echo $_POST['hProductID'];?>" />
            <input id="hCraigslistAdDescription" name="hCraigslistAdDescription" type="hidden" value="<?php if ( $ad ) echo $ad['text']; ?>"/>
			<input id="hProductName" name="hProductName" type="hidden" value="<?php if ( $ad ) echo $ad['product_name']; ?>" />
			<input id="hProductCategoryID" type="hidden" value="0" />
			<input id="hProductCategoryName" type="hidden" value="" />
			<input id="hProductSKU" type="hidden" value="<?php if ( $ad ) echo $ad['sku']; ?>" />
			<input id="hProductBrandName" type="hidden" value="0" />
			<input id="hProductDescription" type="hidden" value="" />
			<input id="hStoreName" type="hidden" value="0" />
			<input id="hStoreURL" type="hidden" value="0" />
			<input id="hStoreLogo" type="hidden" value="" />
			<input id="hPublishType" type="hidden" value="<?php echo ( $ad ) ? 1 : 0; ?>" />
			<input id="hPublishConfirm" type="hidden" value="<?php if ( $publish ) echo '1'; ?>" />

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
				<input type="text" class="tb" name="tAutoComplete" id="tAutoComplete" value="<?php if ( $ad ) echo $ad['sku']; ?>" tmpval="<?php echo _('Enter SKU'); ?>..." />
				<br /><br />
            </div>

            <div id="dProductPhotos" class="hidden"></div>
            
			<div id="dCreateAd" <?php if ( !$ad ) echo ' class="hidden"'; ?>>
                <h2><?php echo _('Create and Preview Ad'); ?></h2>
                <br />
                <label for="tTitle"><?php echo _('Ad Title'); ?>:</label>
                <input type="text" class="tb" name="tTitle" id="tTitle" value="<?php if ( isset( $ad ) ) echo $ad['title']; ?>" /> <a href="javascript:;" id="aRandomHeadline" title="<?php echo _('Random Title'); ?>" ajax="1"><?php echo _('Random Title'); ?></a>
                <br /><br />
                <textarea name="taDescription" id="taDescription" rte="1"><?php if ( $ad ) echo $ad['text']; ?></textarea>
                <p>
                    <strong><?php echo _('Syntax Tags'); ?>:</strong> 
                    [<?php echo _('Product Name'); ?>]
                    [<?php echo _('Store Name'); ?>]
                    [<?php echo _('Store Logo'); ?>]
                    [<?php echo _('Category'); ?>]
                    [<?php echo _('Brand'); ?>] 
                    [<?php echo _('Product Description'); ?>]
                    <!--[Product Specs]-->
                    [<?php echo _('SKU'); ?>]
                    [<?php echo _('Photo'); ?>]
                    <!--[Attributes]-->
                </p>
                <br />
                <input type="submit" class="button" value="<?php echo _('Save'); ?>" />
				<br /><br />
				<br />
            </div>

            <div id="dPreviewAd"<?php if ( !$ad ) echo ' class="hidden"'; ?>>
                <h2><?php echo _('Preview'); ?> - &nbsp;<small><a href="javascript:;" id="aRefresh" title="<?php echo _('Refresh'); ?>"><?php echo _('Refresh'); ?></a></small></h2>
                <div id="dCraigslistCustomPreview">
                    (<?php echo _('Click "Refresh" above to preview your ad'); ?>)
                </div>
                <br />
                <a href="javascript:;" class="button" id="aPublish" title="<?php echo _('Publish'); ?>"><?php echo _('Publish'); ?></a>
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