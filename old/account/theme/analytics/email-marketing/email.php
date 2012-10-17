<?php
/**
 * @page Analytics - Email
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

$mc_campaign_id = $_GET['mcid'];

if ( empty( $mc_campaign_id ) )
	url::redirect('/analytics/email-marketing/');

// Instantiate class
$a = new Analytics();
$e = new Email_Marketing;

// Get the email
$email = $a->get_email( $mc_campaign_id );

// Get the bar chart
$bar_chart = $e->bar_chart( $email );

css( 'analytics' );
javascript(  'swfobject', 'JSON', 'analytics/email' );

add_before_javascript('function open_flash_chart_data() { return JSON.stringify(' . $bar_chart . ');}');
add_javascript_callback( 'swfobject.embedSWF("/media/flash/open-flash-chart.swf", "dEmailStatistics", "787", "387", "9.0.0", "", null, { wmode:"transparent" } );');

$selected = "analytics";
$title = _('Email') . ' | ' . _('Analytics') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Email:'), ' ', $email['subject']; ?><div class="float-right"><a href="javascript:;" id="aStatistics" class="email-screen button" title="<?php echo _('Statistics'); ?>"><?php echo _('Statistics'); ?></a> <a href="javascript:;" id="aClickOverlay" class="email-screen button" title="<?php echo _('Click Overlay'); ?>"><?php echo _('Click Overlay'); ?></a></div></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'analytics/', 'email_marketing', 'email' ); ?>
	<div id="subcontent">
		<div id="dClickStats" class="hidden"><?php echo json_encode( $email['click_overlay'] ); ?></div>
		<?php
		if ( count( $email['advice'] ) > 0 ) {
			$advice = '';
			
			foreach ( $email['advice'] as $adv ) {
				if ( 'negative' != $adv['type'] )
					$advice .= $adv['msg'] . '<br />';
			}
			
			if ( !empty( $advice ) )
				echo '<p><strong>', _('Advice') . ":</strong><br />$advice</p>";
		}
		?>
		<div id="dStatistics" class="stat-screen selected">
			<div id="dEmailStatistics"></div>
			<br clear="all" /><br />
			<!-- End: Bottom Boxes -->
			<div class="col-2">
				<div class="info-box">
					<p class="info-box-title"><?php echo _('Email Details'); ?></p>
					<div class="info-box-content">
						<br />
						<table cellpadding="0" cellspacing="0" id="emails">
							<tr>
								<td width="220"><span class="data"><?php echo $email['emails_sent']; ?></span> <span class="label"><?php echo _('Emails Sent'); ?></span></td>
								<td width="220"><span class="data"><?php echo $email['opens']; ?></span> <span class="label"><?php echo _('Opens'); ?></span></td>
								<td width="220"><span class="data" id="sTotalClicks"><?php echo $email['clicks']; ?></span> <span class="label"><?php echo _('Clicks'); ?></span></td>
							</tr>
							<tr>
								<td><span class="data"><?php echo $email['forwards']; ?></span> <span class="label"><?php echo _('Forwards'); ?></span></td>
								<td><span class="data"><?php echo $email['soft_bounces'] + $email['hard_bounces']; ?></span> <span class="label"><?php echo _('Bounces'); ?></span></td>
								<td><span class="data"><?php echo $email['unsubscribes']; ?></span> <span class="label"><?php echo _('Unsubscribes'); ?></span></td>
							</tr>
							<tr>
								<td><span class="data"><?php echo $email['unique_opens']; ?></span> <span class="label"><?php echo _('Unique Opens'); ?></span></td>
								<td><span class="data"><?php echo $email['unique_clicks']; ?></span> <span class="label"><?php echo _('Unique Clicks'); ?></span></td>
								<td><span class="data"><?php echo $email['abuse_reports']; ?></span> <span class="label"><?php echo _('Abuse Reports'); ?></span></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<br /><br />
		</div>
		<div id="dClickOverlay" class="hidden stat-screen">
			<iframe src="/analytics/email-marketing/click-overlay-html/?mcid=<?php echo $email['mc_campaign_id']; ?>" name="ifClickOverlay" id="ifClickOverlay" width="783" height="100"></iframe>
		</div>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>