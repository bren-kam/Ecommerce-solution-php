<?php
/**
 * @page Email Marketing
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Redirect to main section if they don't have email marketing
if ( !$user['website']['email_marketing'] )
	url::redirect('/email-marketing/subscribers/');

// Instantiate Classes
$e = new Email_Marketing;

// Get data
$emails = $e->dashboard_messages( $user['website']['website_id'] );
$subscribers = $e->dashboard_subscribers( $user['website']['website_id'] );

if ( is_array( $emails ) ) {
	$a = new Analytics;
	
	// Get the analytics data
	$email = $a->get_email( $emails[0]['mc_campaign_id'] );
	
} else {
	$email = array(
		'emails_sent' => 0,
		'opens' => 0,
		'clicks' => 0,
		'forwards' => 0,
		'soft_bounces' => 0,
		'hard_bounces' => 0,
		'unsubscribes' => 0
	);
}

$bar_chart = $e->bar_chart( $email );

javascript( 'swfobject', 'JSON' );

add_before_javascript('function open_flash_chart_data() { return JSON.stringify(' . $bar_chart . ');}');
add_javascript_callback( 'swfobject.embedSWF("/media/flash/open-flash-chart.swf", "dEmailStatistics", "787", "387", "9.0.0", "", null, { wmode:"transparent" } );');

$selected = "email_marketing";
$title = _('Email Marketing') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Email Marketing Dashboard'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'email-marketing/', 'dashboard' ); ?>
	<div id="subcontent">
		<?php if ( $emails[0] ) { ?>
		<p><strong><?php echo _('Latest email:'); ?></strong> <?php echo $emails[0]['subject']; ?></p>
		<?php } else { ?>
		<p><?php echo _('You have not yet sent out an email.'); ?> <a href="/email-marketing/emails/send/" title="<?php echo _('Send Email'); ?>"><?php echo _('Click here'); ?></a> <?php echo _('to get started'); ?>.</p>
		<?php } ?>
		<div id="dEmailStatistics"></div>
		<br clear="all" />
		<br />
		<div class="col-2 float-left">
			<div class="info-box">
				<p class="info-box-title"><?php echo _('Emails Sent'); ?></p>
				<div class="info-box-content">
				<?php 
				if ( is_array( $emails ) ) {
					foreach ( $emails as $em ) {
					?>
						<p><a href="/analytics/email/?mcid=<?php echo $em['mc_campaign_id']; ?>" title="<?php echo $em['subject']; ?>"><?php echo $em['subject']; ?></a></p>
					<?php } ?>
					<p align="right"><a href="/email-marketing/emails/" title="<?php echo _('View All'); ?>" class="big bold"><?php echo _('View'); ?> <span class="highlight"><?php echo _('All'); ?></span></a></p>
				<?php } else { ?>
					<p><?php echo _('You have not yet sent out an email.'); ?> <a href="/email-marketing/emails/send/" title="<?php echo _('Send Email'); ?>"><?php echo _('Click here'); ?></a> <?php echo _('to get started'); ?>.</p>
				<?php } ?>
				</div>
			</div>
		</div>
		<div class="col-2 float-left">
			<div class="info-box">
				<p class="info-box-title"><?php echo _('Latest Subscribers'); ?></p>
				<div class="info-box-content">
					<?php 
					if ( is_array( $subscribers ) ) { 
						foreach ( $subscribers as $s ) {
						?>
						<p><?php echo $s['email']; ?></p>
						<?php } ?>
						<br />
						<p align="right"><a href="/email-marketing/subscribers/" title="<?php echo _('View All'); ?>" class="big bold"><?php echo _('View'); ?> <span class="highlight"><?php echo _('All'); ?></span></a></p>
					<?php } else { ?>
						<p><?php echo _('You do not yet have any subscribers.'); ?></p>
					<?php } ?>
				</div>
			</div>
		</div>
		<br clear="left" /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>