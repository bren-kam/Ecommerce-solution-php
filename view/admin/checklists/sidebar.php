<?php
/**
 * @var Template $template
 * @var User $user
 */
?>
<div id="sidebar">
    <a href="/checklists/" title="<?php echo _('Checklists'); ?>" class="top first<?php $template->select('checklists'); ?>"><?php echo _('Checklists'); ?></a>
    <a href="/checklists/" title="<?php echo _('View Checklists'); ?>" class="sub view first<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
    <?php if ( $user->has_permission( User::ROLE_ADMIN ) ) { ?>
        <a href="/checklists/manage/" title="<?php echo _('Manage Checklists'); ?>" class="sub last<?php $template->select('manage'); ?>"><?php echo _('Manage Checklists'); ?></a>
    <?php } ?>
</div>