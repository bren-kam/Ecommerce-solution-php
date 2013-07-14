<?php
/**
 * @package Grey Suit Retail
 * @page List Website Pages
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var object $email
 * @var string $bar_chart
 */

echo $template->start( _('Email') . ': ' . $email->subject );
?>

<div id="dStatistics">
    <div class="col-2">
        <div class="info-box">
            <p class="info-box-title"><?php echo _('Email Details'); ?></p>
            <div class="info-box-content">
                <br />
                <table id="emails" class="width-auto">
                    <tr>
                        <td width="220"><span class="data"><?php echo $email->send_amt; ?></span> <span class="label"><?php echo _('Emails Sent'); ?></span></td>
                        <td width="220"><span class="data"><?php echo $email->opens; ?></span> <span class="label"><?php echo _('Opens'); ?></span></td>
                        <td width="220"><span class="data" id="sTotalClicks"><?php echo $email->linkclicks; ?></span> <span class="label"><?php echo _('Clicks'); ?></span></td>
                    </tr>
                    <tr>
                        <td><span class="data"><?php echo $email->forwards; ?></span> <span class="label"><?php echo _('Forwards'); ?></span></td>
                        <td><span class="data"><?php echo $email->totalbounces; ?></span> <span class="label"><?php echo _('Bounces'); ?></span></td>
                        <td><span class="data"><?php echo $email->unsubscribes; ?></span> <span class="label"><?php echo _('Unsubscribes'); ?></span></td>
                    </tr>
                    <tr>
                        <td><span class="data"><?php echo $email->uniqueopens; ?></span> <span class="label"><?php echo _('Unique Opens'); ?></span></td>
                        <td><span class="data"><?php echo $email->uniquelinkclicks; ?></span> <span class="label"><?php echo _('Unique Clicks'); ?></span></td>
                        <td><span class="data"><?php echo $email->uniqueforwards; ?></span> <span class="label"><?php echo _('Unique Forwards'); ?></span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <br /><br />
</div>

<script type="text/javascript">
    function open_flash_chart_data() {
        return JSON.stringify(<?php echo $bar_chart; ?>);
    }
</script>

<?php echo $template->end(); ?>