<?php
/**
 * @page Users - Add
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	url::redirect( '/login/' );

// If user's permission level is too low, redirect.
if( $user['role'] < 7 )
	url::redirect( '/login/' );

$c = new Companies();
$v = new Validator();

$companies = $c->get_all();

$v->form_name = 'fAddUser';

$v->add_validation( 'sCompany', 'req', _('The "Company" field is required') );

$v->add_validation( 'tEmail', 'req', _('The "Email" field is required') );
$v->add_validation( 'tEmail', 'email', _('The "Email" must contain a valid email') );

$v->add_validation( 'pPassword', 'req', _('The "Password" field is required') );
$v->add_validation( 'pPassword|pConfirmPassword', 'match', _('The "Password" and "Confirm Password" must match') );

$v->add_validation( 'tContactName', 'req', _('The "Contact Name" field is required') );

// Add it to the footer
add_footer( $v->js_validation() );

// Set to false
$success = false;

if( nonce::verify( $_POST['_nonce'], 'add-user' ) ) {
	$errs = $v->validate();
	
	if( empty( $errs ) ) {
		if( $user['role'] <= 8 ) $_POST['sCompany'] = $user['company_id'];	
		$success = $u->create( $_POST['sCompany'], $_POST['tEmail'], $_POST['pPassword'], $_POST['tContactName'], $_POST['tStoreName'], ( $user['role'] >= $_POST['sRole'] ) ? intval( $_POST['sRole'] ) : intval( $user['role'] ) );
	}
}

css( 'form', 'users/add' );
javascript( 'validator', 'jquery', 'users/add' );

$selected = 'users';
$title = _('Add User') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Add User'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'users/' ); ?>
	<div id="subcontent">
		<?php 
		if( !$success ) {
			$main_form_class = '';
			$success_class = ' class="hidden"';
			
			if( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		} else {
			$success_class = '';
			$main_form_class = ' class="hidden"';
		}
		?>
		<div id="dMainForm"<?php echo $main_form_class; ?>>
			<?php 
			if( isset( $errs ) && !empty( $errs ) ) {
				$error_message = '';
				
				foreach( $errs as $e ) {
					$error_message .= ( !empty( $error_message ) ) ? '<br />' . $e : $e;
				}
				
				echo "<p class='red'>$error_message</p>";
			}
			?>
			<form action="/users/add/" name="fAddUser" id="fAddUser" method="post">
			<table cellpadding="0" cellspacing="0">
				<?php
                // If their role is too low, only show them their own company.
				if( $user['role'] > 8 ) { ?>
				<tr>
					<td><label for="sCompany"><?php echo _('Company'); ?>: <span class="red">*</span></label></td>
					<td>
                        <select name="sCompany" id="sCompany">
							<option value="">-- <?php echo _('Select a Company'); ?> --</option>
							<?php 
							foreach( $companies as $c ) { 
								$selected = ( !$success && $_POST['sCompany'] == $c['company_id'] ) ? ' selected="selected"' : '';
							?>
							<option value="<?php echo $c['company_id']; ?>"<?php echo $selected; ?>><?php echo $c['name']; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
                <?php 
				} else {
					echo '<input id="sCompany" name="sCompany" type="hidden" value="' . $user['company_id'] . '"/>';
				}
				?>
				<tr>
					<td><label for="tEmail"><?php echo _('Email'); ?>: <span class="red">*</span></label></td>
					<td><input type="text" name="tEmail" id="tEmail" maxlength="100" value="<?php if( !$success ) echo $_POST['tEmail']; ?>" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="pPassword"><?php echo _('Password'); ?>: <span class="red">*</span></label></td>
					<td><input type="password" name="pPassword" id="pPassword" maxlength="30" value="" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="pConfirmPassword"><?php echo _('Retype Password'); ?>: <span class="red">*</span></label></td>
					<td><input type="password" name="pConfirmPassword" id="pConfirmPassword" maxlength="30" value="" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="tContactName"><?php echo _('Contact Name'); ?>: <span class="red">*</span></label></td>
					<td><input type="text" name="tContactName" id="tContactName" maxlength="80" value="<?php if( !$success ) echo $_POST['tContactName']; ?>" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="tStoreName"><?php echo _('Store Name'); ?>:</label></td>
					<td><input type="text" name="tStoreName" id="tStoreName" maxlength="80" value="<?php if( !$success ) echo $_POST['tStoreName']; ?>" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="sRole"><?php echo _('Role'); ?>:</label></td>
					<td>
						<select name="sRole" id="sRole">
							<?php
							$max_role = ( $user['role'] <= 10 ) ? $user['role'] : 5;
							$roles = array( 1 => 'Basic User', 5 => 'Basic Account', 7 => 'Online Specialist', 8 => 'Admin', 10 => 'Super Admin' );
							$selected_role_number = ( !$success && isset( $_POST['sRole'] ) ) ? $_POST['sRole'] : 5;
							
							for( $i = 1; $i <= $max_role; $i++ ) { 
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
					<td><input type="submit" class="button" value="<?php echo _('Add User'); ?>" /></td>
				</tr>
				<?php nonce::field( 'add-user' ); ?>
			</table>
			</form>
		</div>
		<div id="dSuccess"<?php echo $success_class; ?>>
			<p><?php echo _('User has been successfully created!'); ?></p>
			<p><?php echo _('Click here to <a href="/users/" title="View Users">view all users</a> or <a href="#" id="aAddAnother" title="Add a User">add another</a>.'); ?></p>
		</div>
		<br /><br />
	</div>
</div>

<?php get_footer(); ?>