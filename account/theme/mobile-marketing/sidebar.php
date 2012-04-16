<div id="sidebar">
	<h2><?php echo _('Sidebar'); ?></h2>
	<a href="/mobile-marketing/" title="<?php echo _('Dashboard'); ?>" class="top<?php if ( isset( $dashboard ) ) echo ' selected'; ?>"><?php echo _('Dashboard'); ?></a>
	
    <a href="/mobile-marketing/keywords/" title="<?php echo _('Keywords'); ?>" class="top<?php if ( isset( $keywords ) ) echo ' selected'; ?>"><?php echo _('Keywords'); ?></a>
   	<?php if ( isset( $keywords ) ) { ?>
   		<a href="/mobile-marketing/keywords/add-edit/" title="<?php echo _('Add Keyword'); ?>" class="sub"><?php echo _('Add'); ?></a>
   	<?php } ?>

	<a href="/mobile-marketing/messages/add-edit/" title="<?php echo _('Send Message'); ?>" class="top<?php if ( isset( $send_message ) ) echo ' selected'; ?>"><?php echo _('Send Message'); ?></a>
	<?php if ( isset( $send_message ) ) { ?>
		<a href="/mobile-marketing/messages/" title="<?php echo _('View Messages'); ?>" class="sub<?php if ( isset( $messages ) ) echo ' selected'; ?>"><?php echo _('View'); ?></a>
	<?php } ?>
	
	<a href="/mobile-marketing/subscribers/" title="<?php echo _('Subscribers'); ?>" class="top<?php if ( isset( $subscribers ) ) echo ' selected'; ?>"><?php echo _('Subscribers'); ?></a>
	<?php if ( isset( $subscribers ) ) { ?>
		<a href="/mobile-marketing/subscribers/" title="<?php echo _('View Subscribed Users'); ?>" class="sub<?php if ( isset( $subscribed ) ) echo ' selected'; ?>"><?php echo _('Subscribed'); ?></a>
		<a href="/mobile-marketing/subscribers/unsubscribed/" title="<?php echo _('View Unsubscribed Users'); ?>" class="sub<?php if ( isset( $unsubscribed ) ) echo ' selected'; ?>"><?php echo _('Unsubscribed'); ?></a>
		<a href="/mobile-marketing/subscribers/add-edit/" title="<?php echo _('Add Subscriber'); ?>" class="sub<?php if ( isset( $add_edit_subscriber ) ) echo ' selected'; ?>"><?php echo _('Add'); ?></a>
		<!--<a href="/mobile-marketing/subscribers/import/" title="<?php echo _('Import Subscribers'); ?>" class="sub<?php if ( isset( $import_subscribers ) ) echo ' selected'; ?>"><?php echo _('Import'); ?></a>-->
	<?php } ?>
	
	<a href="/mobile-marketing/lists/" title="<?php echo _('Mobile Lists'); ?>" class="top<?php if ( isset( $mobile_lists ) ) echo ' selected'; ?>"><?php echo _('Mobile Lists'); ?></a>
	<?php if ( isset( $mobile_lists ) ) { ?>
		<a href="/mobile-marketing/lists/add-edit/" title="<?php echo _('Add Mobile List'); ?>" class="sub<?php if ( isset( $add_edit_mobile_list ) ) echo ' selected'; ?>"><?php echo _('Add'); ?></a>
	<?php } ?>
	<a href="/mobile-marketing/pages/" title="<?php echo _('Mobile Pages'); ?>" class="top<?php if ( isset( $mobile_pages ) ) echo ' selected'; ?>"><?php echo _('Mobile Pages'); ?></a>
	
	<!--
	<a href="/mobile-marketing/autoresponders/" title="<?php echo _('Autoresponders'); ?>" class="top<?php if ( isset( $autoresponders ) ) echo ' selected'; ?>"><?php echo _('Autoresponders'); ?></a>
	<?php if ( isset( $autoresponders ) ) { ?>
		<a href="/mobile-marketing/autoresponders/" title="<?php echo _('View Autoresponders'); ?>" class="sub<?php if ( isset( $view_autoresponders ) ) echo ' selected'; ?>"><?php echo _('View'); ?></a>
		<a href="/mobile-marketing/autoresponders/add-edit/" title="<?php echo _('Add Autoresponder'); ?>" class="sub<?php if ( isset( $add_edit_autoresponder ) ) echo ' selected'; ?>"><?php echo _('Add'); ?></a>
	<?php } ?>
	-->
	<!--<a href="/mobile-marketing/settings/" title="<?php echo _('Settings'); ?>" class="top<?php if ( isset( $settings ) ) echo ' selected'; ?>"><?php echo _('Settings'); ?></a>-->
</div>