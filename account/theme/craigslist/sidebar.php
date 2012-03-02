<div id="sidebar">
	<h2>Actions</h2>
	<a href="/craigslist/" title="<?php echo _('Craigslist Ads'); ?>" class="top"><?php echo _('Craigslist Ads'); ?></a>
    <?php
    global $user;

    $class_name = ( $user['role'] > 5 ) ? '' : ' last';
    ?>
	<a href="/craigslist/add-edit/" title="<?php echo _('Create Craigslist Ad'); ?>" class="sub add<?php echo $class_name; ?>"><?php echo _('Create'); ?></a>
    <?php if ( $user['role'] > 5 ) { ?>
        <a href="/craigslist/download/" title="<?php echo _('Download Ads'); ?>" class="sub last"><?php echo _('Download'); ?></a>
    <?php } ?>
</div>