<?php
/**
 * @page Brands
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

css( 'data-tables/TableTools.css', 'data-tables/ui.css', 'brands/list' );
javascript( 'jquery', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard.js', 'data-tables/jquery.tableTools.js', 'brands/list' );

$selected = 'products';
$title = _('Brands') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Brands'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'brands/' ); ?>
	<div id="subcontent">
		<?php nonce::field( 'delete-brand', '_ajax_delete_brand' ); ?>
        <div id="dBrandsContainer">
        	<table cellpadding="0" cellspacing="0" width="100%" id="tListBrands">
				<thead>
					<tr>
						<th width="30%"><?php echo _('Brand Name'); ?></th>
						<th width="70%"><?php echo _('URL'); ?></th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
        </div>
		<br clear="left" />
		<br /><br />
	</div>
</div>

<?php get_footer(); ?>