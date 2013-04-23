<?php
/**
 * @package Grey Suit Retail
 * @page List Articles
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( '', '../sidebar' );
?>

<table ajax="/knowledge-base/articles/list-all/" perPage="30,50,100">
    <thead>
        <tr>
            <th width="23%" sort="1"><?php echo _('Title'); ?></th>
            <th width="25%"><?php echo _('Section'); ?></th>
            <th width="14%"><?php echo _('Phone'); ?></th>
            <th width="28%"><?php echo _('Website'); ?></th>
            <th width="10%"><?php echo _('Permission'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>