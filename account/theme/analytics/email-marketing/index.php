<?php
/**
 * @page Analytics - Email Marketing
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Redirect to main section if they don't have email marketing
if ( !$user['website']['email_marketing'] )
	url::redirect('/analytics/');

$a = new Analytics;
$emails = $a->get_emails();

$selected = "analytics";
$title = _('Email Marketing') . ' | ' . _('Analytics') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Email Marketing'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'analytics/', 'email_marketing' ); ?>
	<div id="subcontent">
		<table perPage="30,50,100" cellpadding="0" cellspacing="0" width="100%" class="dt">
			<thead>
				<tr>
					<th><?php echo _('Subject'); ?></th>
					<th class="text-right" column="formatted-num"><?php echo _('Sent *'); ?></th>
					<th class="text-right" column="formatted-num"><?php echo _('Opens *'); ?></th>
					<th class="text-right" column="formatted-num"><?php echo _('Clicked *'); ?></th>
					<th class="text-right" sort="1 desc"><?php echo _('Date'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php 
			foreach ( $emails as $e ) { 
				$last_updated = dt::date( 'F jS, Y \a\t g:i a', $e['last_updated'] );
			?>
				<tr>
					<td><a href="/analytics/email-marketing/email/?mcid=<?php echo $e['mc_campaign_id']; ?>" title="<?php echo $e['subject']; ?>"><?php echo $e['subject']; ?></a></td>
					<td class="text-right" title="<?php echo $last_updated; ?>"><?php echo $e['emails_sent']; ?></td>
					<td class="text-right" title="<?php echo $last_updated; ?>"><?php echo $e['opens']; ?></td>
					<td class="text-right" title="<?php echo $last_updated; ?>"><?php echo $e['clicks']; ?></td>
					<td class="text-right"><?php echo date( 'F jS, Y \a\t g:i a', $e['date_sent'] ); ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<br clear="left" /><br />
		<p>* <?php echo _('These statistics may not be up to date. To see the date and time they were last updated, hover over that cell for 1 second.'); ?></p>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>