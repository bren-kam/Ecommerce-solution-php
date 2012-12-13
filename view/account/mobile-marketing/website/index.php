<?php
/**
 * @package Grey Suit Retail
 * @page Mobile Marketing - List Mobile Pages
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 */

echo $template->start( _('Mobile Marketing') );
?>
<table ajax="/mobile-marketing/website/list-pages/" perPage="30,50,100">
    <thead>
        <tr>
            <th width="65%" sort="1"><?php echo _('Title'); ?></th>
            <th width="15%"><?php echo _('Status'); ?></th>
            <th width="20%"><?php echo _('Last Updated'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>