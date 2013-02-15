<?php
/**
 * @package Grey Suit Retail
 * @page Groups | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Groups'), '../sidebar' );
?>

<table ajax="/products/groups/list-groups/" perPage="30,50,100">
    <thead>
        <tr>
            <th width="100%" sort="1"><?php echo _('Name'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>