<?php
/**
 * @package Grey Suit Retail
 * @page List Website Brands
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Website Brands') );
?>

<div class="relative">
    <table ajax="/website/list-brands/" perPage="30,50,100">
        <thead>
            <tr>
                <th width="65%"><?php echo _('Name'); ?></th>
                <th width="35%"><?php echo _('Last Updated'); ?></th>
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