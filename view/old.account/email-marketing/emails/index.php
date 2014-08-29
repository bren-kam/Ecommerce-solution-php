<?php
/**
 * @package Grey Suit Retail
 * @page Email Messages | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Email Messages'), '../sidebar' );
?>
<table ajax="/email-marketing/emails/list-all/" perPage="30,50,100">
    <thead>
        <tr>
            <th width="50%"><?php echo _('Subject'); ?></th>
            <th width="20%"><?php echo _('Status'); ?></th>
            <th width="30%" sort="1 desc"><?php echo _('Date'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>