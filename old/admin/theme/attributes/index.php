<?php
/**
 * @page Attributes
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

css( 'data-tables/TableTools.css', 'data-tables/ui.css', 'attributes/list' );
javascript( 'jquery', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard.js', 'data-tables/jquery.tableTools.js', 'attributes/list' );

$selected = 'products';
$title = _('Attributes') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Attributes'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'attributes/' ); ?>
	<div id="subcontent">
		<?php nonce::field( 'delete-attribute', '_ajax_delete_attribute' ); ?>
        <div id="dAttributesContainer">
        	<table cellpadding="0" cellspacing="0" width="100%" id="tListAttributes">
				<thead>
					<tr>
						<th width="100%"><?php echo _('Attribute Name'); ?></th>
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