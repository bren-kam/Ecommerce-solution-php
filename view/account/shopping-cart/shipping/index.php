<?php
/**
 * @package Grey Suit Retail
 * @page Shopping Cart - List Shipping Methods
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Shipping Methods'), '../sidebar' );
?>

<table ajax="/shopping-cart/shipping/list-shipping-methods/" perPage="30,50,100" sort="1">
    <thead>
        <tr>
            <th sort="1"><?php echo _('Name'); ?></th>
            <th><?php echo _('Type'); ?></th>
            <th><?php echo _('Method'); ?></th>
            <th><?php echo _('Amount'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>