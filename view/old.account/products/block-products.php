<?php
/**
 * @package Grey Suit Retail
 * @page Block Products | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 * @var Product[] $blocked_products
 */

echo $template->start( _('Block Products') );
?>
<p><?php echo _("Separate SKU's by putting one on each line."); ?></p>
<?php echo $form; ?>
<br /><br />
<?php if ( !empty( $blocked_products ) ) { ?>
    <h2><?php echo _('Blocked Products'); ?></h2>
    <br />
    <form action="/products/unblock-products/" method="post" name="fUnblockProducts">
        <table class="width-auto">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th class="text-left"><strong><?php echo _('Name'); ?></strong></th>
                    <th class="text-left"><strong><?php echo _('SKU'); ?></strong></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $blocked_products as $product ) { ?>
                <tr>
                    <td><input type="checkbox" class="cb" name="unblock-products[]" value="<?php echo $product->id; ?>" /></td>
                    <td><?php echo $product->name; ?></td>
                    <td><?php echo $product->sku; ?></td>
                </tr>
                <?php } ?>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" class="button" value="<?php echo _('Unblock Products'); ?>" /></td>
                </tr>
            </tbody>
        </table>
        <?php nonce::field('unblock_products'); ?>
    </form>
<?php
}

echo $template->end();
?>