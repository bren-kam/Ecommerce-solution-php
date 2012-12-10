<?php
/**
 * @package Grey Suit Retail
 * @page List Tickets
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var array $assigned_to_users
 */

echo $template->start( _('Tickets'), false );
?>

<div class="relative">
    <select id="sStatus">
        <option value="0"><?php echo _('Open'); ?></option>
        <option value="1"><?php echo _('Closed'); ?></option>
    </select>
    <select id="sAssignedTo">
		<option value="0"><?php echo _('All'); ?></option>
		<option value="-1"><?php echo _('Peers'); ?></option>
		<?php
		foreach ( $assigned_to_users as $user_id => $contact_name ) {
			$selected = ( $user->has_permission( User::ROLE_ADMIN ) && $user_id == $user->id ) ? ' selected="selected"' : '';
			?>
			<option value="<?php echo $user_id; ?>"<?php echo $selected; ?>><?php echo $contact_name; ?></option>
		<?php } ?>
	</select>
    <table ajax="/tickets/list-all/" perPage="30,50,100">
        <thead>
            <tr>
                <th width="26%"><?php echo _('Summary'); ?></th>
                <th width="15%"><?php echo _('Name'); ?></th>
                <th width="18%" sort="3 asc"><?php echo _('Website'); ?></th>
                <th width="10%" sort="1 desc"><?php echo _('Priority'); ?></th>
                <th width="16%"><?php echo _('Assigned To'); ?></th>
                <th width="15%" sort="2 asc"><?php echo _('Created'); ?></th>
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