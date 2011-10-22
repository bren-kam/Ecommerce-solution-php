<div id="sidebar">
	<h2><?php echo _('Sidebar'); ?></h2>
	<a href="/analytics/" title="<?php echo _('Dashboard'); ?>" class="top"><?php echo _('Dashboard'); ?></a>
	<a href="/analytics/content-overview/" title="<?php echo _('Content Overview'); ?>" class="top"><?php echo _('Content Overview'); ?></a>
	
	<a href="/analytics/traffic-sources-overview/" title="<?php echo _('Traffic Sources'); ?>" class="top"><?php echo _('Traffic Sources'); ?></a>
	<?php if ( isset( $traffic_sources_overview ) ) { ?>
	<a href="/analytics/traffic-sources/" title="<?php echo _('View Sources'); ?>" class="sub"><?php echo _('Sources'); ?></a>
	<a href="/analytics/traffic-keywords/" title="<?php echo _('View Keywords'); ?>" class="sub"><?php echo _('Keywords'); ?></a>
	<?php } ?>
	
	<a href="/analytics/visitors/" title="<?php echo _('Visitors'); ?>" class="top"><?php echo _('Visitors'); ?></a>
	<?php 
	global $user;
	if ( $user['website']['email_marketing'] ) {
	?>
	<a href="/analytics/email-marketing/" title="<?php echo _('Email Marketing'); ?>" class="top"><?php echo _('Email Marketing'); ?></a>
	<?php } ?>
	
	<?php
	$w = new Websites;
	$settings = $w->get_settings( 'facebook-url' );
	if ( !empty( $settings['facebook-url'] ) ) {
	?>
	<a href="<?php echo $settings['facebook-url']; ?>" title="<?php echo _('Facebook'); ?>" class="top" target="_blank"><?php echo _('Facebook'); ?></a>
	<?php } ?>
</div>