<?php
css( 'categories/mini', 'form' );
javascript( 'jquery', 'jquery.ui', 'jquery.form', 'categories/mini' );

$c = new Categories;
$a = new Attributes;

$categories = $c->get_list();
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
	<form id="frAdd" name="frAdd"action="/ajax/categories/insert/" method="post">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td><label for="tName"><?php echo _('Name'); ?>:</label></td>
			<td><input type="text" id="tName" name="tName" value="" class="tb" /></td>
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
			<td><input type="text" id="tSlug" name="tSlug" value="" class="tb" /></td>
		</tr>
		<tr>
			<td><label for="tTags"><?php echo _('Tags'); ?>:</label></td>
			<td><input type="text" name="tTags" id="tTags" value="" class="tb" /></td>
		</tr>
		<tr>
			<td><label for="sAttributes"><?php echo _('Attributes'); ?>:</label></td>
			<td>
				<div id="dAttributeList"></div>
				<select id="sAttributes" name="sAttributes" class="dd">
					<option value="">-- <?php echo _('Select an Attribute'); ?> --</option>
					<?php foreach ( $attributes as $a ) { ?>
					<option value="<?php echo $a['attribute_id']; ?>"><?php echo $a['title']; ?></option>
					<?php } ?>
				</select><br />
				<p style="float:right;"><a href="#" id="aAddAttribute" title="<?php echo _('Add Attribute'); ?>"><?php echo _('Add'); ?></a></p>
				<input type="hidden" name="hAttributes" id="hAttributes" />
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" class="button" value="<?php echo _('Add Category'); ?>" style="width: auto" /></td>
		</tr>
	</table>
	<br />
	
	
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