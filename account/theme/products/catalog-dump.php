<?php
/**
 * @page Catalog Dump
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Have to be Online Specialist or better to do this
if ( $user['role'] < 7 )
	url::redirect( '/products/' );

// Add validation
$v = new Validator;
$v->form_name = 'fCatalogDump';
$v->add_validation( 'hBrandID', 'req', _('You must select a brand before dumping') );

add_footer( $v->js_validation() );

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'catalog-dump' ) ) {
	// Server side validation
	$errs = $v->validate();
	
	// Dump brand i there was no errors
	if ( empty( $errs ) ) {
		$p = new Products;
		
		list( $success, $quantity, $no_industries ) = $p->dump_brand( $_POST['hBrandID'] );
		
		if ( !$success ) {
			if ( $no_industries ) {
				$errs .= _("This website has no industries.  Please contact your online specialist for assistance with this issue.");
			} else {
				$errs .= _("There is not enough free space to add this brand. Delete at least $quantity products, or expand the size of the product catalog.");
			}
		}
	}
}

// Need jQuery UI for AutoComplete
add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );
javascript('products/catalog-dump');

$title = _('Catalog Dump') . ' | ' . _('Product Catalog') . ' ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Catalog Dump'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
			<p class="success"><?php echo $quantity, _(' brand products added successfully!'); ?></p>
		<?php
		}
		
		if ( isset( $errs ) )
			echo "<p class='red'>$errs</p>";
		?>
		<p><?php echo _('NOTE: This will add <em>every</em> item in a selected brand into your product catalog.'); ?></p>
		<p><input type="text" class="tb" name="tAutoComplete" id="tAutoComplete" tmpval="<?php echo _('Enter Brand'); ?>..." /></p>
		<form action="/products/catalog-dump/" method="post" name="fCatalogDump">
			<p><input type="submit" class="button" value="<?php echo _('Dump Brand'); ?>" /></p>
			<input type="hidden" id="hBrandID" name="hBrandID" />
			<?php nonce::field('catalog-dump'); ?>
		</form>
		<?php nonce::field( 'brands-autocomplete', '_ajax_brands_autocomplete' ); ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>