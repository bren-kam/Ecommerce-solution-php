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
        <?php if ( $user->account->is_new_template() ) { ?>
            <a href="/website/brands/" title="<?php echo _('Website Brands'); ?>" class="sub view<?php $template->select('brand-pages'); ?>"><?php echo _('Brands'); ?></a>
        <?php } ?>
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

    <a href="/website/settings/" title="<?php echo _('Settings'); ?>" class="top<?php $template->select('settings'); ?>"><?php echo _('Settings'); ?></a>
    <?php if ( $template->v('settings') ) { ?>
        <a href="/website/settings/" title="<?php echo _('Settings'); ?>" class="sub<?php $template->select('page-settings'); ?>"><?php echo _('Settings'); ?></a>
        <a href="/website/home-page-layout/" title="<?php echo _('Home Page Layout'); ?>" class="sub<?php $template->select('home-page-layout'); ?>"><?php echo _('Home Page Layout'); ?></a>
        <a href="/website/navigation/" title="<?php echo _('Header Navigation'); ?>" class="sub <?php $template->select('sidebar-navigation'); ?>"><?php echo _('Header Navigation'); ?></a>
        <a href="/website/footer-navigation/" title="<?php echo _('Footer Navigation'); ?>" class="sub <?php $template->select('footer-navigation'); ?>"><?php echo _('Footer Navigation'); ?></a>
        <a href="/website/header/" title="<?php echo _('Header'); ?>" class="sub <?php $template->select('website-header'); ?>"><?php echo _('Website Header'); ?></a>
        <?php if ( $user->has_permission( User::ROLE_ONLINE_SPECIALIST ) ) { ?>
            <a href="/website/html-header/" title="<?php echo _('HTML &lt;head&gt;'); ?>" class="sub <?php $template->select('html-header'); ?>"><?php echo _('HTML &lt;head&gt;'); ?></a>
        <?php } ?>

        <?php if ( $user->account->is_new_template() ) { ?>
            <a href="/website/custom-404/" title="<?php echo _('404 Page'); ?>" class="sub last<?php $template->select('custom-404'); ?>"><?php echo _('404 Page'); ?></a>
        <?php } ?>
    <?php } ?>
</div>