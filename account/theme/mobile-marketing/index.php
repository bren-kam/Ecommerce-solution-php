<?php
/**
 * @page Mobile Marketing
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Redirect to main section if they don't have mobile marketing
if ( !$user['website']['mobile_marketing'] )
	url::redirect('/');

// Instantiate Classes
$m = new Mobile_Marketing;

// Get data
$messages = $m->dashboard_messages();
$subscribers = $m->dashboard_subscribers();

if ( is_array( $subscribers ) ) {
	$a = new Analytics;
	
	// Get the analytics data
	//$message = $a->get_message( $messages['am_blast_id'] );
	
} else {
	$message = array(
		'opens' => 0,
		'clicks' => 0,
		'forwards' => 0,
		'soft_bounces' => 0,
		'hard_bounces' => 0,
		'unsubscribes' => 0
	);
}

//$bar_chart = $e->bar_chart( $message );

$selected = "mobile_marketing";
$title = _('Mobile Marketing') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Mobile Marketing Dashboard'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'mobile-marketing/', 'dashboard' ); ?>
	<div id="subcontent">
        <p><strong><?php echo _('Short Code'), ':</strong> ', '#96362'; ?></strong></p>
		<?php if ( $messages[0] ) { ?>
		<p><strong><?php echo _('Latest message:'); ?></strong> <?php echo format::limit_chars( $messages[0]['message'], 50 ); ?></p>
		<?php } else { ?>
		<p><?php echo _('You have not yet sent out a message.'); ?> <a href="/mobile-marketing/messages/add-edit/" title="<?php echo _('Send Message'); ?>"><?php echo _('Click here'); ?></a> <?php echo _('to get started'); ?>.</p>
		<?php } ?>
		<div id="dMessageStatistics"></div>
		<br clear="all" />
		<br />
		<div class="col-2 float-left">
			<div class="info-box">
				<p class="info-box-title"><?php echo _('Messages Sent'); ?></p>
				<div class="info-box-content">
				<?php 
				if ( is_array( $messages ) ) {
					foreach ( $messages as $em ) {
					?>
						<p><a href="/analytics/mobile/?mmid=<?php echo $em['mobile_message_id']; ?>" title="<?php echo $em['subject']; ?>"><?php echo $em['subject']; ?></a></p>
					<?php } ?>
					<p align="right"><a href="/mobile-marketing/messages/" title="<?php echo _('View All'); ?>" class="big bold"><?php echo _('View'); ?> <span class="highlight"><?php echo _('All'); ?></span></a></p>
				<?php } else { ?>
					<p><?php echo _('You have not yet sent out a message.'); ?> <a href="/mobile-marketing/messages/add-edit/" title="<?php echo _('Send Message'); ?>"><?php echo _('Click here'); ?></a> <?php echo _('to get started'); ?>.</p>
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
						<p><?php echo $s['phone']; ?></p>
						<?php } ?>
						<br />
						<p align="right"><a href="/mobile-marketing/subscribers/" title="<?php echo _('View All'); ?>" class="big bold"><?php echo _('View'); ?> <span class="highlight"><?php echo _('All'); ?></span></a></p>
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