<div id="sidebar">
	<h2><?php echo _('Sidebar'); ?></h2>
	<a href="/settings/" title="<?php echo _('Settings'); ?>" class="top"><?php echo _('Settings'); ?></a>
	<?php 
	global $user;
	if ( $user['role'] > 1 ) {
	?>
	<a href="/settings/authorized-users/" title="<?php echo _('Authorized Users'); ?>" class="top"><?php echo _('Authorized Users'); ?></a>
	<?php } ?>
</div>