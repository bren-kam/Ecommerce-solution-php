<?php
/**
 * @var User $user
 * @var $template Template
 */
?>
<div id="sidebar">
    <div id="actions">
        <a href="/accounts/" title="<?php echo _('Accounts'); ?>" class="top first<?php $template->select('accounts'); ?>"><?php echo _('Accounts'); ?></a>
        <?php if ( isset( $accounts ) && true === $accounts ) { ?>
            <a href="/accounts/" title="<?php echo _('View Accounts'); ?>" class="sub view first<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
            <?php if ( $user->has_permission(7) ) { ?>
                <a href="/accounts/add/" title="<?php echo _('Add Account'); ?>" class="sub add last<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
            <?php
            }
        }

        if ( $user->has_permission(10) ) {
            ?>
            <a href="/accounts/companies/" title="<?php echo _('Companies'); ?>" class="top<?php $template->select('companies'); ?>"><?php echo _('Companies'); ?></a>
            <?php if ( isset( $companies ) && true === $companies ) { ?>
                <a href="/accounts/companies/" title="<?php echo _('View Companies'); ?>" class="sub view first<?php $template->select('view'); ?>"><?php echo _('View'); ?></a>
                <a href="/accounts/companies/add-edit/" title="<?php echo _('Add Company'); ?>" class="sub add last<?php $template->select('add'); ?>"><?php echo _('Add'); ?></a>
                <?php
            }
        }
        ?>
    </div>
</div>