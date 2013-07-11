<?php
/**
 * @package Grey Suit Retail
 * @page List Website Pages
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AnalyticsEmail $email
 * @var string $bar_chart
 */

echo $template->start( _('Email') . ': ' . $email->subject );
?>

<p><a href="#" id="aStatistics" class="email-screen button" title="<?php echo _('Statistics'); ?>"><?php echo _('Statistics'); ?></a> <a href="#" id="aClickOverlay" class="email-screen button" title="<?php echo _('Click Overlay'); ?>"><?php echo _('Click Overlay'); ?></a></p>
<div id="dClickStats" class="hidden"><?php echo json_encode( $email->click_overlay ); ?></div>
<?php
if ( count( $email->advice ) > 0 ) {
    $advice = '';

    foreach ( $email->advice as $adv ) {
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
                <table id="emails" class="width-auto">
                    <tr>
                        <td width="220"><span class="data"><?php echo $email->emails_sent; ?></span> <span class="label"><?php echo _('Emails Sent'); ?></span></td>
                        <td width="220"><span class="data"><?php echo $email->opens; ?></span> <span class="label"><?php echo _('Opens'); ?></span></td>
                        <td width="220"><span class="data" id="sTotalClicks"><?php echo $email->clicks; ?></span> <span class="label"><?php echo _('Clicks'); ?></span></td>
                    </tr>
                    <tr>
                        <td><span class="data"><?php echo $email->forwards; ?></span> <span class="label"><?php echo _('Forwards'); ?></span></td>
                        <td><span class="data"><?php echo $email->soft_bounces + $email->hard_bounces; ?></span> <span class="label"><?php echo _('Bounces'); ?></span></td>
                        <td><span class="data"><?php echo $email->unsubscribes; ?></span> <span class="label"><?php echo _('Unsubscribes'); ?></span></td>
                    </tr>
                    <tr>
                        <td><span class="data"><?php echo $email->unique_opens; ?></span> <span class="label"><?php echo _('Unique Opens'); ?></span></td>
                        <td><span class="data"><?php echo $email->unique_clicks; ?></span> <span class="label"><?php echo _('Unique Clicks'); ?></span></td>
                        <td><span class="data"><?php echo $email->abuse_reports; ?></span> <span class="label"><?php echo _('Abuse Reports'); ?></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <br /><br />
</div>
<div id="dClickOverlay" class="hidden stat-screen">
    <iframe src="<?php echo url::add_query_arg( 'accid', $email->ac_campaign_id, '/analytics/email-click-overlay/' ); ?>" name="ifClickOverlay" id="ifClickOverlay" width="783" height="100"></iframe>
</div>
<script type="text/javascript">
    function open_flash_chart_data() {
        return JSON.stringify(<?php echo $bar_chart; ?>);
    }
</script>

<?php echo $template->end(); ?>