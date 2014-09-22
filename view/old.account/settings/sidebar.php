<?php
/**
 * @var Template $template
 * @var User $user
 */
?>
<div id="sidebar">
    <a href="/settings/" title="<?php echo _('Settings'); ?>" class="top first<?php $template->select('settings'); ?>"><?php echo _('Settings'); ?></a>

    <a href="/settings/authorized-users/" title="<?php echo _('Authorized Users'); ?>" class="top <?php $template->select('authorized-users'); ?>"><?php echo _('Authorized Users'); ?></a>
    <?php if ( $template->v('authorized-users') ) { ?>
    <a href="/settings/authorized-users/add-edit/" title="<?php echo _('Add'); ?>" class="sub add<?php $template->select('add-edit'); ?>"><?php echo _('Add'); ?></a>
    <?php } ?>

    <a href="/settings/logo-and-phone/" title="<?php echo _('Logo and Phone'); ?>" class="top last<?php $template->select('logo-and-phone'); ?>"><?php echo _('Logo and Phone'); ?></a>
</div>