<?php
/**
 * @page Product Options
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

css( 'data-tables/TableTools.css', 'data-tables/ui.css', 'product-options/list' );
javascript( 'jquery', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard.js', 'data-tables/jquery.tableTools.js', 'product-options/list' );

$selected = 'products';
$title = _('Product Options') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Product Options'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'product-options/' ); ?>
	<div id="subcontent">
		<?php nonce::field( 'delete-product-option', '_ajax_delete_product_option' ); ?>
        <?php if ( isset( $_GET['add'] ) ) { ?>
        <div class="success">Your product option has been successfully added</div><br/>
        <?php } elseif ( isset( $_GET['edit'] ) ) { ?>
        <div class="success">Your product option has been successfully edited</div><br/>
        <?php } ?>
        <div id="dUsersContainer">
        	<table cellpadding="0" cellspacing="0" width="100%" id="tListProductOptions">
				<thead>
					<tr>
						<th width="40%" class="center"><?php echo _('Title'); ?></th>
						<th width="40%"><?php echo _('Name'); ?></th>
						<th width="20%"><?php echo _('Type'); ?></th>
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