<?php
/**
 * @package Grey Suit Retail
 * @page Shopping Cart - List Orders
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Orders'), '../sidebar' );
?>

<table ajax="/shopping-cart/orders/list-orders/" perPage="30,50,100" sort="1">
    <thead>
        <tr>
            <th width="15%" sort="1 desc"><?php echo _('Order Number'); ?></th>
            <th width="20%"><?php echo _('Name'); ?></th>
            <th width="20%"><?php echo _('Price'); ?></th>
            <th width="20%"><?php echo _('Status'); ?></th>
            <th width="25%"><?php echo _('Date'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>