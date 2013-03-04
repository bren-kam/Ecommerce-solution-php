<?php
/**
 * @var Template $template
 * @var User $user
 */
?>
<div id="sidebar">
    <a href="/email-marketing/" title="<?php echo _('Dashboard'); ?>" class="top first<?php $template->select('dashboard'); ?>"><?php echo _('Dashboard'); ?></a>

    <a href="/email-marketing/emails/send/" title="<?php echo _('Send Email'); ?>" class="top<?php $template->select('emails'); ?>"><?php echo _('Send Email'); ?></a>
   	<?php if ( $template->v('emails') ) { ?>
   		<a href="/email-marketing/emails/" title="<?php echo _('View'); ?>" class="sub<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
   	<?php } ?>

    <a href="/email-marketing/subscribers/" title="<?php echo _('Subscribers'); ?>" class="top<?php $template->select('subscribers'); ?>"><?php echo _('Subscribers'); ?></a>
    <?php if ( true === $template->v('subscribers') ) { ?>
        <a href="/email-marketing/subscribers/" title="<?php echo _('View'); ?>" class="sub<?php $template->select('subscribed'); ?>"><?php echo _('Subscribed'); ?></a>
        <a href="/email-marketing/subscribers/unsubscribed/" title="<?php echo _('View'); ?>" class="sub<?php $template->select('unsubscribed'); ?>"><?php echo _('Unsubscribed'); ?></a>
        <a href="/email-marketing/subscribers/add-edit/" title="<?php echo _('Add'); ?>" class="sub<?php $template->select('add-edit'); ?>"><?php echo _('Add'); ?></a>
        <a href="/email-marketing/subscribers/import/" title="<?php echo _('Import'); ?>" class="sub<?php $template->select('import'); ?>"><?php echo _('Import'); ?></a>
        <a href="/email-marketing/subscribers/export/<?php if ( isset( $_GET['elid'] ) ) echo '?elid=' . $_GET['elid']; ?>" title="<?php echo _('Export'); ?>" class="sub<?php $template->select('export'); ?>"><?php echo _('Export'); ?></a>
    <?php } ?>

    <a href="/email-marketing/email-lists/" title="<?php echo _('Email Lists'); ?>" class="top<?php $template->select('email-lists'); ?>"><?php echo _('Email Lists'); ?></a>
   	<?php if ( $template->v('email-lists') ) { ?>
   		<a href="/email-marketing/email-lists/add-edit/" title="<?php echo _('Add Email List'); ?>" class="sub<?php $template->select('add-edit'); ?>"><?php echo _('Add'); ?></a>
   	<?php } ?>

   	<a href="/email-marketing/autoresponders/" title="<?php echo _('Autoresponders'); ?>" class="top<?php $template->select('autoresponders'); ?>"><?php echo _('Autoresponders'); ?></a>
   	<?php if ( $template->v('autoresponders') ) { ?>
   		<a href="/email-marketing/autoresponders/add-edit/" title="<?php echo _('Add'); ?>" class="sub<?php $template->select('add-edit'); ?>"><?php echo _('Add'); ?></a>
   	<?php } ?>

   	<a href="/email-marketing/settings/" title="<?php echo _('Settings'); ?>" class="top last<?php $template->select('settings'); ?>"><?php echo _('Settings'); ?></a>
</div>