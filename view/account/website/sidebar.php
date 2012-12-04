<?php
/**
 * @var Template $template
 * @var User $user
 */
?>
<div id="sidebar">
    <a href="/website/" title="<?php echo _('Pages'); ?>" class="top first<?php $template->select('website'); ?>"><?php echo _('Pages'); ?></a>
    <?php if ( $template->v('website') ) { ?>
        <a href="/website/" title="<?php echo _('Website Pages'); ?>" class="sub view first<?php $template->select('pages'); ?>"><?php echo _('Website'); ?></a>
        <a href="/website/categories/" title="<?php echo _('Website Categories'); ?>" class="sub view<?php $template->select('categories'); ?>"><?php echo _('Categories'); ?></a>
        <a href="/website/add/" title="<?php echo _('Add Page'); ?>" class="sub add last<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
    <?php } ?>
    <a href="/website/sidebar/" title="<?php echo _('Sidebar'); ?>" class="top<?php $template->select('sidebar'); ?>"><?php echo _('Sidebar'); ?></a>
    <a href="/website/banners/" title="<?php echo _('Banners'); ?>" class="top<?php $template->select('banners'); ?>"><?php echo _('Banners'); ?></a>
    <a href="/website/sale/" title="<?php echo _('Sale'); ?>" class="top<?php $template->select('sale'); ?>"><?php echo _('Sale'); ?></a>
    <a href="/website/room-planner/" title="<?php echo _('Room Planner'); ?>" class="top<?php $template->select('room-planner'); ?>"><?php echo _('Room Planner'); ?></a>
    <a href="/website/settings/" title="<?php echo _('Settings'); ?>" class="top last<?php $template->select('settings'); ?>"><?php echo _('Settings'); ?></a>
</div>