<?php
/**
 * @var User $user
 * @var $template Template
 */
?>
<div id="sidebar">
    <div id="actions">
        <a href="/accounts/" title="<?php echo _('Accounts'); ?>" class="top first"><?php echo _('Accounts'); ?></a>
        <a href="/accounts/companies/" title="<?php echo _('Companies'); ?>" class="top"><?php echo _('Companies'); ?></a>
        <a href="<?php echo url::add_query_arg( 'aid', $_GET['aid'], '/accounts/customize/settings/' ); ?>" title="<?php echo _('Customize'); ?>" class="top<?php $template->select('customize'); ?>"><?php echo _('Customize'); ?></a>
        <a href="<?php echo url::add_query_arg( 'aid', $_GET['aid'], '/accounts/customize/settings/' ); ?>" title="<?php echo _('Settings'); ?>" class="sub first<?php $template->select('settings'); ?>"><?php echo _('Settings'); ?></a>
        <a href="<?php echo url::add_query_arg( 'aid', $_GET['aid'], '/accounts/customize/stylesheet/' ); ?>" title="<?php echo _('CSS'); ?>" class="sub<?php $template->select('stylesheet'); ?>"><?php echo _('CSS'); ?></a>
        <a href="<?php echo url::add_query_arg( 'aid', $_GET['aid'], '/accounts/customize/favicon/' ); ?>" title="<?php echo _('Favicon'); ?>" class="sub last<?php $template->select('favico'); ?>"><?php echo _('Favicon'); ?></a>
    </div>
</div>