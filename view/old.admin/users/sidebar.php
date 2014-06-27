<?php
/**
 * @var $this Template
 */
?>
<div id="sidebar">
    <div id="actions">
        <a href="/users/" title="<?php echo _('Users'); ?>" class="top first<?php $this->select('users'); ?>"><?php echo _('Users'); ?></a>
        <a href="/users/" title="<?php echo _('View Users'); ?>" class="sub view first<?php $this->select('view'); ?>"><?php echo _('View'); ?></a>
        <a href="/users/add-edit/" title="<?php echo _('Add User'); ?>" class="sub add last<?php $this->select('add'); ?>"><?php echo _('Add'); ?></a>
    </div>
</div>