<?php
/**
 * @package Grey Suit Retail
 * @page Step1 | Create | Campaigns | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var User $user
 * @var EmailMessage $campaign
 * @var EmailList[] $email_lists
 * @var array $settings
 * @var string $timezone
 * @var string $server_timezone
 * @var EmailTemplate[] $templates
 * @var AccountFile[] $files
 * @var string $default_from
 * @var boolean $overwrite_from
 * @var DateTime $scheduled_datetime
 */
?>

    <h2>Here is what your campign will look like</h2>
<br />

<div class="email-marketing-wrapper clear">
    <div class="email-layout" id="email-preview">

    </div>

    <p>
        <a href="#" data-step="2" class="button float-left" title="<?php echo _('Wait, I Need to Edit'); ?>"><?php echo _('Wait, I Need to Edit'); ?></a>
        <a href="#" class="button save-draft float-left" title="<?php echo _('Save Draft'); ?>"><?php echo _('Save Draft'); ?></a>
        <a href="#dSendTest" class="button float-left" rel="dialog" title="<?php echo _('Send a Test Campaign'); ?>"><?php echo _('Send a Test Campaign'); ?></a>
        <a href="#" data-step="3" class="button float-right save-campaign" title="<?php echo _('Looks Good! Send it Out.'); ?>"><?php echo _('Looks Good! Send it Out.'); ?></a>
    </p>

    <div class="hidden" id="dSendTest">
        <p>
            <input type="text" id="test-destination" class="tb" placeholder="Email to send Campaign Preview" />
            <a href="#" class="button close send-test">Send</a>
        </p>
    </div>
</div><!-- .email-marketing-wrapper -->

<?php nonce::field( 'send_test', '_send_test'); ?>
<?php nonce::field( 'save_draft', '_save_draft'); ?>
<?php nonce::field( 'save_campaign', '_save_campaign'); ?>