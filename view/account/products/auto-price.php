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
 * @var Brand[] $brands
 * @var WebsiteAutoPrice[] $auto_prices
 * @var array $auto_price_candidates
 * @var Product $product
 */
?>

<div id="tabs">
    <div class="tab-link"><a href="/products/auto-price/" class="selected" title="<?php echo _('Auto Price'); ?>"><?php echo _('Auto Price'); ?></a></div>
    <div class="tab-link"><a href="/products/price-multiplier/" title="<?php echo _('Price Multiplier'); ?>"><?php echo _('Price Multiplier'); ?></a></div>
    <div class="tab-link"><a href="/products/product-prices/" title="<?php echo _('Product Prices'); ?>"><?php echo _('Product Prices'); ?></a></div>
</div>

<?php echo $template->start( _('Auto Price') ); ?>

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
            <th><?php echo _('Brand'); ?></th>
            <th><?php echo _('Category'); ?></th>
            <th><?php echo _('MSRP'); ?></th>
            <th><?php echo _('Everyday Price'); ?></th>
            <th><?php echo _('Sale Price'); ?></th>
            <th><?php echo _('Price Ending'); ?></th>
            <th><?php echo _('Action'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $remove_auto_price = nonce::create('remove_auto_price');
        $delete_auto_price = nonce::create('delete_auto_price');
        $run_auto_prices = nonce::create('run_auto_prices');

        foreach ( $auto_prices as $auto_price ) {
            $brand_name = ( 0 == $auto_price->brand_id ) ? _('All') : $brands[$auto_price->brand_id]->name;
            $category = Category::$categories[$auto_price->category_id];
            ?>
            <tr id="ap_<?php echo $auto_price->brand_id; ?>_<?php echo $auto_price->category_id; ?>">
                <td>
                    <?php echo $brand_name; ?>
                </td>
                <td>
                    <?php echo $category->name; ?>
                </td>
                <td><input type="text" class="tb" name="auto-price[<?php echo $auto_price->brand_id; ?>][<?php echo $auto_price->category_id; ?>][alternate_price]" value="<?php echo (float) $auto_price->alternate_price; ?>"></td>
                <td><input type="text" class="tb" name="auto-price[<?php echo $auto_price->brand_id; ?>][<?php echo $auto_price->category_id; ?>][price]" value="<?php echo (float) $auto_price->price; ?>"></td>
                <td><input type="text" class="tb" name="auto-price[<?php echo $auto_price->brand_id; ?>][<?php echo $auto_price->category_id; ?>][sale_price]" value="<?php echo (float) $auto_price->sale_price; ?>"></td>
                <td><input type="text" class="tb" name="auto-price[<?php echo $auto_price->brand_id; ?>][<?php echo $auto_price->category_id; ?>][ending]" value="<?php echo (float) $auto_price->ending; ?>"></td>
                <td>
                    <a href="<?php echo url::add_query_arg( array( 'bid' => $auto_price->brand_id, 'cid' => $category->id, '_nonce' => $run_auto_prices ), '/products/run-auto-prices/' ); ?>" ajax="1" confirm='<?php echo _('Make sure you have pressed "Save" before continuing.'); ?>'><?php echo _('Run'); ?></a> |
                    <a href="<?php echo url::add_query_arg( array( 'bid' => $auto_price->brand_id, 'cid' => $category->id, '_nonce' => $remove_auto_price ), '/products/remove-auto-price/' ); ?>" ajax="1" confirm="<?php echo _('Are you sure you want to remove these prices? This cannot be undone.'); ?>"><?php echo _('Remove Prices From All Products'); ?></a> |
                    <a href="<?php echo url::add_query_arg( array( 'bid' => $auto_price->brand_id, 'cid' => $category->id, '_nonce' => $delete_auto_price ), '/products/delete-auto-price/' ); ?>" ajax="1" confirm="<?php echo _('Are you sure you want to delete this row? This cannot be undone.'); ?>"><?php echo _('Delete'); ?></a>
                </td>
            </tr>
            <?php } ?>
        <tr>
            <td>
                <select id="brand">
                    <option value="0">-- All --</option>
                    <?php foreach ( $brands as $brand ) { ?>
                    <option value="<?php echo $brand->id; ?>"><?php echo $brand->name; ?></option>
                    <?php } ?>
                </select>
            </td>
            <td>
                <select id="category">
                    <?php foreach ( $categories as $category ) { ?>
                    <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                    <?php } ?>
                </select>
            </td>
            <td><input type="text" class="tb" id="alternate_price"></td>
            <td><input type="text" class="tb" id="price"></td>
            <td><input type="text" class="tb" id="sale_price"></td>
            <td><input type="text" class="tb" id="ending"></td>
            <td><a href="#" class="button" id="add" title="<?php echo _('Add'); ?>"><?php echo _('Add'); ?></a></td>
        </tr>
    </tbody>
</table>
<input type="submit" class="button" value="<?php echo _('Save'); ?>">
<?php nonce::field('auto_price'); ?>
</form>
<br><br>
<br><br>

<div id="example">
    <h2><?php echo _('Demo - Try Me!'); ?></h2>
    <br>
    <p>Here you can try out your calculations to see how they would work. We have randomly selected an auto-pricing-eligible product from your catalog.</p>
    <p>The wholesale price is: <strong><span class="big">$<?php echo number_format( $product->price, 2 ); ?></span>.</strong></p>
    <p>Please check the help article if you have any questions!</p>
    <br>
    <table>
        <thead>
            <tr>
                <th><?php echo _('Brand'); ?></th>
                <th><?php echo _('Category'); ?></th>
                <th><?php echo _('MSRP'); ?></th>
                <th><?php echo _('Everyday Price'); ?></th>
                <th><?php echo _('Sale Price'); ?></th>
                <th><?php echo _('Price Ending'); ?></th>
                <th><?php echo _('Action'); ?></th>
            </tr>
        </thead>
        <tbody>
        <tr>
        <td>
            <?php echo $brands[$product->brand_id]->name; ?>
        </td>
        <td>
            <?php echo Category::$categories[$product->category_id]->name; ?>
        </td>
        <td><input type="text" class="tb" id="example_alternate_price" value="2"></td>
        <td><input type="text" class="tb" id="example_price" value="1"></td>
        <td><input type="text" class="tb" id="example_sale_price" value="0.5"></td>
        <td><input type="text" class="tb" id="example_ending"></td>
        <td><a href="#" class="button" id="update" title="<?php echo _('Update'); ?>"><?php echo _('Update'); ?></a></td>
    </tr>
    </tbody>
    </table>
    <br clear="left"><br>
    <img id="example-image" src="http://<?php echo $product->industry; ?>.retailcatalog.us/products/<?php echo $product->id; ?>/<?php echo current( $product->images ); ?>" alt="<?php echo $product->name; ?>" align="left">
    <h2 id="example-name"><?php echo $product->name; ?></h2>
    <br><br>
    <p id="example-price">
        <span id="example-sale-price" class="sale-price" data-original-price="<?php echo $product->price; ?>">$<?php echo number_format( $product->price * 1.5, 2 ); ?></span>
        <span class="strikethrough" id="example-regular-price">$<?php echo number_format( $product->price * 2, 2 ); ?></span>
        <br>
        <span class="msrp" id="example-msrp">(MSRP $<?php echo str_replace( '.00', '', number_format( $product->price * 3, 2 ) ); ?>)</span>
    </p>
    <p>
        SKU: <?php echo $product->sku; ?><br>
        Brand: <?php echo $product->brand; ?>
    </p>
    <br clear="left"><br>
</div>
<br><br>
<?php echo $template->end(); ?>