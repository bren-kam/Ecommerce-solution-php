<?php
/**
 * @package Grey Suit Retail
 * @page Edit Account > Actions
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Account $account
 */

?>

<div id="tabs">
    <div class="tab-link"><a href="/accounts/edit/?aid=<?php echo $account->id; ?>" title="<?php echo _('Account'); ?>"><?php echo _('Account'); ?></a></div>
    <div class="tab-link"><a href="/accounts/edit/website-settings/?aid=<?php echo $account->id; ?>" title="<?php echo _('Website Settings'); ?>"><?php echo _('Website Settings'); ?></a></div>
    <div class="tab-link"><a href="/accounts/edit/other-settings/?aid=<?php echo $account->id; ?>" title="<?php echo _('Other Settings'); ?>"><?php echo _('Other Settings'); ?></a></div>
    <div class="tab-link"><a href="/accounts/edit/actions/?aid=<?php echo $account->id; ?>" class="selected" title="<?php echo _('Actions'); ?>"><?php echo _('Actions'); ?></a></div>
    <?php if ( $user->has_permission(8) ) { ?>
        <div class="tab-link"><a href="/accounts/edit/dns/?aid=<?php echo $account->id; ?>" title="<?php echo _('DNS'); ?>"><?php echo _('DNS'); ?></a></div>
    <?php } ?>
</div>

<?php
$template->get_sidebar( '../sidebar' );
echo $template->start();
echo $template->start_subcontent();
?>

<h3><?php echo _('DNS'); ?></h3>

<?php
echo $template->end_subcontent();
echo $template->end();
?>