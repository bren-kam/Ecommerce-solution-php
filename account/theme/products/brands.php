<?php
/**
 * @page Product Catalog > Brands
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$b = new Brands;
$brands = $b->get_top_brands();

// Load the jQuery UI CSS
add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );

css('products/brands');
javascript('products/brands');

$title = _('Brands') . ' | ' . _('Product Catalog') . ' ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Brands'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/' ); ?>
	<div id="subcontent">
		<table cellpadding="0" cellspacing="0" class="form">
			<tr>
				<td><label for="tAutoComplete"><?php echo _('Add Brand'); ?></label></td>
				<td><input type="text" class="tb" tmpval="<?php echo _('Enter Brand'); ?>..." id="tAutoComplete" /></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="checkbox" class="cb" id="cbLinkBrands"<?php echo ( $user['website']['link_brands'] ) ? ' checked="checked"' : ''; ?> /> <label for="cbLinkBrands"><?php echo _('Link to Brand Websites'); ?></label></td>
			</tr>
		</table>
		<?php 
		nonce::field( 'brands-autocomplete', '_ajax_autocomplete' );
		nonce::field( 'add-brand', '_ajax_add_brand' );
		nonce::field( 'set-link', '_ajax_set_link' );
		nonce::field( 'remove-brand', '_ajax_remove_brand' );
		nonce::field( 'update-sequence', '_ajax_update_sequence' );
		?>
		<hr />
		<div id="brands">
		<?php
		if ( is_array( $brands ) ) {
			$remove_brand_nonce = nonce::create('remove-brand');
			foreach ( $brands as $brand ) {
			?>
				<div id="dBrand_<?php echo $brand['brand_id']; ?>" class="brand">
					<img src="<?php echo $brand['image']; ?>" title="<?php echo $brand['name']; ?>" />
					<h4><?php echo $brand['name']; ?></h4>
					<p class="brand-url"><a href="<?php echo $brand['link']; ?>" title="<?php echo $brand['name']; ?>" target="_blank" ><?php echo $brand['link']; ?></a></p>
					<a href="/ajax/products/brands/remove/?_nonce=<?php echo $remove_brand_nonce; ?>&amp;bid=<?php echo $brand['brand_id']; ?>" title="<?php echo _('Remove'); ?>" class="remove" ajax="1"><?php echo _('Remove'); ?></a>
				</div>
		<?php 
			}
		}?>
		</div>
		<br clear="left" /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>