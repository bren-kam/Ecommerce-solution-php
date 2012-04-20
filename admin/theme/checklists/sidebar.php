<div id="sidebar">
	<h2><?php echo _('Actions'); ?></h2>
	<a href="/checklists/" title="<?php echo _('Checklists'); ?>" class="top"><?php echo _('Checklists'); ?></a>
		<a href="/checklists/" title="<?php echo _('In Progress'); ?>" class="sub first"><?php echo _('In Progress'); ?></a>
		<a href="/checklists/completed/" title="<?php echo _('Completed'); ?>" class="sub"><?php echo _('Completed'); ?></a>
		<!--<a href="/checklists/add-checklist/" title="<?php echo _('Add Checklist'); ?>" class="sub last"><?php echo _('Add Checklist'); ?></a>-->
    <?php
    global $user;
    if ( $user['role'] >= 8 ) {
    ?>
        <a href="/checklists/manage/" title="<?php echo _('Manage Checklists'); ?>" class="top"><?php echo _('Manage Checklists'); ?></a>
    <?php } ?>
</div>