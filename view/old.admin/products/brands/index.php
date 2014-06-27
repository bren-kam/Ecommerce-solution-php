<?php
/**
 * @package Grey Suit Retail
 * @page List Brands
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Brands'), '../sidebar' );
?>

<table ajax="/products/brands/list-all/" perPage="30,50,100">
    <thead>
        <tr>
           <th width="30%" sort="1"><?php echo _('Brand Name'); ?></th>
            <th width="70%"><?php echo _('URL'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>