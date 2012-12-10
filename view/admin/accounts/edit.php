<?php
/**
 * @package Grey Suit Retail
 * @page Edit Account
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var User $owner
 * @var Account $account
 * @var array $checkboxes
 */
?>

<div id="tabs">
    <div class="tab-link"><a href="/accounts/?aid=<?php echo $account->id; ?>" class="selected" title="<?php echo _('Account'); ?>"><?php echo _('Account'); ?></a></div>
    <div class="tab-link"><a href="/accounts/website-settings/?aid=<?php echo $account->id; ?>" title="<?php echo _('Website Settings'); ?>"><?php echo _('Website Settings'); ?></a></div>
    <div class="tab-link"><a href="/accounts/other-settings/?aid=<?php echo $account->id; ?>" title="<?php echo _('Other Settings'); ?>"><?php echo _('Other Settings'); ?></a></div>
    <div class="tab-link"><a href="/accounts/actions/?aid=<?php echo $account->id; ?>" title="<?php echo _('Actions'); ?>"><?php echo _('Actions'); ?></a></div>
    <?php if ( $account->craigslist ) { ?>
        <div class="tab-link"><a href="/accounts/craigslist/?aid=<?php echo $account->id; ?>" title="<?php echo _('Craigslist'); ?>"><?php echo _('Craigslist'); ?></a></div>
    <?php
    }

    if ( $user->has_permission( User::ROLE_SUPER_ADMIN ) ) {
        ?>
        <div class="tab-link"><a href="/accounts/dns/?aid=<?php echo $account->id; ?>" title="<?php echo _('DNS'); ?>"><?php echo _('DNS'); ?></a></div>
    <?php } ?>
</div>

<?php
echo $template->start();

if ( $errs )
    echo '<p class="red">' . $errs . '</p><br />';
?>

<form name="fEditAccount" action="" method="post">
    <h3><?php echo _('Information'); ?></h3>
    <table>
        <tr>
            <td>
                <label for="tTitle"><?php echo _('Title'); ?></label>
                <br />
                <?php echo $account_title; ?>
            </td>
            <td>
                <label for="sUserID"><?php echo _('Owner'); ?></label>
                <br />
                <?php echo $users; ?>
            </td>
        </tr>
        <tr>
            <td>
                <label for="tPhone"><?php echo _('Phone'); ?></label>
                <br />
                <?php echo $phone; ?>
            </td>
            <td>
                <strong><?php echo _('Email'); ?></strong>
                <br />
                <?php echo $owner->email; ?>
            </td>
        </tr>
        <tr>
            <td>
                <label for="tProducts"><?php echo _('Products'); ?></label>
                <br />
                <?php echo $products; ?>
            </td>
            <td>
                <label for="sOSUserID"><?php echo _('Online Specialist'); ?></label>
                <br />
                <?php echo $os_users; ?>
            </td>
        </tr>
        <tr>
            <td>
                <label for="tPlan"><?php echo _('Plan'); ?></label>
                <br />
                <?php echo $plan; ?>
            </td>
            <td>
                <label for="taPlanDescription"><?php echo _('Plan Description'); ?></label>
                <br />
                <?php echo $plan_description; ?>
            </td>
        </tr>
    </table>
    <br /><br />

    <h3><?php echo _('Features'); ?></h3>
    <table id="tFeatures">
        <?php
        $i = 0;
        $open = false;

        foreach ( $checkboxes as $feature => $cb ) {
            $selected = $cb['selected'] ? ' selected' : '';
            $i++;

            if ( !$open ) {
                echo '<tr>';
                $open = true;
            }
            ?>
            <td><label for="<?php echo $cb['form_name']; ?>"><?php echo $cb['name']; ?></label></td>
            <td><a href="#" class="on-off<?php echo $selected; ?>"></a><?php echo $cb['checkbox']; ?></td>
            <?php
            if ( 0 == $i % 2 ) {
                echo '</tr>';
                $open = false ;
            }
        }

        if ( $open )
            echo '</tr>';
        ?>
    </table>

    <p class="float-right"><input type="submit" class="button" value="<?php echo _('Save'); ?>" /></p>
    <br clear="right" />
    <?php nonce::field('edit'); ?>
</form>

<?php echo $template->end(); ?>