<?php
/**
 * @page Visitors
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$a = new Analytics( $user['website']['ga_profile_id'] );
list( $start_date, $end_date ) = $a->dates();

$selected = "analytics";
$title = _('Visitors | Analytics') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Visitors'), ' <span class="small">', dt::date( 'M j, Y', strtotime( $start_date ) ), ' - ', dt::date( 'M j, Y', strtotime( $end_date ) ), '</span>'; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'analytics/', 'visitors' ); ?>
	<div id="subcontent">
		<table ajax="/ajax/analytics/list-visitors/" perPage="30,50,100" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th width="25%"><?php echo _('Name'); ?></th>
					<th width="25%" sort="3 desc"><?php echo _('Pages Visited'); ?></th>
					<th width="25%" sort="2 desc"><?php echo _('Subscribed'); ?></th>
					<th width="25%" sort="1 desc"><?php echo _('Date Visited'); ?></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>