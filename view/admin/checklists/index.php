<?php
/**
 * @package Grey Suit Retail
 * @page List Checklists
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Checklists') );
?>

<table ajax="/checklists/list-all/" perPage="30,50,100">
<thead>
    <tr>
        <tr>
            <th width="10%" sort="1"><?php echo _('Days Left'); ?></th>
            <th width="30%"><?php echo _('Website'); ?></th>
            <th width="20%"><?php echo _('Online Specialist'); ?></th>
            <th width="20%"><?php echo _('Type'); ?></th>
            <th width="20%"><?php echo _('Date Created'); ?></th>
        </tr>
    </tr>
</thead>
<tbody>
</tbody>
</table>

<?php echo $template->end(); ?>