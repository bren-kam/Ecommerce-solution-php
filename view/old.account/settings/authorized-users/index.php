<?php
/**
 * @package Grey Suit Retail
 * @page Authorized Users | Settings | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Authorized Users'), '../sidebar' );
?>
<table ajax="/settings/authorized-users/list-all/" perPage="30,50,100">
    <thead>
        <tr>
            <th width="40%" sort="1"><?php echo _('Email'); ?></th>
            <th width="10%"><?php echo _('Pages'); ?></th>
            <th width="10%"><?php echo _('Products'); ?></th>
            <th width="10%"><?php echo _('Analytics' ); ?></th>
            <th width="10%"><?php echo _('Blog'); ?></th>
            <th width="10%"><?php echo _('Email Marketing'); ?></th>
            <th width="10%"><?php echo _('Shopping Cart'); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<?php echo $template->end(); ?>