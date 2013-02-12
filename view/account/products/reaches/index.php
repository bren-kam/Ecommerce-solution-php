<?php
/**
 * @package Grey Suit Retail
 * @page Reaches | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Reaches'), '../sidebar' );
?>

<table ajax="/products/reaches/list-reaches/" perPage="100,250,500">
    <thead>
        <tr>
            <th width="15%"><?php echo _('Name'); ?></th>
            <th width="22%"><?php echo _('Email'); ?></th>
            <th width="18%"><?php echo _('Assigned To'); ?></th>
            <th width="10%"><?php echo _('Status'); ?></th>
            <th width="10%"><?php echo _('Priority'); ?></th>
            <th width="25%"><?php echo _('Created'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>