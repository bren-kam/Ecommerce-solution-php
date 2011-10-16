<?php
/**
 * @page Add Product Option
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	url::redirect( '/login/' );

$add = ( empty( $_GET['poid'] ) ) ? true : false;
$addEditTitle = ( $add ) ? _('Add') : _('Edit');

$po = new Product_Options;

if( !$add )
	$product_option = $po->get( $_GET['poid'] );

if( nonce::verify( $_POST['_nonce'], 'add-edit-product-option' ) ) {
	if( !isset( $_GET['poid'] ) || $_GET['poid'] == '' ) {
		// Get what type of option it is
		switch( $_POST['hForm'] ) {
			case 'fDropDown':
				$option_type = 'select';
	
				// Parse drop down items
				if( !empty( $_POST['hListItems'] ) ) {
					$options = explode( '|', stripslashes( $_POST['hListItems'] ) );
					
					foreach( $options as $o ) {
						if( !empty( $o ) )
							$list_items[] = $o;
					}
				}
				
				$type_nice_name = 'Drop Down List'; 
			break;
			
			case 'fCheckbox':
				$option_type = 'checkbox';
				$type_nice_name = 'Checkbox';
			break;
	
			case 'fText':
				$option_type = $_POST['tOptionSize_Text'];
				$type_nice_name = 'Text';
			break;
			
			default: break;
		}
		
		// Get the extension
		$ext = str_replace( 'f', '', $_POST['hForm'] );
	
		// Create it
		$po->create( $option_type, $_POST['tOptionTitle_' . $ext], $_POST['tOptionName_' . $ext], $list_items );
	// } elseif( nonce::verify( $_POST['_nonce'], 'update-product-option' ) ) {
	} elseif( isset( $_GET['poid'] ) ) {
		// Get what type of option it is
		switch( $_POST['hForm'] ) {
			case 'fDropDown':
				$option_type = 'select';
	
				// Parse drop down items
				if( !empty( $_POST['hListItems'] ) ) {
					$options = explode( '|', stripslashes( $_POST['hListItems'] ) );
					
					foreach( $options as $o ) {
						if( !empty( $o ) )
							$list_items[] = $o;
					}
				}
				
				$type_nice_name = 'Drop Down List'; 
			break;
			
			case 'fCheckbox':
				$option_type = 'checkbox';
				$type_nice_name = 'Checkbox';
			break;
	
			case 'fText':
				$option_type = $_POST['tOptionSize_Text'];
				$type_nice_name = 'Text';
			break;
			
			default: break;
		}
		
		// Get the extension
		$ext = str_replace( 'f', '', $_POST['hForm'] );

		$po->update( $option_type, $_POST['tOptionTitle_' . $ext], $_POST['tOptionName_' . $ext], $list_items, $_POST['hProductOptionID'] );
		// $po->update( $option_type, $option_title, $option_name, $list_items, $_POST['hProductOptionID'] );
		url::redirect( '/product-options/?edit=1' );
	}
}

css( 'form', 'product-options/add-edit' );
javascript( 'jquery', 'jquery.ui', 'jquery.tmp-val', 'product-options/add-edit' );

$selected = 'products';
$title = _('Add Product Option') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<p id="pOptionBreadCrumb">&nbsp;<?php echo ( $add ) ? '<span id="sOptionCurrentPage" class="hidden">' . _('Choose Option Type') . '</span>' : '<a href="javascript:;" id="aOptionPage_dAddOptionStep1" title="' . _('Choose Option Type') . '" class="option-breadcrumb">' . _('Choose Option Type') . '</a> &gt; <span id="sOptionCurrentPage">' . $type_nice_name . '</span>'; ?></p>
	<h1><?php echo $addEditTitle, _(' Product Option'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'product-options/' ); ?>
	<div id="subcontent">
		<?php
			if( !$add )
			switch( $product_option['option_type'] ) {
				case 'select':
					$type_nice_name = _('Drop Down List');
				break;
	
				case 'checkbox':
					$type_nice_name = _('Checkbox');
				break;
				
				case 'textarea':
				case 'text':
					$type_nice_name = _('Text');
				break;
				
				default: break;
			}
		?>
		<div id="dAddOptionStep1" class="page<?php if( !$add ) echo ' hidden'; ?>">
			<table cellpadding="0" cellspacing="0" style="margin: 0 auto;">
				<tr>
					<td width="20%" rowspan="3">&nbsp;</td>
					<td style="text-align:center;" width="20%"><a href="javascript:;" id="aChooseDropDown" class="choices" title="<?php echo _('Drop Down List'); ?>"><img src="/images/product-options/dropdown.jpg" alt="<?php echo _('Drop Down List'); ?>" width="134" height="134" style="padding-bottom:10px;" /></a></td>
					<td style="text-align:center;" width="20%"><a href="javascript:;" id="aChooseCheckbox" class="choices" title="<?php echo _('Checkbox'); ?>"><img src="/images/product-options/checkbox.jpg" alt="<?php echo _('Checkbox'); ?>" width="134" height="134" style="padding-bottom:10px;" /></a></td>
					<td style="text-align:center;" width="20%"><a href="javascript:;" id="aChooseText" class="choices" title="<?php echo _('Text'); ?>"><img src="/images/product-options/text.jpg" alt="<?php echo _('Text'); ?>" width="134" height="134" style="padding-bottom:10px;" /></a></td>
					<td width="20%" rowspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td style="text-align:center;"><a href="javascript:;" id="aChooseButtonDropDown" class="choices" title="<?php echo _('Drop Down List'); ?>"><img src="/images/buttons/add-big.gif" width="65" height="26" alt="<?php echo _('Add Drop Down List Option'); ?>" /></a></td>
					<td style="text-align:center;"><a href="javascript:;" id="aChooseButtonCheckbox" class="choices" title="<?php echo _('Checkbox'); ?>"><img src="/images/buttons/add-big.gif" width="65" height="26" alt="<?php echo _('Add Checkbox Option'); ?>" /></a></td>
					<td style="text-align:center;"><a href="javascript:;" id="aChooseButtonText" class="choices" title="<?php echo _('Text'); ?>"><img src="/images/buttons/add-big.gif" width="65" height="26" alt="<?php echo _('Add Text Option'); ?>" /></a></td>
				</tr>
				<tr>
					<td style="text-align:center;" valign="top">
						<h3><?php echo _('Drop Down List'); ?></h3>
						<p><?php echo _('This is an option that can be added to a product where the user would be able to select one of multiple options. Examples are Colors and Sizes.'); ?></p>
					</td>
					<td style="text-align:center;" valign="top">
						<h3><?php echo _('Checkbox'); ?></h3>
						<p><?php echo _('This is an option that can be added to a product for a yes/no type question. Example: Insurance.'); ?></p>
					</td>
					<td style="text-align:center;" valign="top">
						<h3><?php echo _('Text'); ?></h3>
						<p><?php echo _('This is an option that can be added if you want to give the user the ability to provide you with some information.'); ?></p>
					</td>
				</tr>
			</table>
		</div>
		<?php $dropdown = ( isset( $product_option ) && 'select' == $product_option['option_type'] ) ? true : false; ?>
		<div id="dOption_DropDown" class="page<?php if( $add || !$dropdown ) echo ' hidden'; ?>">
			<h3><?php echo _('Drop Down List'); ?></h3>
			<p id="pAddOption_DropDown" class="hidden"></p>
			<form action="/product-options/add-edit/?poid=<?php echo $_GET['poid']; ?>" name="fAddOption_DropDown" id="fAddOption_DropDown" method="post">
				<div class="row">
					<div class="cell" style="width:15%;"><label for="tOptionTitle_DropDown"><?php echo _('Drop Down Title'); ?>:</label></div>
					<div class="cell"><input type="text" class="tb" name="tOptionTitle_DropDown" id="tOptionTitle_DropDown" maxlength="50" value="<?php if( $dropdown ) echo $product_option['option_title']; ?>" /></div>
				</div>
				<div class="row">
					<div class="cell" style="width:15%;"><label for="tOptionName_DropDown"><?php echo _('Drop Down Name'); ?>:</label></div>
					<div class="cell"><input type="text" class="tb" name="tOptionName_DropDown" id="tOptionName_DropDown" maxlength="200" value="<?php if( $dropdown ) echo $product_option['option_name']; ?>" /></div>
				</div>
				<br /><br />
				<div class="row">
					<div class="cell" style="width:15%"><?php echo _('List Items'); ?></div>
					<div class="cell">
						<div id="dDropDownItemsList">
						<?php 
						$extra_count = ( isset( $product_option['extra'] ) ) ? count( $product_option['extra'] ) : 0;
						$list_items = '';
						
						if( $dropdown && $extra_count > 0 )
						foreach( $product_option['extra'] as $product_option_list_item_id => $li ) {
							$li_slug = format::slug( $li );
							
							$list_items .= "$product_option_list_item_id:$li|";
						?>
						<div extra="<?php echo $li_slug; ?>" id="dListItem_<?php echo $li_slug; ?>" class="list-item-container">
							<div class="list-item" id="dLI<?php echo $product_option_list_item_id; ?>">
								<span class="list-item-name"><?php echo $li; ?></span>
								<div style="display:inline;float:right">
									<a href="javascript:;" title='Edit "<?php echo $li; ?>" List Item' class="edit-list-item"><img class="edit-list-item" src="/images/icons/edit.png" alt='Edit "<?php echo $li; ?>" List Item' width="15" height="17" /></a>
									<a href="javascript:;" class="delete-list-item" title='Delete "<?php echo $li; ?>" List Item'><img class="delete-list-item" src="/images/icons/x.png" /></a>
								</div>
							</div>
						</div>
						<?php } ?>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="cell" style="width:15%;">&nbsp;</div>
					<div class="cell">
						<input type="text" class="tb" id="tListItemValue" maxlength="50" value="<?php echo _('Item Name'); ?>" />
						<a href="javascript:;" id="aAddListItem" title="<?php echo _('Add Item'); ?>"><?php echo _('Add Item...'); ?></a>
					</div>
				</div>
				<br /><br />
				<div class="row">
					<div class="cell" style="width:15%;">&nbsp;</div>
					<div class="cell"><input type="submit" class="button" value="<?php echo ( $add ) ? _('Add Option') : _('Save'); ?>" /></div>
				</div>
				<input type="hidden" name="hListItems" id="hListItems" value="<?php echo $list_items; ?>" />
				<?php if( !$add ) echo '<input type="hidden" name="hProductOptionID" value="' . $product_option['product_option_id'] . '" />'; ?>
				<input type="hidden" name="hForm" value="fDropDown" />
				<?php nonce::field( 'add-edit-product-option' ); ?>
			</form>
		</div>
		
		<?php $checkbox = ( isset( $product_option ) && 'checkbox' == $product_option['option_type'] ) ? true : false; ?>
		<div id="dOption_Checkbox" class="page<?php if( $add || !$checkbox ) echo ' hidden'; ?>">
			<h3><?php echo _('Checkbox'); ?></h3>
			<p id="pAddOption_Checkbox" class="hidden"></p>
			<form action="/product-options/add-edit/?poid=<?php echo $_GET['poid']; ?>" name="fAddOption_Checkbox" id="fAddOption_Checkbox" method="post">
				<div class="row">
					<div class="cell" style="width:15%;"><label for="tOptionTitle_Checkbox"><?php echo _('Checkbox Title'); ?>:</label></div>
					<div class="cell"><input type="text" class="tb" name="tOptionTitle_Checkbox" id="tOptionTitle_Checkbox" maxlength="50" value="<?php if( $checkbox ) echo $product_option['option_title']; ?>" /></div>
				</div>
				<div class="row">
					<div class="cell" style="width:15%;"><label for="tOptionName_Checkbox"><?php echo _('Checkbox Name'); ?>:</label></div>
					<div class="cell"><input type="text" class="tb" name="tOptionName_Checkbox" id="tOptionName_Checkbox" maxlength="200" value="<?php if( $checkbox ) echo $product_option['option_name']; ?>" /></div>
				</div>
				<br /><br />
				<div class="row">
					<div class="cell" style="width:15%;">&nbsp;</div>
					<div class="cell"><input type="submit" class="button" value="<?php echo ( $add ) ? _('Add Option') : _('Save'); ?>" /></div>
				</div>
				<?php if( !$add ) echo '<input type="hidden" name="hProductOptionID" value="' . $product_option['product_option_id'] . '" />'; ?>
				<input type="hidden" name="hForm" value="fCheckbox" />
				<?php nonce::field( 'add-edit-product-option' ); ?>
			</form>
		</div>
		
		<?php $text = ( isset( $product_option ) && 'text' == $product_option['option_type'] && 'textarea' != $product_option['option_type'] ) ? true : false; ?>
		<div id="dOption_Text" class="<?php if( $add || !$text ) echo ' hidden'; ?> page">
			<h3><?php echo _('Text'); ?></h3>
			<p id="pAddOption_Text" class="hidden"></p>
			<form action="/product-options/add-edit/?poid=<?php echo $_GET['poid']; ?>" name="fAddOption_Text" id="fAddOption_Text" method="post">
				<div class="row">
					<div class="cell" style="width:15%;"><label for="tOptionSize_Text"><?php echo _('Size'); ?>:</label></div>
					<div class="cell">
						<select name="tOptionSize_Text" id="tOptionSize_Text" class="dd">
							<option value="text"><?php echo _('One Line'); ?></option>
							<option value="textarea"<?php if( $text && 'textarea' == $product_option['option_type'] ) echo ' selected="selected"'; ?>><?php echo _('Multiple Lines'); ?></option>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="cell" style="width:15%;"><label for="tOptionTitle_Text"><?php echo _('Text Field Title'); ?>:</label></div>
					<div class="cell"><input type="text" class="tb" name="tOptionTitle_Text" id="tOptionTitle_Text" maxlength="50" value="<?php if( $text ) echo $product_option['option_title']; ?>" /></div>
				</div>						
				<div class="row">
					<div class="cell" style="width:15%;"><label for="tOptionName_Text"><?php echo _('Text Name'); ?>:</label></div>
					<div class="cell"><input type="text" class="tb" name="tOptionName_Text" id="tOptionName_Text" maxlength="200" value="<?php if( $text ) echo $product_option['option_name']; ?>" /></div>
				</div>
				<br /><br />
				<div class="row">
					<div class="cell" style="width:15%;">&nbsp;</div>
					<div class="cell"><input type="submit" class="button" value="<?php echo ( $add ) ? _('Add Option') : _('Save'); ?>" /></div>
				</div>
				<?php if( !$add ) echo '<input type="hidden" name="hProductOptionID" value="' . $product_option['product_option_id'] . '" />'; ?>
				<input type="hidden" name="hForm" value="fText" />
				<?php nonce::field( 'add-edit-product-option' ); ?>
			</form>
		</div>
		<br /><br />
		<br clear="all" />
	</div>
</div>

<?php get_footer(); ?>