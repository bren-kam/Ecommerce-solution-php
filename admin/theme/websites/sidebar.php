<div id="sidebar">
	<h2><?php echo _('Actions'); ?></h2>
	<a href="/websites/" title="<?php echo _('Websites'); ?>" class="top"><?php echo _('Websites'); ?></a>
	<a href="/websites/" title="<?php echo _('View Websites'); ?>" class="sub view first"><?php echo _('View'); ?></a>
	<?php 
	global $user;
	if ( $user['role'] >= 7 ) { 
	?> 
    <a href="/websites/add/" title="<?php echo _('Add Website'); ?>" class="sub add last"><?php echo _('Add'); ?></a>
	<?php } ?>
    <?php global $sidebar_emails; if ( $user['role'] >= 9 && $sidebar_emails ) { ?>
	<a href="/websites/manage_emails/?wid=<?php echo $_GET['wid']; ?>" title="<?php echo _('Manage Emails'); ?>" class="sub last"><?php echo _('Manage Emails'); ?></a>
	<a href="/websites/manage_forwarders/?wid=<?php echo $_GET['wid']; ?>" title="<?php echo _('Manage Forwarders'); ?>" class="sub last"><?php echo _('Manage Forwarders'); ?></a>
	<?php } ?>
</div>