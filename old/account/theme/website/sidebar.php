<?php global $user; ?>
<div id="sidebar">
	<h2><?php echo _('Sidebar'); ?></h2>
	<a href="/website/" class="top selected" title="<?php echo _('Pages'); ?>"><?php echo _('Pages'); ?></a>
	<a href="/website/" class="sub<?php if ( isset( $pages ) ) echo ' selected"'; ?>" title="<?php echo _('Website'); ?>"><?php echo _('Website'); ?></a>
    <a href="/website/categories/" class="sub<?php if ( isset( $categories ) ) echo ' selected"'; ?>" title="<?php echo _('Categories'); ?>"><?php echo _('Categories'); ?></a>
    <?php if ( $user['role'] >= 7 ) { ?>
        <a href="/website/add-page/" class="sub<?php if ( isset( $add_page ) ) echo ' selected'; ?>" title="<?php echo _('Add Page'); ?>"><?php echo _('Add'); ?></a>
    <?php } ?>

    <a href="/website/website-sidebar/" class="top" title="<?php echo _('Sidebar'); ?>"><?php echo _('Sidebar'); ?></a>
	<a href="/website/banners/" class="top" title="<?php echo _('Banners'); ?>"><?php echo _('Banners'); ?></a>
	<a href="/website/sale/" class="top" title="<?php echo _('Sale Page'); ?>"><?php echo _('Sale'); ?></a>
	<a href="/website/room-planner/" class="top" title="<?php echo _('Room Planner'); ?>"><?php echo _('Room Planner'); ?></a>

    <?php if ( $user['role'] >= 7 ) { ?>
	    <a href="/website/settings/" class="top<?php if ( isset( $settings ) ) echo ' selected'; ?>" title="<?php echo _('Settings'); ?>"><?php echo _('Settings'); ?></a>
	<?php } ?>
</div>