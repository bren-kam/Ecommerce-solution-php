<div id="sidebar">
	<h2><?php echo _('Actions'); ?></h2>
	<a href="/accounts/" title="<?php echo _('Accounts'); ?>" class="top"><?php echo _('Accounts'); ?></a>
	<a href="/accounts/" title="<?php echo _('View Accounts'); ?>" class="sub view first"><?php echo _('View'); ?></a>
	<?php 
	global $user;
	if ( $user['role'] >= 7 ) { 
	?> 
    <a href="/accounts/add/" title="<?php echo _('Add Account'); ?>" class="sub add last"><?php echo _('Add'); ?></a>
	<?php } ?>
</div>