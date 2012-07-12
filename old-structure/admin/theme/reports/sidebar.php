<div id="sidebar">
	<h2><?php echo _('Actions'); ?></h2>
	<a href="/reports/" title="<?php echo _('Websites'); ?>" class="top"><?php echo _('Websites'); ?></a>
    <a href="/reports/ashley-incomplete/" title="<?php echo _('Ashley - Incomplete Items'); ?>" class="top"><?php echo _('Ashley'); ?></a>
    <?php
    global $user;
    if ( $user['role'] > 7 || '1' == $user['company_id'] ) {
    ?>
        <a href="/reports/custom-reports/" title="<?php echo _('Custom Reports'); ?>" class="top"><?php echo _('Custom'); ?></a>
    <?php } ?>
</div>