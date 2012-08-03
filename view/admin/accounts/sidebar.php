<?php
/**
 * @var User $user
 */
?>
<div id="sidebar">
	<h2><?php echo _('Actions'); ?></h2>
    <div id="actions">
        <a href="/accounts/" title="<?php echo _('Accounts'); ?>" class="top first"><?php echo _('Accounts'); ?></a>
        <?php if ( isset( $accounts ) ) { ?>
            <a href="/accounts/" title="<?php echo _('View Accounts'); ?>" class="sub view first"><?php echo _('View'); ?></a>
            <?php if ( $user->has_permission(7) ) { ?>
                <a href="/accounts/add/" title="<?php echo _('Add Account'); ?>" class="sub add last"><?php echo _('Add'); ?></a>
            <?php
            }
        }

        if ( $user->has_permission(10) ) {
            ?>
            <a href="/companies/" title="<?php echo _('Companies'); ?>" class="top"><?php echo _('Companies'); ?></a>
            <?php if ( isset( $companies ) ) { ?>
                <a href="/companies/" title="<?php echo _('View Companies'); ?>" class="sub view first"><?php echo _('View'); ?></a>
                <a href="/companies/add-edit/" title="<?php echo _('Add Company'); ?>" class="sub add last"><?php echo _('Add'); ?></a>
                <?php
            }
        }
        ?>
    </div>
</div>