<div id="sidebar">
	<h2><?php echo _('Sidebar'); ?></h2>
	<a href="/settings/" title="<?php echo _('Settings'); ?>" class="top"><?php echo _('Settings'); ?></a>
	<?php 
	global $user;
	if ( $user['role'] > 1 ) {
	?>
	<a href="/settings/authorized-users/" title="<?php echo _('Authorized Users'); ?>" class="top"><?php echo _('Authorized Users'); ?></a>
        <?php if ( isset( $authorized_users ) ) { ?>
            <a href="/settings/add-edit-authorized-user/" title="<?php echo _('Add Authorized User'); ?>" class="sub"><?php echo _('Add'); ?></a>
        <?php
        }
    }
    ?>
    <a href="/settings/logo-and-phone/" class="top" title="<?php echo _('Logo &amp; Phone'); ?>"><?php echo _('Logo &amp; Phone'); ?></a>
</div>