<div id="sidebar">
	<h2><?php echo _('Sidebar'); ?></h2>
	<a href="/email-marketing/" title="<?php echo _('Dashboard'); ?>" class="top<?php if ( isset( $dashboard ) ) echo ' selected'; ?>"><?php echo _('Dashboard'); ?></a>
	
	<a href="/email-marketing/emails/send/" title="<?php echo _('Send Email'); ?>" class="top<?php if ( isset( $send_email ) ) echo ' selected'; ?>"><?php echo _('Send Email'); ?></a>
	<?php if ( isset( $send_email ) ) { ?>
		<a href="/email-marketing/emails/" title="<?php echo _('View Emails'); ?>" class="sub<?php if ( isset( $emails ) ) echo ' selected'; ?>"><?php echo _('View'); ?></a>
	<?php } ?>
	
	<a href="/email-marketing/subscribers/" title="<?php echo _('Subscribers'); ?>" class="top<?php if ( isset( $subscribers ) ) echo ' selected'; ?>"><?php echo _('Subscribers'); ?></a>
	<?php if ( isset( $subscribers ) ) { ?>
		<a href="/email-marketing/subscribers/" title="<?php echo _('View Subscribed Users'); ?>" class="sub<?php if ( isset( $subscribed ) ) echo ' selected'; ?>"><?php echo _('Subscribed'); ?></a>
		<a href="/email-marketing/subscribers/unsubscribed/" title="<?php echo _('View Unsubscribed Users'); ?>" class="sub<?php if ( isset( $unsubscribed ) ) echo ' selected'; ?>"><?php echo _('Unsubscribed'); ?></a>
		<a href="/email-marketing/subscribers/add-edit/" title="<?php echo _('Add Subscriber'); ?>" class="sub<?php if ( isset( $add_edit_subscriber ) ) echo ' selected'; ?>"><?php echo _('Add'); ?></a>
		<a href="/email-marketing/subscribers/import/" title="<?php echo _('Import Subscribers'); ?>" class="sub<?php if ( isset( $import_subscribers ) ) echo ' selected'; ?>"><?php echo _('Import'); ?></a>
        <a href="/email-marketing/subscribers/export/<?php if ( isset( $_GET['elid'] ) ) echo '?elid=' . $_GET['elid']; ?>" title="<?php echo _('Export Subscribers'); ?>" class="sub"><?php echo _('Export'); ?></a>
	<?php } ?>
	
	<a href="/email-marketing/email-lists/" title="<?php echo _('Email Lists'); ?>" class="top<?php if ( isset( $email_lists ) ) echo ' selected'; ?>"><?php echo _('Email Lists'); ?></a>
	<?php if ( isset( $email_lists ) ) { ?>
		<a href="/email-marketing/email-lists/add-edit/" title="<?php echo _('Add Email List'); ?>" class="sub<?php if ( isset( $add_edit_email_list ) ) echo ' selected'; ?>"><?php echo _('Add'); ?></a>
	<?php } ?>
	
	<a href="/email-marketing/autoresponders/" title="<?php echo _('Autoresponders'); ?>" class="top<?php if ( isset( $autoresponders ) ) echo ' selected'; ?>"><?php echo _('Autoresponders'); ?></a>
	<?php if ( isset( $autoresponders ) ) { ?>
		<a href="/email-marketing/autoresponders/" title="<?php echo _('View Autoresponders'); ?>" class="sub<?php if ( isset( $view_autoresponders ) ) echo ' selected'; ?>"><?php echo _('View'); ?></a>
		<a href="/email-marketing/autoresponders/add-edit/" title="<?php echo _('Add Autoresponder'); ?>" class="sub<?php if ( isset( $add_edit_autoresponder ) ) echo ' selected'; ?>"><?php echo _('Add'); ?></a>
	<?php } ?>
	
	<a href="/email-marketing/settings/" title="<?php echo _('Settings'); ?>" class="top<?php if ( isset( $settings ) ) echo ' selected'; ?>"><?php echo _('Settings'); ?></a>
</div>