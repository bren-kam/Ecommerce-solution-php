<?php
/**
 * @package Grey Suit Retail
 * @page Auto Price | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Category[] $categories
 * @var WebsiteAutoPrice $auto_price
 * @var array $auto_price_candidates
 */

echo $template->start( _('Auto Price') );
?>

<p><?php echo _('On this page you set all of your prices based on the wholesale price.'); ?></p>
<p><?php echo _('Please enter in the percent increase in the fields below before clicking the "Auto Price" button. A "0" will be ignored.'); ?></p>
<p><a href="/products/download-non-autoprice-products/" title="<?php echo _('Downloads Products'); ?>"><?php echo _('Click here'); ?></a> <?php echo _('to download a spreadsheet of all items that cannot be priced using the auto price.' ); ?></p>
<br><br>
<?php if ( empty( $auto_price_candidates ) ) { ?>
    <p><?php echo _('This would affect none of your current products.'); ?></p>
<?php } else { ?>
    <p><?php echo _('This would affect the following products:'); ?></p>
    <ul>
        <?php foreach ( $auto_price_candidates as $candidate ) { ?>
        <li> * <?php echo $candidate['brand'] . ' - ' . $candidate['count'] . ' ' . _('product(s)'); ?></li>
        <?php } ?>
    </ul>
<?php } ?>
<br>
    <p><strong><?php echo _('NOTE'); ?>:</strong> <?php echo _('Please enter all prices as the percentage increase over the wholesale price.'); ?></p>
<br>
<form name="fAutoPrice" method="post">
<table id="auto-prices">
    <thead>
        <tr>
            <th><?php echo _('Category'); ?></th>
            <th><?php echo _('Price'); ?></th>
            <th><?php echo _('Sale Price'); ?></th>
            <th><?php echo _('Alternate Price'); ?></th>
            <th><?php echo _('Ending'); ?></th>
            <th><?php echo _('Price New Items'); ?></th>
            <th><?php echo _('Action'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $reset_auto_prices = nonce::create('reset_auto_prices');
        $run_auto_prices = nonce::create('run_auto_prices');
        foreach ( $categories as $category ) {
            $auto_price = new WebsiteAutoPrice();
            $auto_price->get_by_category( $user->account->id, $category->category_id );
            ?>
        <tr>
            <td>
                <?php echo $category->name; ?>
            </td>
            <td><input type="text" class="tb" name="auto-price[<?php echo $category->id; ?>][price]" value="<?php echo $auto_price->price; ?>"></td>
            <td><input type="text" class="tb" name="auto-price[<?php echo $category->id; ?>][sale_price]" value="<?php echo $auto_price->sale_price; ?>"></td>
            <td><input type="text" class="tb" name="auto-price[<?php echo $category->id; ?>][alternate_price]" value="<?php echo $auto_price->alternate_price; ?>"></td>
            <td><input type="text" class="tb" name="auto-price[<?php echo $category->id; ?>][ending]" value="<?php echo $auto_price->ending; ?>"></td>
            <td><input type="checkbox" name="auto-price[<?php echo $category->id; ?>][future]" value="1"<?php if ( $auto_price->future ) echo ' checked="checked"'; ?>></td>
            <td>
                <a href="<?php echo url::add_query_arg( array( 'cid' => $category->id, '_nonce' => $run_auto_prices ), '/products/run-auto-prices/' ); ?>" ajax="1" confirm='<?php echo _('Make sure you have pressed "Save" before continuing.'); ?>'><?php echo _('Run Now'); ?></a> |
                <a href="<?php echo url::add_query_arg( array( 'cid' => $category->id, '_nonce' => $reset_auto_prices ), '/products/reset-auto-prices/' ); ?>" ajax="1" confirm="<?php echo _('Are you sure you want to reset these prices? This cannot be undone.'); ?>"><?php echo _('Reset Prices'); ?></a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<input type="submit" class="button" value="<?php echo _('Save'); ?>">
<?php nonce::field('auto_price'); ?>
</form>
<br /><br />
<br /><br />

<?php echo $template->end(); ?>