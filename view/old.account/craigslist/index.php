<?php
/**
 * @package Grey Suit Retail
 * @page Craigslist Ads | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Craigslist Ads') );
?>

<table ajax="/craigslist/list-all/" perPage="30,50,100">
    <thead>
        <tr>
            <th width="20%"><?php echo _('Headline'); ?></th>
            <th width="35%"><?php echo _('Content'); ?></th>
            <th width="10%"><?php echo _('Product Name'); ?></th>
            <th width="10%"><?php echo _('SKU' ); ?></th>
            <th width="10%"><?php echo _('Status'); ?></th>
            <th width="15%" sort="1 desc"><?php echo _('Created'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>