<?php
/**
 * @var Template $template
 * @var User $user
 */
?>
<div id="sidebar">
    <a href="/craigslist/" title="<?php echo _('Craigslist Ads'); ?>" class="top first selected"><?php echo _('Craigslist Ads'); ?></a>
    <a href="/craigslist/add-edit/" title="<?php echo _('Add'); ?>" class="sub<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
</div>