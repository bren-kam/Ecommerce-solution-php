<?php
/**
 * @package Grey Suit Retail
 * @page Autoresponders | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Autoresponders'), '../sidebar' );
?>

<table ajax="/email-marketing/autoresponders/list-all/" perPage="30,50,100">
    <thead>
        <tr>
            <th width="50%" sort="1"><?php echo _('Name'); ?></th>
            <th width="50%"><?php echo _('Subject'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>