<?php
/**
 * @var Template $template
 * @var User $user
 */
?>
<div id="sidebar">
    <a href="/mobile-marketing/" title="<?php echo _('Dashboard'); ?>" class="top first<?php $template->select('dashboard'); ?>"><?php echo _('Dashboard'); ?></a>

	<a href="/mobile-marketing/website/" class="top last<?php $template->select('website'); ?>" title="<?php echo _('Mobile Website'); ?>"><?php echo _('Mobile Website'); ?></a>
    <?php if ( $template->v('website') ) { ?>
    	<a href="/mobile-marketing/website/" title="<?php echo _('Pages'); ?>" class="sub<?php $template->select('website'); ?>"><?php echo _('Pages'); ?></a>
        <a href="/mobile-marketing/website/add-edit/" class="sub<?php $template->select('add'); ?>" title="<?php echo _('Add'); ?>"><?php echo _('Add'); ?></a>
    <?php } ?>
</div>