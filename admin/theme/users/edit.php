<?php
/**
 * @page Users - Edit
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// If user's permission level is too low, redirect.
if ( $user['role'] < 7 )
	login();

// If no one was selected, take them back to the users page
if ( empty( $_GET['uid'] ) )
	url::redirect( '/users/' );

$c = new Companies();
$v = new Validator();
$w = new Websites();

$us = $u->get_user( $_GET['uid'] );

// Make sure they have permission
if ( $user['role'] < 8 && $us['company_id'] != $user['company_id'] )
    url::redirect('/users/');

$websites = $w->get_user_websites( $_GET['uid'] );
$companies = $c->get_all();

$v->form_name = 'fEditUser';

if ( $user['role'] >= 8 )
    $v->add_validation( 'sCompany', 'req', _('The "Company" field is required') );

$v->add_validation( 'tEmail', 'req', _('The "Email" field is required') );
$v->add_validation( 'tEmail', 'email', _('The "Email" must contain a valid email') );

$v->add_validation( 'pPassword|pConfirmPassword', 'match', _('The "Password" and "Confirm Password" must match') );

$v->add_validation( 'tContactName', 'req', _('The "Contact Name" field is required') );

$v->add_validation( 'tWorkPhone', 'phone', _('The "Work" field must contain a valid phone number') );
$v->add_validation( 'tCellPhone', 'phone', _('The "Contact Name" field must contain a valid phone number') );

$v->add_validation( 'tBillingZip', 'zip', _('The "Zip" field must contain a valid zip code') );

// Add it to the footer
add_footer( $v->js_validation() );

// Set to false
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-user' ) ) {
	$errs = $v->validate();
	
	if ( empty( $errs ) ) {
        $company_id = ( $user['role'] < 8 ) ? $us['company_id'] : $_POST['sCompany'];

		$information = array(
			'company_id'			=> $company_id
			, 'email'				=> $_POST['tEmail']
			, 'contact_name'		=> stripslashes( $_POST['tContactName'] )
            , 'work_phone'          => $_POST['tWorkPhone']
			, 'cell_phone'          => $_POST['tCellPhone']
			, 'store_name'			=> $_POST['tStoreName']
			, 'billing_first_name' 	=> $_POST['tBillingFirstName']
			, 'billing_last_name' 	=> $_POST['tBillingLastName']
			, 'billing_address1' 		=> $_POST['tBillingAddress']
			, 'billing_city' 			=> $_POST['tBillingCity']
			, 'billing_state'		 	=> $_POST['sBillingState']
			, 'billing_zip'		 	=> $_POST['tBillingZip']
			, 'products'				=> $_POST['tProducts']
			, 'role'					=> ( $user['role'] >= $_POST['sRole'] ) ? intval( $_POST['sRole'] ) : intval( $user['role'] )
			, 'status'				=> $_POST['sStatus']
		);
		
		if ( !empty( $_POST['pPassword'] ) )
			$information['password'] = $_POST['pPassword'];
		
		if ( $user['role'] >= $us['role'] ) {
			$response = $u->update_information( $_GET['uid'], $information );
            $success = $response->success();

            if ( !$success )
                $errs .= $response->message();
        }
	}
}

css( 'form', 'users/edit' );
javascript( 'validator', 'jquery', 'users/edit' );

$selected = 'users';
$title = _('Edit User') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Edit User'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'users/' ); ?>
	<div id="subcontent">
		<?php 
		if ( !$success ) {
			$main_form_class = '';
			$success_class = ' class="hidden"';
			
			if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		} else {
			$success_class = '';
			$main_form_class = ' class="hidden"';
		}
		?>
		<div id="dMainForm"<?php echo $main_form_class; ?>>
			<?php 
			if ( isset( $errs ) && !empty( $errs ) ) {
				$error_message = '';
				
				foreach ( $errs as $e ) {
					$error_message .= ( !empty( $error_message ) ) ? '<br />' . $e : $e;
				}
				
				echo "<p class='red'>$error_message</p>";
			}
			?>
			<form action="/users/edit/?uid=<?php echo $_GET['uid']; ?>" name="fEditUser" id="fEditUser" method="post">
			<table cellpadding="0" cellspacing="0" style="float:left;padding-right: 20px">
				<tr><td colspan="2"><strong><?php echo _('Personal Information'); ?></strong></td></tr>
                <?php if ( $user['role'] >= 8 ) { ?>
				<tr>
					<td><label for="sCompany"><?php echo _('Company'); ?>: <span class="red">*</span></label></td>
					<td>
						<select name="sCompany" id="sCompany">
							<option value="">-- <?php echo _('Select a Company'); ?> --</option>
							<?php 
							$selected_company = ( empty( $_POST['sCompany'] ) ) ? $us['company_id'] : $_POST['sCompany'];
							foreach ( $companies as $c ) { 
								$selected = ( $selected_company == $c['company_id'] ) ? ' selected="selected"' : '';
							?>
							<option value="<?php echo $c['company_id']; ?>"<?php echo $selected; ?>><?php echo $c['name']; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
                <?php } ?>
				<tr>
					<td><label for="tEmail"><?php echo _('Email'); ?>: <span class="red">*</span></label></td>
					<td><input type="text" name="tEmail" id="tEmail" maxlength="100" value="<?php echo ( empty( $_POST['tEmail'] ) ) ? $us['email'] : $_POST['tEmail']; ?>" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="pPassword"><?php echo _('Password'); ?>:</label></td>
					<td><input type="password" name="pPassword" id="pPassword" maxlength="30" value="" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="pConfirmPassword"><?php echo _('Retype Password'); ?>:</label></td>
					<td><input type="password" name="pConfirmPassword" id="pConfirmPassword" maxlength="30" value="" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="tContactName"><?php echo _('Contact Name'); ?>: <span class="red">*</span></label></td>
					<td><input type="text" name="tContactName" id="tContactName" maxlength="80" value="<?php echo ( empty( $_POST['tContactName'] ) ) ? $us['contact_name'] : $_POST['tContactName']; ?>" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="tWorkPhone"><?php echo _('Work Phone'); ?>:</label></td>
					<td><input type="text" name="tWorkPhone" id="tWorkPhone" maxlength="80" value="<?php echo ( empty( $_POST['tWorkPhone'] ) ) ? $us['work_phone'] : $_POST['tWorkPhone']; ?>" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="tCellPhone"><?php echo _('Cell Phone'); ?>:</label></td>
					<td><input type="text" name="tCellPhone" id="tCellPhone" maxlength="80" value="<?php echo ( empty( $_POST['tCellPhone'] ) ) ? $us['cell_phone'] : $_POST['tCellPhone']; ?>" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="tStoreName"><?php echo _('Store Name'); ?>:</label></td>
					<td><input type="text" name="tStoreName" id="tStoreName" maxlength="80" value="<?php echo ( empty( $_POST['tStoreName'] ) ) ? $us['store_name'] : $_POST['tStoreName']; ?>" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="tProducts"><?php echo _('Products'); ?>:</label></td>
					<td><input type="text" name="tProducts" id="tProducts" maxlength="10" value="<?php echo ( empty( $_POST['tProducts'] ) ) ? $us['products'] : $_POST['tProducts']; ?>" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="sStatus"><?php echo _('Status'); ?>:</label></td>
					<td>
						<select name="sStatus" id="sStatus">
							<?php
							$statuses = array( 0 => 'Inactive', 1 => 'Active' );
							$selected_status = ( empty( $_POST['sStatus'] ) ) ? $us['status'] : $_POST['sStatus'];
							
							foreach ( $statuses as $s => $status_name ) { 
								$selected = ( $selected_status == $s ) ? ' selected="selected"' : '';
								
								echo '<option value="', $s, '"', $selected, '>', $status_name, '</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="sRole"><?php echo _('Role'); ?>:</label></td>
					<td>
						<select name="sRole" id="sRole">
							<?php
							$max_role = ( $user['role'] <= 10 ) ? $user['role'] : 5;
							$roles = array( 1 => _('Authorized User'), 5 => _('Basic Account'), 6 => _('Marketing Specialist'), 7 => _('Online Specialist'), 8 => _('Admin'), 10 => _('Super Admin') );
							$selected_role_number = ( empty( $_POST['sRole'] ) ) ? $us['role'] : $_POST['sRole'];
							
							for ( $i = 1; $i <= $max_role; $i++ ) { 
								$selected = ( $selected_role_number == $i ) ? ' selected="selected"' : '';
								$name = ( array_key_exists( $i, $roles ) ) ? $i . ' - ' . $roles[$i] : $i;
								
								echo '<option value="', $i, '"', $selected, '>', $name, '</option>';
							}
							?>
						</select>
					</td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="button" value="<?php echo _('Save User'); ?>" /></td>
				</tr>
			</table>
			<?php nonce::field( 'update-user' ); ?>
			<table cellpadding="0" cellspacing="0" style="float:left">
				<tr><td colspan="2"><strong><?php echo _('Billing Information'); ?></strong></td></tr>
				<tr>
					<td><label for="tBillingFirstName"><?php echo _('First Name'); ?>:</label></td>
					<td><input type="text" name="tBillingFirstName" id="tBillingFirstName" maxlength="50" value="<?php echo ( empty( $_POST['tBillingFirstName'] ) ) ? $us['billing_first_name'] : $_POST['tBillingFirstName']; ?>" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="tBillingLastName"><?php echo _('Last Name'); ?>:</label></td>
					<td><input type="text" name="tBillingLastName" id="tBillingLastName" maxlength="50" value="<?php echo ( empty( $_POST['tBillingLastName'] ) ) ? $us['billing_last_name'] : $_POST['tBillingLastName']; ?>" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="tBillingAddress"><?php echo _('Address'); ?>:</label></td>
					<td><input type="text" name="tBillingAddress" id="tBillingAddress" maxlength="100" value="<?php echo ( empty( $_POST['tBillingAddress'] ) ) ? $us['billing_address1'] : $_POST['tBillingAddress']; ?>" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="tBillingCity"><?php echo _('City'); ?>:</label></td>
					<td><input type="text" name="tBillingCity" id="tBillingCity" maxlength="100" value="<?php echo ( empty( $_POST['tBillingCity'] ) ) ? $us['billing_city'] : $_POST['tBillingCity']; ?>" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="sBillingState"><?php echo _('State'); ?>:</label></td>
					<td>
						<select name="sBillingState" id="sBillingState" class="dd">
							<option value="">-- <?php echo _('Select a State'); ?>--</option>
							<?php
							$selected_state = ( empty( $_POST['sBillingState'] ) ) ? $us['billing_state'] : $_POST['sBillingState'];
							
							data::states( true, $selected_state );
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="tBillingZip"><?php echo _('Zip'); ?>:</label></td>
					<td><input type="text" name="tBillingZip" id="tBillingZip" maxlength="10" value="<?php echo ( empty( $_POST['tBillingZip'] ) ) ? $us['billing_zip'] : $_POST['tBillingZip']; ?>" class="tb" /></td>
				</tr>
			</table>
			<br clear="all" /><br />
			</form>
			
			<?php if ( is_array( $websites ) ) { ?>
				<h2><?php echo _('Websites'); ?></h2>
				<?php
				foreach ( $websites as $w ) { ?>
				<p><a href="/accounts/edit/?wid=<?php echo $w['website_id']; ?>" title="<?php echo $w['title']; ?>"><?php echo $w['title']; ?> - <?php echo $w['domain']; ?></a> &ndash; <a href="/websites/control/?wid=<?php echo $w['website_id']; ?>" title="<?php echo _('Control'), ' ', $w['title']; ?>" target="_blank"><?php echo _('Control Website'); ?></a></p>
				<?php
				}
			}
			?>
		</div>
		<div id="dSuccess"<?php echo $success_class; ?>>
			<p><?php echo _('User has been successfully updated!'); ?></p>
			<p><?php echo _('Click here to <a href="/users/" title="View Users">view all users</a> or <a href="#" id="aUpdateAnother" title="Update a User">continue to update this user</a>.'); ?></p>
			<br /><br />
			<br /><br />
		</div>
		<br /><br />
	</div>
</div>

<?php get_footer(); ?>