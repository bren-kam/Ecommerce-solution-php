<?php
/**
 * @page Checklists
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

css( 'data-tables/TableTools.css', 'data-tables/ui.css', 'checklists/list' );
javascript( 'jquery', 'data-tables/jquery.dataTables', 'data-tables/ZeroClipboard/ZeroClipboard.js', 'data-tables/jquery.tableTools.js', 'checklists/list' );

$selected = 'checklists';
$title = _('Checklists') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Checklists'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'checklists/' ); ?> 
    
    <div id="subcontent">
        <div id="dChecklistsContainer">
        	<table cellpadding="0" cellspacing="0" width="100%" id="tListChecklists">
				<thead>
					<tr>
						<th width="10%" class="center"><?php echo _('Days Left'); ?></th>
						<th width="30%"><?php echo _('Website'); ?></th>
						<th width="20%"><?php echo _('Type'); ?></th>
						<th width="20%"><?php echo _('Date Created'); ?></th>
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

<?php 
get_sidebar();
get_footer();
?>