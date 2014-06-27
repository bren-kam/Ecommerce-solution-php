<?php
/**
 * @var $template Template
 */
?>
<div id="sidebar">
    <div id="actions">
        <a href="/reports/" title="<?php echo _('Search'); ?>" class="top first<?php $template->select('search'); ?>"><?php echo _('Search'); ?></a>
        <a href="/reports/custom/" title="<?php echo _('Custom'); ?>" class="top<?php $template->select('custom'); ?>"><?php echo _('Custom'); ?></a>
    </div>
</div>