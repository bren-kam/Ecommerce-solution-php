<?php
/**
 * @package Grey Suit Retail
 * @page List Attributes
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Attributes'), '../sidebar' );
?>

<table ajax="/products/attributes/list-all/" perPage="30,50,100">
    <thead>
        <tr>
            <th width="100%" sort="1"><?php echo _('Attribute Name'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>