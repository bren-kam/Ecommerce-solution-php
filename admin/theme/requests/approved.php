<?php
/**
 * @page Requests
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

css( 'data-tables/TableTools.css', 'data-tables/ui.css', 'requests/list' );
javascript( 'jquery', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard.js', 'data-tables/jquery.tableTools.js', 'requests/approved' );

$selected = 'requests';
$title = _('Requests') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Requests - Approved'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'requests/' ); ?>
	<div id="subcontent">
        <div id="dRequestsContainer">
            <?php nonce::field( 'delete-request', '_ajax_delete_request' ); ?>
            <table cellpadding="0" cellspacing="0" width="100%" id="tListRequests">
                <thead>
                    <tr>
                        <th width="15%" class="center"><?php echo _('Date Approved'); ?></th>
                        <th width="25%"><?php echo _('Website'); ?></th>
                        <th width="17%"><?php echo _('User'); ?></th>
                        <th width="17%"><?php echo _('Type'); ?></th>
                        <th width="18%"><?php echo _('Date Requested'); ?></th>
                        <th width="8%"><?php echo _('Live'); ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php get_footer(); ?>