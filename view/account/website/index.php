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

<div class="relative">
    <select id="sCompleted">
        <option value="0"><?php echo _('Incomplete'); ?></option>
        <option value="1"><?php echo _('Completed'); ?></option>
    </select>
    <table ajax="/website/list-all-pages/" perPage="30,50,100">
        <thead>
            <tr>
                <th width="10%" sort="1"><?php echo _('Days Left'); ?></th>
                <th width="30%"><?php echo _('Website'); ?></th>
                <th width="20%"><?php echo _('Online Specialist'); ?></th>
                <th width="20%"><?php echo _('Type'); ?></th>
                <th width="20%"><?php echo _('Date Created'); ?></th>
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