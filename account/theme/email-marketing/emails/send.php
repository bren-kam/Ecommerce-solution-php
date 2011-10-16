<?php
/**
 * @page Send Email - Part 1
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Redirect to main section if they don't have email marketing
if( !$user['website']['email_marketing'] )
	url::redirect('/email-marketing/subscribers/');

$e = new Email_Marketing;
$email_lists = $e->get_email_lists( true );
$em = $e->get_email_message( $_GET['emid'] );
$timezone = $e->get_setting('timezone');

// Set the meta
if ( 'offer' == $em['type'] ) {
	$offer_1 = explode( '|', $em['meta']['offer_1'] );
	$offer_2 = explode( '|', $em['meta']['offer_2'] );
	
	if( !empty( $offer_1[1] ) || !empty( $offer_2[1] ) ) {
		$p = new Products;
		
		if( !empty( $offer_1[1] ) )
			$offer_1['product'] = $p->get_product( $offer_1[1] );

		if( !empty( $offer_2[1] ) )
			$offer_2['product'] = $p->get_product( $offer_2[1] );
	}
}

css( 'email-marketing/emails/send', 'jquery.timepicker' );
javascript( 'mammoth', 'jquery.datatables', 'jquery.timepicker', 'jquery.jcarousel-lite', 'jquery.blockUI', 'email-marketing/emails/send' );

// Load the jQuery UI CSS
add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );

$selected = "email_marketing";
$title = _('Send Email - Part 1 | Email Marketing') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Send Email'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'email-marketing/', 'send_email' ); ?>
	<div id="subcontent">
		<div id="tab-top">
			<h2 class="tab selected" id="h2Step1"><a href="javascript:;" id="aStep1" class="step" title="<?php echo _('Step 1'); ?>"><?php echo _('Step 1'); ?></a></h2>
			<h2 class="tab" id="h2Step2"><a href="javascript:;" id="aStep2" class="step" title="<?php echo _('Step 2'); ?>"><?php echo _('Step 2'); ?></a></h2>
			<h2 class="tab" id="h2Step3"><a href="javascript:;" id="aStep3" class="step" title="<?php echo _('Step 3'); ?>"><?php echo _('Step 3'); ?></a></h2>
		</div>
		<div id="dMainContent">
			<form name="fSendEmail" id="fSendEmail" action="/ajax/email-marketing/emails/save/" method="post">
			<div id="dStep1">
				<h2><?php echo _('Basic Email Information'); ?></h2>
				<br />
				<?php
				if( !empty( $em['date_sent'] ) ) {
					// Adjust for timezone
					$em['date_sent'] = date_time::date( 'Y-m-d H:i:s', strtotime( $em['date_sent'] ) + $timezone * 3600 + 18000 );

					list( $date, $time ) = explode( ' ', $em['date_sent'] );
				}

				?>
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td><label for="tSubject"><?php echo _('Subject'); ?>:</label></td>
						<td colspan="3"><input type="text" class="tb" name="tSubject" id="tSubject" maxlength="50" value="<?php echo $em['subject']; ?>" /></td>
					</tr>
					<tr>
						<td><label for="tDate"><?php echo _('Send Date'); ?>:</label></td>
						<td><input type="text" class="tb" name="tDate" id="tDate" value="<?php echo ( empty( $date ) ) ? date_time::date('Y-m-d', time() - ( 3600 * $timezone ) - 18000 ) : $date; ?>" maxlength="10" /></td>
						<td><label for="tTime"><?php echo _('Time'); ?></label>:</td>
						<td><input type="text" class="tb" name="tTime" id="tTime" style="width: 75px;" value="<?php echo ( empty( $time ) ) ? date_time::date('h:i a', time() - ( 3600 * $timezone ) - 18000 ) : date_time::date( 'h:i a', strtotime( $time ) ); ?>" maxlength="8" /></td>
					</tr>
					<tr>
						<td valign="top"><label><?php echo _('Mailing List(s)'); ?>:</label></td>
						<td>
							<a href="javascript:;" id="aCheckAll" title="<?php echo _('Check All'); ?>"><?php echo _('Check All'); ?></a> | <a href="javascript:;" id="aUncheckAll" title="<?php echo _('Uncheck All'); ?>"><?php echo _('Uncheck All'); ?></a>
							<br /><br />
							<?php
							$options = '';
							if( $em['email_lists'] )
								$email_list_ids = array_keys( $em['email_lists'] );
							
							foreach( $email_lists as $el ) {
								$disabled = ( 0 == $el['count'] ) ? ' disabled="disabled"' : '';
								$checked = ( $email_list_ids && in_array( $el['email_list_id'], $email_list_ids ) ) ? ' checked="checked"' : '';
								
								if( 0 == $el['category_id'] ) {
									$options = '<p><input type="checkbox" class="cb mailing-list" id="cbMailingList' . $el['email_list_id'] . '" name="email_lists[]" value="' . $el['email_list_id'] . '"' . $checked . $disabled . ' /> <label for="cbMailingList' . $el['email_list_id'] . '">' . $el['name'] . ' (' . _('Subscribers') . ': ' . $el['count'] . ')</label></p>' . $options;
								} else {
									$options .= '<p><input type="checkbox" class="cb mailing-list" id="cbMailingList' . $el['email_list_id'] . '" name="email_lists[]" value="' . $el['email_list_id'] . '"' . $checked . $disabled . ' /> <label for="cbMailingList' . $el['email_list_id'] . '">' . $el['name'] . ' (' . _('Subscribers') . ': ' . $el['count'] . ')</label></p>';
								}
							}
							
							echo $options;
							?>
						</td>
					</tr>
				</table>
				<p><a href="javascript:;" id="aNextStep2" class="next button" title="<?php echo _('Next'); ?>"><?php echo _('Next'); ?></a></p>
			</div>
			<div class="hidden" id="dStep2" style="position:relative">
				<h2 class="col-2"><?php echo _('Choose Email Type'); ?></h2>
				<p class="col-2 text-right" style="position: absolute; right: 10px; top: 15px"><a href="javascript:;" id="aPreviousStep1" class="previous button" title="<?php echo _('Previous'); ?>"><?php echo _('Previous'); ?></a> <a href="javascript:;" class="save button" title="<?php echo _('Save'); ?>"><?php echo _('Save'); ?></a></p>
				<br />
				<div id="dChooseType" style="height: 170px">
					<?php if( $user['company_id'] != 4 ) { ?>
					<div class="choose-div">
						<a href="javascript:;" id="aOffer" title="<?php echo _('Offer Email'); ?>" class="choose">
							<img src="/images/icons/email-marketing/emails/email-offer.gif" width="162" height="131" alt="<?php echo _('Offer Email'); ?>" />
							<br />
							<?php echo _('Offer'); ?>
						</a>
					</div>
					<?php } ?>
					<div class="choose-div">
						<a href="javascript:;" id="aProduct" title="<?php echo _('Product Email'); ?>" class="choose">
							<img src="/images/icons/email-marketing/emails/email-product.gif" width="162" height="131" alt="<?php echo _('Product Email'); ?>" />
							<br />
							<?php echo _('Product'); ?>
						</a>
					</div>
					<div class="choose-div">
						<a href="javascript:;" id="aCustom" title="<?php echo _('Custom Email'); ?>" class="choose">
							<img src="/images/icons/email-marketing/emails/email-custom.gif" width="162" height="131" alt="<?php echo _('Custom Email'); ?>" />
							<br />
							<?php echo _('Custom'); ?>
						</a>
					</div>
				</div>
				<div id="dOffer" class="hidden email-type">
					<div class="slider">
						<ul id="ulSlider_offer" style="height:400px;width:100px">
						</ul>
					</div>
					<div class="template-image"></div>
					<br clear="all" />
				</div>
				<div id="dProduct" class="hidden email-type">
					<div class="slider">
						<ul id="ulSlider_product" style="height:400px;width:100px">
						</ul>
					</div>
					<div class="template-image"></div>
					<br clear="all" />
				</div>
				<div id="dCustom" class="hidden email-type">
					<div class="slider">
						<ul id="ulSlider_custom" style="height:400px;width:100px">
						</ul>
					</div>
					<div class="template-image"></div>
					<br clear="all" />
				</div>
				<input type="hidden" name="hEmailType" id="hEmailType" value="<?php if( !empty( $em['type'] ) ) echo $em['type']; ?>" />
				<input type="hidden" name="hEmailTemplateID" id="hEmailTemplateID" value="<?php if( !empty( $em['email_template_id'] ) ) echo $em['email_template_id']; ?>" />
			</div>
			<div class="hidden" id="dStep3" style="position:relative">
				<h2 class="col-2"><?php echo _('Email Content'); ?></h2>
				<p class="col-2 text-right" style="position: absolute; right: 10px; top: 5px"><a href="javascript:;" id="aPreviousStep2" class="previous button" title="<?php echo _('Previous'); ?>"><?php echo _('Previous'); ?></a> <a href="javascript:;" class="save button" title="<?php echo _('Save'); ?>"><?php echo _('Save'); ?></a></p>
				<br />
				<textarea name="taMessage" id="taMessage" cols="50" rows="3" rte="1"><?php echo $em['message']; ?></textarea>
				<br />
				<p><a href="http://www.ftc.gov/bcp/edu/pubs/business/ecommerce/bus61.shtm" target="_blank" title="<?php echo _('The CAN-SPAM Act'); ?>"><?php echo _('The CAN-SPAM Act'); ?></a></p>
				<br />
				<div id="dCustom_offer" class="<?php echo ( !empty( $em['type'] ) && 'offer' == $em['type'] ) ? '' : 'hidden'; ?> custom-template">
					<h2><?php echo _('Offer Boxes'); ?></h2>
					<table cellspacing="0">
						<tr>
							<td width="245" valign="top">
								<select name="sBox1" id="sBox1" class="select-box-type">
									<option value=""><?php echo _('Select Left Box Type'); ?></option>
									<option value="text"<?php if( !empty( $offer_1 ) && 'text' == $offer_1[0] ) echo ' selected="selected"'; ?>><?php echo _('Text'); ?></option>
									<option value="product"<?php if( !empty( $offer_1 ) && 'product' == $offer_1[0] ) echo ' selected="selected"'; ?>><?php echo _('Product'); ?></option>
								</select>
								<br /><br />
								<div id="dTextBox1"<?php if( empty( $offer_1 ) || 'text' != $offer_1[0] ) echo ' class="hidden"'; ?>>
									<textarea cols="33" rows="3" name="taBox1" id="taBox1"><?php if( !empty( $offer_1 ) && 'text' == $offer_1[0] ) echo $offer_1[1]; ?></textarea>
								</div>
								<div id="dProductBox1"<?php if( empty( $offer_1 ) || 'product' != $offer_1[0] ) echo ' class="hidden"'; ?>>
									<input type="text" class="tb" name="tAutoSuggestBox1" id="tAutoSuggestBox1" tmpval="<?php echo _('Product Name / SKU'); ?>" />
									<div id="dProductContainerBox1" class="product-container">
										<?php 
										if( !empty( $offer_1['product'] ) ) { 
											$product_image = 'http://' . $offer_1['product']['industry'] . '.retailcatalog.us/products/' . $offer_1['product']['product_id'] . '/' . $offer_1['product']['image'];
										?>
										<div id="dProduct_<?php echo $offer_1[1]; ?>" class="product">
											<h4><?php echo $offer_1['product']['name']; ?></h4>
											<p align="center"><img src="<?php echo $product_image; ?>" alt="<?php echo $offer_1['product']['name']; ?>" height="110" style="margin:10px" /></p>
											<p>
												<?php echo _('Brand'); ?>: <?php echo $offer_1['product']['brand']; ?><br />
												<label for="tProductPrice<?php echo $offer_1[1]; ?>"><?php echo _('Price'); ?>:</label> 
												<input type="text" class="tb product-box-price" name="tProductPrice<?php echo $offer_1[1]; ?>" id="tProductPrice<?php echo $offer_1[1]; ?>" value="<?php echo $offer_1[2]; ?>" maxlength="10" />
											</p>
											<p class="product-actions" id="pProductAction<?php echo $offer_1[1]; ?>"><a href="javascript:;" class="remove-box-product" title="<?php echo _('Remove Product'); ?>"><?php echo _('Remove'); ?></a></p>
										</div>
										<input type="hidden" name="hProductBox1" id="hProductBox1" value="<?php echo $offer_1[1]; ?>|<?php echo $offer_1[2]; ?>" />
										<?php } ?>
									</div>
								</div>
							</td>
							<td width="40">&nbsp;</td>
							<td width="245" valign="top">
								<select name="sBox2" id="sBox2" class="select-box-type">
									<option value=""><?php echo _('Select Right Box Type'); ?></option>
									<option value="text"<?php if( isset( $offer_2 ) && 'text' == $offer_2[0] ) echo ' selected="selected"'; ?>><?php echo _('Text'); ?></option>
									<option value="product"<?php if( isset( $offer_2 ) && 'product' == $offer_2[0] ) echo ' selected="selected"'; ?>><?php echo _('Product'); ?></option>
								</select>
								<br /><br />
								<div id="dTextBox2"<?php if( empty( $offer_2 ) || 'text' != $offer_2[0] ) echo ' class="hidden"'; ?>>
									<textarea cols="33" rows="3" name="taBox2" id="taBox2"><?php if( !empty( $offer_2 ) && 'text' == $offer_2[0] ) echo $offer_2[1]; ?></textarea>
								</div>
								<div id="dProductBox2"<?php if( empty( $offer_2 ) || 'product' != $offer_2[0] ) echo ' class="hidden"'; ?>>
									<input type="text" class="tb" name="tAutoSuggestBox2" id="tAutoSuggestBox2" tmpval="<?php echo _('Product Name / SKU'); ?>" />
									<div id="dProductContainerBox2" class="product-container">
										<?php 
										if( !empty( $offer_2['product'] ) ) { 
											$product_image = 'http://' . $offer_2['product']['industry'] . '.retailcatalog.us/products/' . $offer_2['product']['product_id'] . '/' . $offer_2['product']['image'];
										?>
										<div id="dProduct_<?php echo $offer_2[1]; ?>" class="product">
											<h4><?php echo $offer_2['product']['name']; ?></h4>
											<p align="center"><img src="<?php echo $product_image; ?>" alt="<?php echo $offer_2['product']['name']; ?>" height="110" style="margin:10px" /></p>
											<p>
												<?php echo _('Brand'); ?>: <?php echo $offer_2['product']['brand']; ?><br />
												<label for="tProductPrice<?php echo $offer_2[1]; ?>"><?php echo _('Price'); ?>:</label> 
												<input type="text" name="tProductPrice<?php echo $offer_2[1]; ?>" class="tb product-box-price" id="tProductPrice<?php echo $offer_2[1]; ?>" value="<?php echo $offer_2[2]; ?>" maxlength="10" />
											</p>
											<p class="product-actions" id="pProductAction<?php echo $offer_2[1]; ?>"><a href="javascript:;" class="remove-box-product" title="<?php echo _('Remove'), ' ', $offer_2['product']['name']; ?>"><?php echo _('Remove'); ?></a></p>
										</div>
										<input type="hidden" name="hProductBox2" id="hProductBox2" value="<?php echo $offer_2[1], '|', $offer_2[2]; ?>" />
										<?php } ?>
									</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div id="dCustom_product" class="custom-template<?php if( empty( $em['type'] ) || 'product' != $em['type'] ) echo ' hidden'; ?>">
					<br /><br />
					
					<h2 id="h2ProductCount"><?php echo _('Products Chosen'), ': '; echo ( isset( $em['type'] ) && 'product' == $em['type'] ) ? count( $em['meta'] ) : '0'; ?>/9</h2>
					<h2 style="float:right;width:50%"><?php echo _('Products'); ?></h2>
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
						if( isset( $em['meta'] ) && 'product' == $em['type'] ) {
							$meta = array();
							foreach( $em['meta'] as $product ) {
								$meta[$product['order']] = $product;
							}
							
							ksort( $meta );
						
							foreach( $meta as $product ) {
								$product_image = 'http://' . $product['industry'] . '.retailcatalog.us/products/' . $product['product_id'] . '/' . $product['image'];
								?>
								<div id="dProduct_<?php echo $product['product_id']; ?>" class="product">
									<h4><?php echo $product['name']; ?></h4>
									<p align="center"><img src="<?php echo $product_image; ?>" alt="<?php echo $product['name']; ?>" height="110" style="margin:10px" /></p>
									<p>
										<?php echo _('Brand'), ': ', $product['brand']; ?><br />
										<label for="tProductPrice<?php echo $product['product_id']; ?>"><?php echo _('Price'); ?>:</label> 
										<input type="text" class="tb product-price" name="tProductPrice<?php echo $product['product_id']; ?>" id="tProductPrice<?php echo $product['product_id']; ?>" value="<?php echo $product['price']; ?>" maxlength="10" />
									</p>
									<p class="product-actions" id="pProductAction<?php echo $product['product_id']; ?>"><a href="javascript:;" class="remove-product" title="<?php echo _('Remove Product'); ?>"><?php echo _('Remove'); ?></a></p>
									<input type="hidden" name="products[]" class="hidden-product" id="hProduct<?php echo $product['product_id']; ?>" value="<?php echo $product['product_id'], '|', $product['price']; ?>" />
								</div>
							<?php 
							} 
						}
						?>
					</div>
					<br clear="all" />
				</div>
				<br />
				
				<p style="padding-bottom:0"><a href="javascript:;" id="aSendTest" title="<?php echo _('Send Test'); ?>"><?php echo _('Send Test'); ?> [ + ]</a></p>
				<div id="dSendTest" class="hidden">
					<p id="pSuccessMessage" class="hidden"><?php echo _('Please check your email in a minute or two for the test email.'); ?></p>
					<input type="text" class="tb" id="tTestEmail" maxlength="200" tmpval="<?php echo _('Test email...'); ?>" /> <input type="button" id="bSendTest" class="button" value="<?php echo _('Send Test'); ?>" error="<?php echo _('Please enter a valid test email, then try again.'); ?>" />
					<br />
				</div>
				<br />
				<a href="javascript:;" class="button" id="aSendEmail" title="<?php echo _('Send Email'); ?>" error="<?php echo _('Please save and test your email before you send it'); ?>"><?php echo _('Send Email'); ?></a>
			</div>
			<input type="hidden" name="hEmailMessageID" id="hEmailMessageID" value="<?php echo ( $em['email_message_id'] ) ? $em['email_message_id'] : '0'; ?>" />
			<?php nonce::field( 'save-email' ); ?>
			</form>
			<?php
			// Do not need to be submitted with the form, simply have to be on the page
			nonce::field( 'test-message', '_ajax_test_message' );
			nonce::field( 'products-autocomplete', '_ajax_autocomplete' );
			nonce::field( 'offer-box', '_ajax_offer_box' );
			nonce::field( 'delete-product', '_ajax_delete_product' );
			nonce::field( 'schedule-email', '_ajax_schedule_email' );
			nonce::field( 'get-templates', '_ajax_get_templates' );
			nonce::field( 'search', '_ajax_search' );
			?>
		</div>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>