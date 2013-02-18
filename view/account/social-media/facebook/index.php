<?php
/**
 * @package Grey Suit Retail
 * @page Facebook | Social Media
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Facebook'), 'sidebar' );
?>

<table perPage="25,50,100" ajax="/social-media/facebook/list-pages/">
    <thead>
        <tr>
            <th width="70%" sort="1 asc"><?php echo _('Name'); ?></th>
            <th width="30%"><?php echo _('Date Created'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>