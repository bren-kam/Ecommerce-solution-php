<?php
/**
 * @package Grey Suit Retail
 * @page Email Analytics
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var stdClass $email
 * @var string $bar_chart
 * @var EmailMessage $email_message
 */

echo $template->start( _('Email') . ': ' . $email_message->subject );
?>

<div id="dStatistics">
	<div id="dEmailStatistics"></div>
	<br clear="all" />
	<br />
    <div class="col-2">
        <div class="info-box">
            <p class="info-box-title"><?php echo _('Email Details'); ?></p>
            <div class="info-box-content">
                <br />
                <table id="emails" class="width-auto">
                    <tr>
                        <td width="165"><span class="data"><?php echo $email->requests; ?></span> <span class="label"><?php echo _('Emails Sent'); ?></span></td>
                        <td width="165"><span class="data"><?php echo $email->opens; ?></span> <span class="label"><?php echo _('Opens'); ?></span></td>
                        <td width="165"><span class="data"><?php echo $email->clicks; ?></span> <span class="label"><?php echo _('Clicks'); ?></span></td>
                        <td width="165"><span class="data"><?php echo $email->bounces; ?></span> <span class="label"><?php echo _('Bounces'); ?></span></td>
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