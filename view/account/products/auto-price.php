<?php
/**
 * @package Grey Suit Retail
 * @page Auto Price | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Auto Price') );
?>

<p><?php echo _('On this page you set all of your prices based on the whole sale price.'); ?></p>
<br /><br />
<br />
<p><?php echo _('Please enter in the multipliers in the fields below before uploading. A "0" will be ignored.'); ?></p>
<form name="" method="post">
<table class="col-4">
    <tr>
        <td><?php echo _('Price'); ?>:</td>
        <td><input type="text" class="tb" style="width: 50px" id="price" value="3" /></td>
    </tr>
    <tr>
        <td><?php echo _('Sale Price'); ?>:</td>
        <td><input type="text" class="tb" style="width: 50px" id="sale-price" value="2" /></td>
    </tr>
    <tr>
        <td><?php echo _('Alternate Price'); ?>:</td>
        <td><input type="text" class="tb" style="width: 50px" id="alternate-price" value="0" /></td>
    </tr>
</table>
<br />
<a href="#" id="aMultiplyPrices" class="button" title="<?php echo _('Auto Price'); ?>"><?php echo _('Auto Price'); ?></a>
<div class="hidden" id="multiply-prices"></div>
<?php nonce::field( 'multiply_prices', '_multiply_prices' ); ?>
<br /><br />
<br /><br />

<?php echo $template->end(); ?>