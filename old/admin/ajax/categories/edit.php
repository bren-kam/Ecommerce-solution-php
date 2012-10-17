<?php
css( 'categories/mini', 'form' );
javascript( 'jquery', 'jquery.ui', 'jquery.form', 'categories/mini' );

$c = new Categories;
$a = new Attributes;

$cat = $c->get_category( $_GET['cid'] );
$category_id = $_GET['cid'];
$category_attributes = $a->get_category_attributes( $category_id );

$categories = $c->get_list( $cat['parent_category_id'], 0, 0, $_GET['cid'] );
$attributes = $a->get_attributes();

// Encoded data to get css
list( $css, $ie8 ) = get_css();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo _('Add Category'); ?></title>
<link type="text/css" rel="stylesheet" href="/css/?files=<?php echo $css; ?>" />
</head>
<body>
<div id="main-content" class="clear">
	<div class="general_form">
	<form id="frEdit" name="frEdit"action="/ajax/categories/update/" method="post">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td><label for="tName"><?php echo _('Name'); ?>:</label></td>
			<td><input type="text" id="tName" name="tName" value="<?php echo $cat['name']; ?>" class="tb" /></td>
		</tr>
		<tr>
			<td><label for="sParentCategory"><?php echo _('Parent Category'); ?>:</label></td>
			<td>
				<select id="sParentCategory" name="sParentCategory" class="dd">
					<option value="">-- <?php echo _('Select a Category'); ?> --</option>
					<?php echo $categories; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="tSlug"><?php echo _('Category URL'); ?>:</label></td>
			<td><input type="text" id="tSlug" name="tSlug" value="<?php echo $cat['slug']; ?>" class="tb" /></td>
		</tr>
		<tr>
			<td><label for="sAttributes"><?php echo _('Attributes'); ?>:</label></td>
			<td>
				<div id="dAttributeList">
				<?php foreach ( $category_attributes as $ca ) { ?>
				<div extra="<?php echo format::slug( $attributes[$ca]['title'] ); ?>" id="dAttribute<?php echo $ca; ?>" class="attribute-container">
					<div class="attribute">
						<span class="attribute-name"><?php echo $attributes[$ca]['title']; ?></span>
						<div style="display:inline;float:right"><a href="javascript:;" class="delete-attribute" title='Delete "<?php echo $attributes[$ca]['title']; ?>" Attribute'><img src="/images/icons/x.png" class="delete-attribute" width="15" height="17" /></a></div>
					</div>
				</div>
				<?php } ?>
				</div>
				<select id="sAttributes" name="sAttributes" class="dd">
					<option value="">-- <?php echo _('Select an Attribute'); ?> --</option>
					<?php foreach ( $attributes as $a ) { ?>
					<option value="<?php echo $a['attribute_id']; ?>"><?php echo $a['title']; ?></option>
					<?php } ?>
				</select><br />
				<p style="float:right;"><a href="javascript:;" id="aAddAttribute" title="<?php echo _('Add Attribute'); ?>"><?php echo _('Add'); ?></a></p>
				<input type="hidden" name="hAttributes" id="hAttributes" />
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" class="button" value="<?php echo _('Save Category'); ?>" style="width: auto" /></td>
		</tr>
	</table>
	<br />
	<input type="hidden" name="hCategoryID" id="hCategoryID" value="<?php echo $cat['category_id']; ?>" />
	<?php nonce::field( 'update-category' ); ?>
	</form>
	</div>
	
	<div id="dInfo" style="display:none;"><?php echo _('Information is updated. You may close this dialog now.'); ?></div>
</div>
   <!-- End: Content -->
<?php 
$javascript = get_js();
if ( 'eNpLtDKwqq4FXDAGTwH-' != $javascript ) { // That string means it's empty ?>
<script type="text/javascript" src="/js/?files=<?php echo $javascript; ?>"></script>
<?php 
}

footer();
?>
</body>
</html>