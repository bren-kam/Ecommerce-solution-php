<div id="sidebar">
	<h2><?php echo _('Sidebar'); ?></h2>
	<a href="/website/" class="top selected" title="<?php echo _('Pages'); ?>"><?php echo _('Website'); ?></a>
	<a href="/website/" class="sub<?php if ( isset( $pages ) ) echo ' selected"'; ?>" title="<?php echo _('Pages'); ?>"><?php echo _('Pages'); ?></a>
	<a href="/website/top/" class="sub<?php if ( isset( $top ) ) echo ' selected'; ?>" title="<?php echo _('Top Section'); ?>"><?php echo _('Top Section'); ?></a>
	<a href="/website/website-sidebar/" class="sub<?php if ( isset( $sidebar ) ) echo ' selected'; ?>" title="<?php echo _('Sidebar'); ?>"><?php echo _('Sidebar'); ?></a>
	<a href="/website/banners/" class="sub<?php if ( isset( $banners ) ) echo ' selected'; ?>" title="<?php echo _('Banners'); ?>"><?php echo _('Banners'); ?></a>
	<a href="/website/sale/" class="sub<?php if ( isset( $sale ) ) echo ' selected'; ?>" title="<?php echo _('Sale Page'); ?>"><?php echo _('Sale'); ?></a>
	<a href="/website/room-planner/" class="sub<?php if ( isset( $room_planner ) ) echo ' selected'; ?>" title="<?php echo _('Room Planner'); ?>"><?php echo _('Room Planner'); ?></a>
	<?php 
	global $user;
	if ( $user['role'] >= 7 ) {
	?>
	<a href="/website/add-page/" class="sub<?php if ( isset( $add_page ) ) echo ' selected'; ?>" title="<?php echo _('Add Page'); ?>"><?php echo _('Add'); ?></a>
	<a href="/website/settings/" class="sub<?php if ( isset( $settings ) ) echo ' selected'; ?>" title="<?php echo _('Settings'); ?>"><?php echo _('Settings'); ?></a>
	<?php } ?>
</div>