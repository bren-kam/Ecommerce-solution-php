<?php
/**
 * @page Product Catalog > Groups
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$title = _('Groups') . ' | ' . _('Product Catalog') . ' ' . TITLE;
get_header(); 
?>

<div id="content">
	<h1><?php echo _('Groups'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'products/', 'product_groups' ); ?>
	<div id="subcontent">
		<table ajax="/ajax/products/groups/list/" perPage="30,50,100" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="100%" sort="1"><?php echo _('Name'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>