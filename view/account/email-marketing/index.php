<?php
/**
 * @package Grey Suit Retail
 * @page Dashboard | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var EmailMessage[] $messages
 * @var Email[] $subscribers
 * @var AnalyticsEmail $email
 * @var string $bar_chart
 * @var int $email_count
 */

echo $template->start( _('Dashboard') );

if ( $email ) {
?>
    <p><strong><?php echo _('Latest email:'); ?></strong> <?php echo $email->subject; ?></p>
<?php } elseif( 0 == $email_count ) { ?>
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
        if ( is_array( $messages ) ) {
            foreach ( $messages as $message ) {
            ?>
                <p><a href="<?php echo url::add_query_arg( 'accid', $message->ac_campaign_id, '/analytics/email/' ); ?>" title="<?php echo $message->subject; ?>"><?php echo $message->subject; ?></a></p>
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
                <p><?php echo $s->email; ?></p>
                <?php } ?>
                <br />
                <p align="right"><a href="/email-marketing/subscribers/" title="<?php echo _('View All'); ?>" class="big bold"><?php echo _('View'); ?> <span class="highlight"><?php echo _('All'); ?></span></a></p>
            <?php } else { ?>
                <p><?php echo _('You do not yet have any subscribers.'); ?></p>
            <?php } ?>
        </div>
    </div>
</div>
<br clear="left" />
<script type="text/javascript">
    function open_flash_chart_data() {
        return JSON.stringify(<?php echo $bar_chart; ?>);
    }
</script>

<?php echo $template->end(); ?>