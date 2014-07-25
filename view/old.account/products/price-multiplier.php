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
?>

<div id="tabs">
    <div class="tab-link"><a href="/products/auto-price/" title="<?php echo _('Auto Price'); ?>"><?php echo _('Auto Price'); ?></a></div>
    <div class="tab-link"><a href="/products/price-multiplier/" class="selected" title="<?php echo _('Price Multiplier'); ?>"><?php echo _('Price Multiplier'); ?></a></div>
    <div class="tab-link"><a href="/products/product-prices/" title="<?php echo _('Product Prices'); ?>"><?php echo _('Product Prices'); ?></a></div>
</div>

<?php echo $template->start( _('Price Multiplier') ); ?>

<p>Please make your spreadsheet layout match the example below. For example, include SKU, Price and Note in the top row. Also, the SKU needs to be in the far left column, Price directly to the right of SKU, and Note directly to the right of Price. And as a reminder, SKU and Price are mandatory fields, the Note is optional and this field will add a Price Note to your products.</p>
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
        <td><?php echo _('MSRP'); ?>:</td>
        <td><input type="text" class="tb" style="width: 50px" id="alternate-price" value="0" /></td>
    </tr>
</table>
<br />
<a href="#" id="aMultiplyPrices" class="button" title="<?php echo _('Multiply Prices'); ?>"><?php echo _('Upload Spreadsheet and Run'); ?></a>
<div class="hidden" id="multiply-prices"></div>
<?php nonce::field( 'multiply_prices', '_multiply_prices' ); ?>
<br /><br />
<br /><br />

<?php echo $template->end(); ?>