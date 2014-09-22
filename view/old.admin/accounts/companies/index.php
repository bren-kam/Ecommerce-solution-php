<?php
/**
 * @package Grey Suit Retail
 * @page List Companies
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */
echo $template->start( _('Companies'), '../sidebar' );
?>

<table ajax="/accounts/companies/list-all/" perPage="30,50,100">
    <thead>
        <tr>
            <th width="50%" sort="1"><?php echo _('Name'); ?></th>
            <th width="30%"><?php echo _('Domain'); ?></th>
            <th width="20%"><?php echo _('Created'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>