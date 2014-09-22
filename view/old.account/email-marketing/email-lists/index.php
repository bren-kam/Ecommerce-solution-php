<?php
/**
 * @package Grey Suit Retail
 * @page Email Lists | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Email Lists'), '../sidebar' );
?>

<table ajax="/email-marketing/email-lists/list-all/" perPage="30,50,100">
    <thead>
        <tr>
            <th width="40%" sort="1"><?php echo _('Name'); ?></th>
            <th width="40%"><?php echo _('Description'); ?></th>
            <th width="20%"><?php echo _('Date Created'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>