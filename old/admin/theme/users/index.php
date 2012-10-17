<?php
/**
 * @page Users
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// If user's permission level is too low, redirect.
if ( $user['role'] < 7 )
	login();

css( 'data-tables/TableTools.css', 'data-tables/ui.css', 'users/list' );
javascript( 'jquery', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard.js', 'data-tables/jquery.tableTools.js', 'users/list' );

$selected = 'users';
$title = _('Users') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Users'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'users/' ); ?>
	<div id="subcontent">
        <div id="dUsersContainer">
            <?php nonce::field( 'delete-user', '_ajax_delete_user' ); ?>
        	<table cellpadding="0" cellspacing="0" width="100%" id="tListUsers">
				<thead>
					<tr>
						<th width="23%" class="center"><?php echo _('Name'); ?></th>
						<th width="25%"><?php echo _('Email'); ?></th>
						<th width="14%"><?php echo _('Phone'); ?></th>
						<th width="28%"><?php echo _('Website'); ?></th>
						<th width="10%"><?php echo _('Permission'); ?></th>
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