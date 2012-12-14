<?php
/**
 * @package Grey Suit Retail
 * @page Shopping Cart - List Users
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Shopping Cart'), '../sidebar' );
?>

<table ajax="/shopping-cart/users/list-users/" perPage="30,50,100" sort="1">
    <thead>
        <tr>
            <th width="40%"><?php echo _('Email'); ?></th>
            <th width="20%"><?php echo _('First Name'); ?></th>
            <th width="20%"><?php echo _('Status'); ?></th>
            <th width="20%"><?php echo _('Date' ); ?></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<?php echo $template->end(); ?>