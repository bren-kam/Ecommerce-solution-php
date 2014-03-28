<?php
/**
 * @package Grey Suit Retail
 * @page Manually Priced Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AccountProduct[] $products
 */

echo $template->start( _('Manually Priced Products') );
?>


<p class="float-right">
    <a class="button" id="remove-all" href="/products/manually-priced-remove-all/?_nonce=<?php echo nonce::create( 'manually_priced_remove_all' ) ?>">Clear All</a>
</p>
<table class="dt" width="100%" perPage="100,250,500">
    <thead>
        <tr>
            <th width="30%" sort="1"><?php echo _('Product Name'); ?></th>
            <th width="20%"><?php echo _('SKU'); ?></th>
            <th width="25%"><?php echo _('Category'); ?></th>
            <th width="25%"><?php echo _('Brand'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ( is_array( $products ) )
        foreach ( $products as $product ) { ?>
            <tr>
                <td>
                    <?php echo $product->name; ?> <br>
                    <div class="actions">
                        <a class="manually-priced-remove" href="/products/manually-priced-remove/?product-id=<?php echo $product->product_id ?>"">Remove from this list</a>
                    </div>
                </td>
                <td><?php echo $product->sku; ?></td>
                <td><?php echo $product->category; ?></td>
                <td><?php echo $product->brand; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php nonce::field( 'manually_priced_remove' ) ?>

<?php echo $template->end(); ?>