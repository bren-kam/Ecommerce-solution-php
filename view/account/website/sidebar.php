<?php
/**
 * @var Template $template
 * @var User $user
 */

$industries = $user->account->get_industries();
?>
<div id="sidebar">
    <a href="/website/" title="<?php echo _('Pages'); ?>" class="top first<?php $template->select('pages'); ?>"><?php echo _('Pages'); ?></a>
    <?php if ( $template->v('pages') ) { ?>
        <a href="/website/" title="<?php echo _('Website Pages'); ?>" class="sub view first<?php $template->select('view'); ?>"><?php echo _('Website'); ?></a>
        <a href="/website/categories/" title="<?php echo _('Website Categories'); ?>" class="sub view<?php $template->select('category-pages'); ?>"><?php echo _('Categories'); ?></a>
        <?php if ( $user->has_permission( USER::ROLE_ONLINE_SPECIALIST ) ) { ?>
            <a href="/website/add/" title="<?php echo _('Add Page'); ?>" class="sub add last<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
        <?php
        }
    }
    ?>
    <a href="/website/sidebar/" title="<?php echo _('Sidebar'); ?>" class="top<?php $template->select('sidebar'); ?>"><?php echo _('Sidebar'); ?></a>
    <a href="/website/banners/" title="<?php echo _('Banners'); ?>" class="top<?php $template->select('banners'); ?>"><?php echo _('Banners'); ?></a>
    <a href="/website/sale/" title="<?php echo _('Sale'); ?>" class="top<?php $template->select('sale'); ?>"><?php echo _('Sale'); ?></a>
    <?php if ( in_array( Industry::FURNITURE, $industries ) && $user->account->product_catalog ) { ?>
    <a href="/website/room-planner/" title="<?php echo _('Room Planner'); ?>" class="top<?php $template->select('room-planner'); ?>"><?php echo _('Room Planner'); ?></a>
    <?php } ?>

    <?php if ( $user->has_permission( User::ROLE_ONLINE_SPECIALIST ) ) { ?>
        <a href="/website/settings/" title="<?php echo _('Settings'); ?>" class="top<?php $template->select('settings'); ?>"><?php echo _('Settings'); ?></a>
        <?php if ( $template->v('settings') ) { ?>
            <a href="/website/home-page-layout/" title="<?php echo _('Home Page Layout'); ?>" class="sub<?php $template->select('home-page-layout'); ?>"><?php echo _('Home Page Layout'); ?></a>
            <a href="/website/navigation/" title="<?php echo _('Navigation'); ?>" class="sub last<?php $template->select('sidebar-navigation'); ?>"><?php echo _('Navigation'); ?></a>
    <?php
        }
    }
    ?>
</div>