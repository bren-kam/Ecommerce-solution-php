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

<div class="relative">
    <select id="sStatus">
        <option value="0"><?php echo _('Open'); ?></option>
        <option value="1"><?php echo _('Closed'); ?></option>
    </select>
    <table ajax="/products/reaches/list-reaches/" perPage="100,250,500">
        <thead>
            <tr>
                <th width="15%"><?php echo _('Name'); ?></th>
                <th width="22%"><?php echo _('Email'); ?></th>
                <th width="18%"><?php echo _('Assigned To'); ?></th>
                <th width="10%"><?php echo _('Waiting'); ?></th>
                <th width="10%"><?php echo _('Priority'); ?></th>
                <th width="25%"><?php echo _('Created'); ?></th>
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