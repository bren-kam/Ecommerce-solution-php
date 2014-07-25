<?php
/**
 * @package Grey Suit Retail
 * @page All Products | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AccountProduct[] $products
 */

echo $template->start( _('All Products') );
?>

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
                <td><?php echo $product->name; ?></td>
                <td><?php echo $product->sku; ?></td>
                <td><?php echo $product->category; ?></td>
                <td><?php echo $product->brand; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php echo $template->end(); ?>