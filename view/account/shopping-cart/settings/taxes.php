<?php
/**
 * @package Grey Suit Retail
 * @page Shopping Cart - Taxes
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 * @var array $taxes
 * @var array $states
 */

echo $template->start( _('Taxes'), '../sidebar' );
?>

<form name="fTaxes" id="fTaxes" action="/shopping-cart/settings/taxes/" method="post">
    <table id="tWebsiteTaxes" width="700">
        <tr>
            <th width="40%"><strong><?php echo _('State'); ?></strong></th>
            <th><strong><?php echo _('Cost'); ?></strong></th>
            <th width="40%"><strong><?php echo _('Actions'); ?></strong></th>
        </tr>
        <?php
        if ( is_array( $taxes['states'] ) )
        foreach ( $taxes['states'] as $abbr => $tax ) {
            ?>
            <tr id="trTax<?php echo $abbr; ?>">
                <td>
                    <a href="#" class="zip-codes" title="<?php echo _('Edit Tax Zip Codes'); ?>"><span><?php echo $states[$abbr]; ?></span></a>
                    <textarea name="zip_codes[<?php echo $abbr; ?>]" class="hidden" col="50" rows="3" tmpval="[Zip] [Cost]"><?php
                        if ( isset( $taxes['zip_codes'][$abbr] ) )
                        foreach ( $taxes['zip_codes'][$abbr] as $zip => $cost ) {
                            echo $zip, ' ', $cost, "\n";
                        }
                    ?></textarea>
                </td>
                <td><input type="text" class="tb" name="states[<?php echo $abbr; ?>]" id="tState<?php echo $abbr; ?>" value="<?php echo $tax; ?>" maxlength="5" /></td>
                <td><a href="#" class="delete-state" id="aDeleteTax<?php echo $abbr; ?>" title="<?php echo _('Delete Tax'); ?>"><img width="15" height="17" alt="<?php echo _('Delete'); ?>" src="/images/icons/x.png"></a></td>
            </tr>
        <?php } ?>
        <tr id="trAddTax">
            <td>
                <select name="sState" id="sState">
                    <option value="">-- <?php echo _('Select a State'); ?> --</option>
                    <?php
                    foreach ( $states as $key => $state ) {
                    ?>
                    <option class="<?php if ( isset( $taxes['states'] ) && array_key_exists( $key, $taxes['states'] ) ) echo ' hidden'; ?>" value="<?php echo $key; ?>"><?php echo $state; ?></option>
                    <?php } ?>
                </select>
            </td>
            <td><input type="text" class="tb" name="tAmount" id="tAmount" maxlength="5" tmpval="<?php echo _('Amount'); ?>" /></td>
            <td><a href="#" class="button" id="aAddTax" title="<?php echo _('Add Tax'); ?>" error="<?php echo _('Please enter in a state and tax amount.'); ?>"><?php echo _('Add Tax'); ?></a></td>
        </tr>
    </table>
    <br />
    <input type="submit" class="button" value="<?php echo _('Save Changes'); ?>" />
    <?php nonce::field('taxes'); ?>
</form>

<?php echo $template->end(); ?>