<?php
/**
 * @package Grey Suit Retail
 * @page List Pages
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( '', '../sidebar' );
?>

<table ajax="/knowledge-base/pages/list-all/" perPage="30,50,100">
    <thead>
        <tr>
            <th width="10%"><?php echo _('ID'); ?></th>
            <th width="35%" sort="1"><?php echo _('Page'); ?></th>
            <th width="55%"><?php echo _('Category'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>