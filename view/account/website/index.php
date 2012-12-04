<?php
/**
 * @package Grey Suit Retail
 * @page List Website Pages
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Website Pages') );
?>

<div>
    <table ajax="/website/list-pages/" perPage="30,50,100">
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
</div>

<?php
nonce::field( 'store_session', '_store_session' );
echo $template->end();
?>