<?php
/**
 * @package Grey Suit Retail
 * @page Craigslist > Add Links
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Account $account
 * @var array $account_market_links
 * @var array $craigslist_markets
 */

?>

<div id="tabs">
    <div class="tab-link"><a href="/accounts/edit/?aid=<?php echo $account->id; ?>" title="<?php echo _('Account'); ?>"><?php echo _('Account'); ?></a></div>
    <div class="tab-link"><a href="/accounts/website-settings/?aid=<?php echo $account->id; ?>" title="<?php echo _('Website Settings'); ?>"><?php echo _('Website Settings'); ?></a></div>
    <div class="tab-link"><a href="/accounts/other-settings/?aid=<?php echo $account->id; ?>" title="<?php echo _('Other Settings'); ?>"><?php echo _('Other Settings'); ?></a></div>
    <div class="tab-link"><a href="/accounts/actions/?aid=<?php echo $account->id; ?>" title="<?php echo _('Actions'); ?>"><?php echo _('Actions'); ?></a></div>
    <?php if ( $account->craigslist ) { ?>
        <div class="tab-link"><a href="/accounts/craigslist/?aid=<?php echo $account->id; ?>" class="selected" title="<?php echo _('Craigslist'); ?>"><?php echo _('Craigslist'); ?></a></div>
    <?php
    }

    if ( $user->has_permission( User::ROLE_SUPER_ADMIN ) ) {
        ?>
        <div class="tab-link"><a href="/accounts/dns/?aid=<?php echo $account->id; ?>" title="<?php echo _('DNS'); ?>"><?php echo _('DNS'); ?></a></div>
    <?php } ?>
</div>

<?php echo $template->start( _('Craigslist') ); ?>

<p><?php echo _('At this point in time you cannot modify or edit a market once added. Please add carefully. If you have trouble, please submit a ticket.'); ?></p>
<?php
if ( $errs )
    echo '<p class="red">' . $errs . '</p>';
?>
<br />

<form name="fLinkMarket" action="" method="post">
    <table style="width: auto;">
        <tr>
            <td><strong><?php echo _('Account'); ?>:</strong></td>
            <td><?php echo $account->title; ?></td>
        </tr>
        <tr>
            <td><strong><?php echo _('Linked Markets'); ?>:</strong></td>
            <td>
                <?php
                if ( count( $account_market_links ) > 0 ) {
                    foreach ( $account_market_links as $aml ) {
                        echo "<p>$aml</p>";
                    }
                } else {
                    echo '<p>', _('You have not linked any markets yet.'), '</p>';
                }
                ?>
            </td>
        </tr>
        <tr>
            <td><label for="sMarketId"><?php echo _('Market'); ?>:</label></td>
            <td>
                <select name="sMarketId" id="sMarketId">
                    <option value="">-- <?php echo _('Select Market'); ?> --</option>
                    <?php
                    /**
                     * @var CraigslistMarket $cm
                     */
                    if ( is_array( $craigslist_markets ) )
                    foreach ( $craigslist_markets as $cm ) {
                        if ( 0 == $cm->cl_market_id )
                            continue;
                        ?>
                        <option value="<?php echo $cm->craigslist_market_id; ?>" rel="<?php echo $cm->cl_market_id; ?>"><?php echo $cm->market; ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="sCLCategoryId"><?php echo _('Category'); ?>:</label></td>
            <td>
                <select name="sCLCategoryId" id="sCLCategoryId" rel="<?php echo _('Loading'); ?>">
                    <option value="">-- <?php echo _('Select Market'); ?> --</option>
                </select>
            </td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>
            <td>&nbsp;</td>
            <td><input type="submit" class="button" value="<?php echo _('Link Market'); ?>" /></td>
        </tr>
    </table>
    <?php nonce::field('craigslist'); ?>
</form>
<input type="hidden" id="hAccountId" value="<?php echo $account->id; ?>" />

<?php
nonce::field( 'get_craigslist_market_categories', '_get_craigslist_market_categories' );

echo $template->end();
?>