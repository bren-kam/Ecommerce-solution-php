<?php
/**
 * @page Craigslist
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$c = new Craigslist();

$form = $_POST;

if ( $form ) {
	$publish = $form['hPublishConfirm'] == '1';
	
	if ( $form['hCraigslistAdId'] != '' ) {
		$result = $c->update( $form['hCraigslistAdId'], $form['hTemplateID'], $form['hProductId'], $user['website']['website_id'], $form['sChooseDays'], $form['tTitle'], $form['hCraigslistAdDescription'], 1, $publish );
		url::redirect('/craigslist/?m=2');
	} else {
		$result = $c->create( $form['hTemplateID'], $form['hProductId'], $user['website']['website_id'], $form['sChooseDays'], $form['tTitle'], $form['hCraigslistAdDescription'], 1, $publish );
		url::redirect('/craigslist/?m=1');
	}
}

$cid = ( isset( $_GET['cid'] ) ) ? $_GET['cid'] : false;

$ad = ( $cid ) ? $c->get( $cid ) : false;

$title = _('Craigslist Ads') . ' | ' . TITLE;

get_header();
javascript( 'mammoth', 'craigslist/add-edit', 'jquery.autocomplete' );
css( 'craigslist/add-edit', 'jquery.ui' );
// css( 'jquery.ui' );
css();
?>

<div id="content">
	<h1><?php echo _( ( $cid ) ? 'Edit Craigslist Ad' : 'Create Craigslist Ad' ); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'craigslist/' ); ?>
	<div id="subcontent">
		<form id="fAddCraigslistTemplate" method="post" action="#"/>
            <input id="hCraigslistAdId" name="hCraigslistAdId" type="hidden" value="<?php if ( $cid ) echo $cid;?>" />
            <input id="hCraigslistAdDescription" name="hCraigslistAdDescription" type="hidden" value="<?php echo ( $ad ) ? $ad['text'] : ''; ?>"/>
			<input id="hProductId" name="hProductId" type="hidden" value="<?php if ( isset( $_POST['product_id'] ) ) echo $_POST['product_id'];?>" />
            <input id="hProductName" name="hProductName" type="hidden" value="<?php echo ( $ad ) ? $ad['product_name'] : ''; ?>" />
            <input id="hProductCategoryId" type="hidden" value="0" />
            <input id="hProductCategoryName" type="hidden" value="" />
            <input id="hProductSKU" type="hidden" value="<?php echo ( $ad ) ? $ad['sku'] : ''; ?>" />
            <input id="hProductSpecs" type="hidden" value="0" />
            <input id="hProductBrandName" type="hidden" value="0" />
            <input id="hProductDescription" type="hidden" value="" />
            <input id="hStoreName" type="hidden" value="0" />
            <input id="hStoreURL" type="hidden" value="0" />
            <input id="hStoreLogo" type="hidden" value="( Logo )" />
            <input id="hPublishConfirm" name="hPublishConfirm" type="hidden" value="" />
            <input id="hPublishType" type="hidden" value="<?php if ( $ad ) echo "1"; else echo "0"; ?>" />
            <input id="hPublishConfirm" type="hidden" value="<?php if ( isset( $publish ) && $publish ) echo '1'; ?>" />
			<input id="hPublishStyle" type="hidden" value="<?php if ( $ad ) echo ( isset( $ad['craigslist_template_id'] ) && $ad['craigslist_template_id'] ) ? 'template' : 'custom'; ?>" />
            <input id="hTemplateID" name="hTemplateID" type="hidden" value="0" />
            <input id="hTemplateTitle" type="hidden" value="" />
            <input id="hTemplateIndex" type="hidden" value="1" />
            <input id="hTemplateCount" type="hidden" value="" />
            <input id="hTemplateDescription" type="hidden" value="" />
            <input id="hSearchType" type="hidden" value="sku" />
		                
			<br clear="left" /><br />
			<div id="dNarrowSearch" style="display:block;" >
				<?php nonce::field( 'craigslist' ); ?>
				<h2><?php echo _('Select Product');?></h2>
                <select id="sAutoComplete">
                    <option value="sku">SKU</option>
                    <option value="products">Product Name</option>
                </select>
				<input width="100" class="tb" type="text" name="tAutoComplete" id="tAutoComplete" tmpval="Enter SKU..." <?php echo ( $ad ) ? 'value="' . $ad['sku'] . '"' : ''; ?> />
				<a href="#" id="aSelect" title="Select" class="button">Select</a>
            </div>
    
            <div id="dItemDescription" style="display:none">
            </div>
            
            <div id="dProductPhotos" style="display:none">
            </div>
            
            <div id="dPreviewTemplate" style="display:none;">
	            <hr/>
                <h2>Select a Template&nbsp;</h2>
                
                <table style="width:100%; right:0px; left:auto;"><tr>
                    <td width="15"><a id="aPrevTemplate" title="previous" href="#"><</a></td>
                    <td width="40"><span id="dAdPaging"></span></td>
                    <td width="15"><a id="aNextTemplate" title="next" href="#">></a></td>
                    <td></td>
                    <td width="175">
                        <a href="#" id="aSelectTemplate" title="Select Ad Template" class="button">Select</a>
                        or <a href="#" id="aCreateAd" title="Create Your Own">Create your own</a>
                    </td>
                </tr></table>
                            
                <div id="dCraigslistPreview" style="margin:5px; padding:10px; border:1px solid #666;"></div>
                <br />
            </div>
            <br/><br/>
                    
			<div id="dCreateAd" style="display:<?php echo ( $ad ) ? 'block' : 'none';?>;">
	            <hr/>
                <h2>Create and Preview Ad</h2>
                <br/>
                <label for="tTitle"><?php echo _('Ad Title'); ?>:</label>
                <input class="tb" type="text" name="tTitle" width="500" id="tTitle" value="<?php echo ( $ad ) ? $ad['title'] : ''; ?>" />
                <br/><br/>
                <textarea name="taDescription" id="taDescription" rte="1"><?php echo ( $ad ) ? $ad['text'] : ''; ?></textarea>
                <p>
                    <strong>Syntax Tags:</strong> 
                    [Product Name]
                    [Store Name]
                    [Store Logo]
                    [Category]
                    [Brand] 
                    [Product Description]
                    <!--[Product Specs]-->
                    [SKU]
                    [Photo]
                    <!--[Attributes]-->
                </p>
                <br/>
                <a href="#" id="aPublish" title="Publish" class="button">Publish</a>
                <a href="/craigslist/" id="aCancel" title="Cancel">Cancel</a>
            </div>

            <div id="dPreviewAd" style="display:<?php echo ( $ad ) ? 'block' : 'none';?>;" >
                <br/><br/><br/>
                <h2>Preview - &nbsp;<small><a href="#" onclick="refreshPreview()" id="aRefresh" title="Refresh">Refresh</a></small></h2>
                <div id="dCraigslistCustomPreview" style="background-color:#FFF; border:1px solid #666; padding:10px;">
                    (Click "Refresh" above to preview your ad)
                </div>
                <br />
            </div>
            <div id="dGenerateHTML" style="display:<?php if ( isset( $publish ) && $publish ) echo "block"; else echo "none"; ?>;" >
                <h2>Paste this code into Craigslist:</h2>
                <br/>
                <textarea rows="20" style="width:600px;" id="dCraigslistPublish">
                </textarea>
                <br/><br/>
                <tr>
                    <td width="120">
                        Ad Duration:
                        <select id="sChooseDays" name="sChooseDays" >
                            <option value="-1">----</option>
                            <option value="3">3 Days</option>
                            <option value="5">5 Days</option>
                            <option value="7">1 Week</option>
                            <option value="14">2 Weeks</option>
                            <option value="21">3 Weeks</option>
                            <option value="28">1 Month</option>
                        </select>
                    </td>
                    <td></td>
                </tr>
                <tr height="10"></tr>
                <tr>
                    <td>
                        Click here to confirm ad is published.
                    </td>
                    <td>
                        <input type="submit" value="Confirm" class="button" />
                    </td>
                </tr>
            </div>
    	</form>
	</div>
</div>
<br/><br/>
<?php get_footer(); ?>