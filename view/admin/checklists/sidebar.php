<?php
/**
 * @var $this Template
 */
?>
<div id="sidebar">
    <a href="/checklists/" title="<?php echo _('Checklists'); ?>" class="top first<?php $this->select('checklists'); ?>"><?php echo _('Checklists'); ?></a>
    <a href="/checklists/" title="<?php echo _('View Checklists'); ?>" class="sub view first<?php $this->select('view'); ?>"><?php echo _('View'); ?></a>
    <a href="/checklists/manage/" title="<?php echo _('Manage Checklists'); ?>" class="sub last<?php $this->select('manage'); ?>"><?php echo _('Manage Checklists'); ?></a>
</div>