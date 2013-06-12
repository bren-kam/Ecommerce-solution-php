<?php
/**
 * @package Grey Suit Retail
 * @page Product Prices | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Brand[] $brands
 * @var Category[] $categories
 */

echo $template->start( _('Product Prices') );
?>
<h2><?php echo _('1. Select a brand and/or category'); ?></h2>
<br />
<select id="sBrand">
    <option value="">-- <?php echo _('Select Brand'); ?> --</option>
    <?php foreach ( $brands as $brand ) { ?>
        <option value="<?php echo $brand->id; ?>"><?php echo $brand->name; ?></option>
    <?php } ?>
</select>
<br /><br />
<select id="sCategory">
    <option value="">-- <?php echo _('Select Category'); ?> --</option>
    <?php foreach ( $categories as $category ) { ?>
        <option value="<?php echo $category->id; ?>"><?php echo str_repeat( '&nbsp;', $category->depth * 5 ); echo $category->name; ?></option>
    <?php } ?>
</select>
<br /><br />
<p class="float-right" id="pButton"><span class="hidden success" id="sSaveMessage"><?php echo _('Your products have been saved.'); ?></span> <input type="button" class="button" id="bSave" value="<?php echo _('Save'); ?>" /></p>
<br />
<h2><?php echo _('2. Enter the values and click Save'); ?></h2>
<br clear="right" />
<br /><br />
<br />
<?php nonce::field( 'set_product_prices', '_set_product_prices' ); ?>
<table id="tProductPrices">
    <thead>
        <tr>
            <th class="text-left"><?php echo _('SKU'); ?></th>
            <th class="text-left"><?php echo _('Name'); ?></th>
            <th class="text-left"><?php echo _('Price'); ?></th>
            <th class="text-left"><?php echo _('Price Notes'); ?></th>
            <th class="text-left"><?php echo _('Alternate Price Name'); ?></th>
            <th class="text-left"><?php echo _('Alternate Price'); ?></th>
            <th><?php echo _('Sale Price'); ?></th>
        </tr>
    </thead>
</table>
<br clear="right" /><br />
<p class="float-right" id="pButton2"><span class="hidden success" id="sSaveMessage2"><?php echo _('Your products have been saved.'); ?></span> <input type="button" class="button" id="bSave2" value="<?php echo _('Save'); ?>" /></p>
<br clear="right" />
<?php
echo $template->end();
?>