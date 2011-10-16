<div id="sidebar">
	<h2><?php echo _('Sidebar'); ?></h2>
	<a href="/email-marketing/" title="<?php echo _('Dashboard'); ?>" class="top<?php if( $dashboard ) echo ' selected'; ?>"><?php echo _('Dashboard'); ?></a>
	
	<a href="/email-marketing/emails/send/" title="<?php echo _('Send Email'); ?>" class="top<?php if( $send_email ) echo ' selected'; ?>"><?php echo _('Send Email'); ?></a>
	<?php if( $send_email ) { ?>
		<a href="/email-marketing/emails/" title="<?php echo _('View Emails'); ?>" class="sub<?php if( $emails ) echo ' selected'; ?>"><?php echo _('View'); ?></a>
	<?php } ?>
	
	<a href="/email-marketing/subscribers/" title="<?php echo _('Subscribers'); ?>" class="top<?php if( $subscribers ) echo ' selected'; ?>"><?php echo _('Subscribers'); ?></a>
	<?php if( $subscribers ) { ?>
		<a href="/email-marketing/subscribers/" title="<?php echo _('View Subscribed Users'); ?>" class="sub<?php if( $subscribed ) echo ' selected'; ?>"><?php echo _('Subscribed'); ?></a>
		<a href="/email-marketing/subscribers/unsubscribed/" title="<?php echo _('View Unsubscribed Users'); ?>" class="sub<?php if( $unsubscribed ) echo ' selected'; ?>"><?php echo _('Unsubscribed'); ?></a>
		<a href="/email-marketing/subscribers/add-edit/" title="<?php echo _('Add Subscriber'); ?>" class="sub<?php if( $add_edit_subscriber ) echo ' selected'; ?>"><?php echo _('Add'); ?></a>
		<a href="/email-marketing/subscribers/import/" title="<?php echo _('Import Subscribers'); ?>" class="sub<?php if( $import_subscribers ) echo ' selected'; ?>"><?php echo _('Import'); ?></a>
	<?php } ?>
	
	<a href="/email-marketing/email-lists/" title="<?php echo _('Email Lists'); ?>" class="top<?php if( $email_lists ) echo ' selected'; ?>"><?php echo _('Email Lists'); ?></a>
	<?php if( $email_lists ) { ?>
		<a href="/email-marketing/email-lists/add-edit/" title="<?php echo _('Add Email List'); ?>" class="sub<?php if( $add_edit_email_list ) echo ' selected'; ?>"<?php echo _('>Add'); ?></a>
	<?php } ?>
	
	<a href="/email-marketing/autoresponders/" title="<?php echo _('Autoresponders'); ?>" class="top<?php if( $autoresponders ) echo ' selected'; ?>"><?php echo _('Autoresponders'); ?></a>
	<?php if( $autoresponders ) { ?>
		<a href="/email-marketing/autoresponders/" title="<?php echo _('View Autoresponders'); ?>" class="sub<?php if( $view_autoresponders ) echo ' selected'; ?>"><?php echo _('View'); ?></a>
		<a href="/email-marketing/autoresponders/add-edit/" title="<?php echo _('Add Autoresponder'); ?>" class="sub<?php if( $add_edit_autoresponder ) echo ' selected'; ?>"<?php echo _('>Add'); ?></a>
	<?php } ?>
	
	<a href="/email-marketing/settings/" title="<?php echo _('Settings'); ?>" class="top<?php if( $settings ) echo ' selected'; ?>"><?php echo _('Settings'); ?></a>
</div>