<?php
/**
 * @package Grey Suit Retail
 * @page Coupons | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Coupons'), '../sidebar' );
?>

<table ajax="/products/coupons/list-coupons/" perPage="30,50,100">
    <thead>
        <tr>
            <th width="40%"><?php echo _('Name'); ?></th>
            <th width="13%"><?php echo _('Amount'); ?></th>
            <th width="13%"><?php echo _('Type'); ?></th>
            <th width="14%"><?php echo _('Item Limit'); ?></th>
            <th width="20%" sort="1"><?php echo _('Date Created'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>