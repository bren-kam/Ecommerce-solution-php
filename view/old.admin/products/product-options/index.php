<?php
/**
 * @package Grey Suit Retail
 * @page List Product Options
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Product Options'), '../sidebar' );

?>

<table ajax="/products/product-options/list-all/" perPage="30,50,100">
    <thead>
        <tr>
            <th width="40%" sort="1"><?php echo _('Title'); ?></th>
            <th width="40%"><?php echo _('Name'); ?></th>
            <th width="20%"><?php echo _('Type'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>