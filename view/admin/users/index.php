<?php
/**
 * @package Grey Suit Retail
 * @page List Users
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start();
?>

<table ajax="/users/list-all/" perPage="30,50,100">
<thead>
    <tr>
        <th width="23%" sort="1"><?php echo _('Name'); ?></th>
        <th width="25%"><?php echo _('Email'); ?></th>
        <th width="14%"><?php echo _('Phone'); ?></th>
        <th width="28%"><?php echo _('Website'); ?></th>
        <th width="10%"><?php echo _('Permission'); ?></th>
    </tr>
</thead>
<tbody>
</tbody>
</table>

<?php echo $template->end(); ?>