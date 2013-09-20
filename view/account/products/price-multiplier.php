<?php
/**
 * @package Grey Suit Retail
 * @page Price Multiplier | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Price Multiplier') );
?>

<p><?php echo _('On this page you can upload a list of prices indexed by SKU.'); ?></p>
<p><?php echo _('Please make your spreadsheet layout match the example below.'); ?></p>
<p><?php echo _('Example:'); ?></p>
<table class="generic col-2">
    <tr>
        <th width="50%"><?php echo _('SKU'); ?></th>
        <th><?php echo _('Price'); ?></th>
        <th><?php echo _('Note'); ?></th>
    </tr>
    <tr>
        <td>A123</td>
        <td>400</td>
        <td></td>
    </tr>
    <tr>
        <td>B456</td>
        <td>359.99</td>
        <td>Includes Chair</td>
    </tr>
    <tr class="last">
        <td>...</td>
        <td>...</td>
        <td></td>
    </tr>
</table>
<br /><br />
<br />
<p><?php echo _('Please enter in the multipliers in the fields below before uploading. A "0" will be ignored.'); ?></p>
<table class="col-4">
    <tr>
        <td><?php echo _('Price'); ?>:</td>
        <td><input type="text" class="tb" style="width: 50px" id="price" value="1" /></td>
    </tr>
    <tr>
        <td><?php echo _('Sale Price'); ?>:</td>
        <td><input type="text" class="tb" style="width: 50px" id="sale-price" value="0" /></td>
    </tr>
    <tr>
        <td><?php echo _('Alternate Price'); ?>:</td>
        <td><input type="text" class="tb" style="width: 50px" id="alternate-price" value="0" /></td>
    </tr>
</table>
<br />
<a href="#" id="aMultiplyPrices" class="button" title="<?php echo _('Multiply Prices'); ?>"><?php echo _('Multiply Prices'); ?></a>
<div class="hidden" id="multiply-prices"></div>
<?php nonce::field( 'multiply_prices', '_multiply_prices' ); ?>
<br /><br />
<br /><br />

<?php echo $template->end(); ?>