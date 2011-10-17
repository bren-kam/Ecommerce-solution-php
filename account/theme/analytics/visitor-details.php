<?php
/**
 * @page List Visitor
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$analytics_visitor_id = (int) $_GET['avid'];

// Redirect them to the home page of analytics
if ( !$analytics_visitor_id )
	url::redirect('/analytics/');

$a = new Analytics;
$visitor = $a->get_visitor( $analytics_visitor_id );

$selected = "analytics";
$title = _('Visitor Details | Analytics') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Visitor'), ': ', $visitor['name']; ?> </h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'analytics/', 'visitor_details', 'visitor' ); ?>
	<div id="subcontent">
		<table cellpadding="0" cellspacing="0" class="form">
			<tr>
				<td><label><?php echo _('Name'); ?>:</label></td>
				<td><?php echo $visitor['name']; ?></td>
			</tr>
			<tr>
				<td><label><?php echo _('Email'); ?>:</label></td>
				<td><?php echo $visitor['email']; ?></td>
			</tr>
			<tr>
				<td><label><?php echo _('Date Created'); ?>:</label></td>
				<td><?php echo dt::date( 'F jS, Y', $visitor['date_created'] ); ?></td>
			</tr>
		</table>
		<br /><br />
		<table perPage="30,50,100" cellpadding="0" cellspacing="0" width="100%" class="dt">
			<thead>
				<tr>
					<th width="50%"><?php echo _('Page'); ?></th>
					<th width="26%" sort="3 desc"><?php echo _('Est. Time On Page'); ?></th>
					<th width="12%" sort="2 desc"><?php echo _('Subscribed'); ?></th>
					<th width="12%" sort="1 desc"><?php echo _('Date Visited'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php 
			$page_count = count( $visitor['pages'] );
			for ( $i = 0; $i < $page_count; $i++ ) {
				$page_date = dt::date( 'F jS, Y', $visitor['pages'][$i]['date_visited'] );
				
				if ( $i == $page_count - 1 )
					$get_time_on_page = false;
				
				$get_time_on_page = ( $page_date == dt::date( 'F jS, Y', $visitor['pages'][$i+1]['date_visited'] ) ) ? true : false;
			?>
			<tr>
				<td><?php echo $visitor['pages'][$i]['page']; ?></td>
				<td><?php echo ( $get_time_on_page ) ? dt::sec_to_time( $visitor['pages'][$i+1]['date_visited'] - $visitor['pages'][$i]['date_visited'] ) : 'N/A'; ?></td>
				<td><?php echo ( $visitor['pages'][$i]['subscribed'] ) ? 'Yes' : 'No'; ?></td>
				<td><?php echo $page_date; ?></td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>