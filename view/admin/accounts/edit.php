<?php
/**
 * @package Grey Suit Retail
 * @page Edit Account
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

?>

<div id="tabs">
    <div class="tab-link"><a href="/accounts/edit/?aid=<?php echo $account->id; ?>" class="selected" title="<?php echo _('Account'); ?>"><?php echo _('Account'); ?></a></div>
    <div class="tab-link"><a href="/accounts/edit/website-settings/?aid=<?php echo $account->id; ?>" title="<?php echo _('Website Settings'); ?>"><?php echo _('Website Settings'); ?></a></div>
    <div class="tab-link"><a href="/accounts/edit/other-settings/?aid=<?php echo $account->id; ?>" title="<?php echo _('Other Settings'); ?>"><?php echo _('Other Settings'); ?></a></div>
    <div class="tab-link"><a href="/accounts/edit/actions/?aid=<?php echo $account->id; ?>" title="<?php echo _('Actions'); ?>"><?php echo _('Actions'); ?></a></div>
    <?php if ( $user->has_permission(8) ) { ?>
        <div class="tab-link"><a href="/accounts/edit/dns/?aid=<?php echo $account->id; ?>" title="<?php echo _('DNS'); ?>"><?php echo _('DNS'); ?></a></div>
    <?php } ?>
</div>

<?php
echo $template->start();
?>

<form name="fEditAccount" action="" method="post">
<h3><?php echo _('Information'); ?></h3>
<table>
    <tr>
        <td>
            <label for="tTitle"><?php echo _('Title'); ?></label>
            <br />
            <input type="text" class="tb" name="tTitle" id="tTitle" value="<?php echo $account->title; ?>" />
        </td>
        <td>
            <label for="sOwner"><?php echo _('Owner'); ?></label>
            <br />
            <select name="sOwner" id="sOwner">
                <option value="">-- <?php echo _('Select Owner'); ?> --</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>replace</td>
        <td>replace</td>
    </tr>
    <tr>
        <td>replace</td>
        <td>
            <label for="sOnlineSpecialist"><?php echo _('Online Specialist'); ?></label>
            <br />
            <select name="sOnlineSpecialist" id="sOnlineSpecialist">
                <option value="">-- <?php echo _('Select Online Specialist'); ?> --</option>
            </select>
        </td>
    </tr>
</table>
</form>

<?php echo $template->end(); ?>