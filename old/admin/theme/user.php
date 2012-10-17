<?php
/**
 * @page User
 * @package Real Statistics
 * @subpackage Admin
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$v = new Validator();
$v->form_name = 'fUser';

$v->add_validation( 'tFirstName', 'req', _('The "First Name" field is required') );
$v->add_validation( 'tLastName', 'req', _('The "Last Name" field is required') );

$v->add_validation( 'tEmail', 'req', _('The "Email" field is required') );
$v->add_validation( 'tEmail', 'email', _('The "Email" field must contain a valid email') );

$v->add_validation( 'tUsersLimit', 'req', _('The "Users Limit" field is required') );
$v->add_validation( 'tUsersLimit', 'num', _('The "Users Limit" field may only contain a number') );

$v->add_validation( 'tPassword|tRePassword', 'match', _('The "Password" and "Confirm Password" field must match') );

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'update-user' ) ) {
	$errs = $v->validate();
	if ( empty( $errs ) ) {
		$information = array( 'account_type_id' => $_POST['sAccountType'], 'first_name' => $_POST['tFirstName'], 'last_name' => $_POST['tLastName'], 'email' => $_POST['tEmail'], 'users_limit' => $_POST['tUsersLimit'], 'monthly' => $_POST['sMonthly'], 'affiliate' => $_POST['sAffiliate'], 'status' => $_POST['sStatus'] );
		
		if ( !empty( $_POST['tPassword'] ) )
			$information['password'] = $_POST['tPassword'];
		
		$u->update_information( (int) $_GET['uid'], $information );
		$success = true;
	}
} else {
	if ( !empty( $_POST ) )
		$errs = _('A verification error occurred. Please refresh the page and try again.');
}

$us = $u->get_user( $_GET['uid'] );
$linked_users = $u->get_linked_users( $_GET['uid'] );

css( 'form', 'jquery.passwordStrengthMeter', 'user' );
javascript( 'validator', 'jquery', 'jquery.tmp-val', 'jquery.passwordStrengthMeter', 'user' );

$selected = 'users';
$title = _('User | Admin') . ' | ' . TITLE;
get_header();
?>

<div class="narrowcolumn">
	<h1><?php echo $us['first_name'], ' ', $us['last_name']; ?></h1>
	<?php 
	if ( $success ) 
		echo '<p>', $us['first_name'], ' ', $us['last_name'], ' has been successfully updated.';
	
	if ( !empty( $errs ) ) 
		echo "<p class='red'>$errs</p><br />";
	?>
	<form name="fUser" action="" method="post">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td><label for="tFirstName"><?php echo _('First Name'); ?>:</label></td>
			<td><input type="text" class="tb" name="tFirstName" id="tFirstName" maxlength="50" value="<?php echo $us['first_name']; ?>" /></td>
		</tr>
		<tr>
			<td><label for="tLastName"><?php echo _('Last Name'); ?>:</label></td>
			<td><input type="text" class="tb" name="tLastName" id="tLastName" maxlength="50" value="<?php echo $us['last_name']; ?>" /></td>
		</tr>
		<tr>
			<td><label for="tEmail"><?php echo _('Email'); ?>:</label></td>
			<td><input type="text" class="tb" name="tEmail" id="tEmail" maxlength="200" value="<?php echo $us['email']; ?>" /></td>
		</tr>
		<tr>
			<td><label for="sAccountType"><?php echo _('Account Type'); ?>:</label></td>
			<td>
				<select name="sAccountType" class="dd">
				<?php
				$account_types = array( 
					1 => _('Paying User'),
					2 => _('Non-Paying User'),
					3 => _('Management Account'),
					4 => _('Free Account')
				);
				
				foreach ( $account_types as $acid => $ac ) {
					$selected = ( $us['account_type_id'] == $acid ) ? ' selected="selected"' : '';
					
					echo '<option value="' . $acid . '"' . $selected . '>' . $ac . "</option>\n";
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="tUsersLimit"><?php echo _('Users Limit'); ?>:</label></td>
			<td><input type="text" class="tb" name="tUsersLimit" id="tUsersLimit" maxlength="4" value="<?php echo $us['users_limit']; ?>" /></td>
		</tr>
		<tr>
			<td><label for="sMonthly"><?php echo _('Monthly/Yearly'); ?>:</label></td>
			<td>
				<select name="sMonthly" class="dd">
				<?php
				$monthly_types = array( 
					1 => _('Monthly'),
					0 => _('Yearly')
				);
				
				foreach ( $monthly_types as $mv => $m ) {
					$selected = ( $us['monthly'] == $mv ) ? ' selected="selected"' : '';
					
					echo '<option value="' . $mv . '"' . $selected . '>' . $m . "</option>\n";
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="sAffiliate"><?php echo _('Affiliate'); ?>:</label></td>
			<td>
				<select name="sAffiliate" class="dd">
				<?php
				$affiliate_types = array( 
					1 => _('Affiliate'),
					0 => _('Not Affiliate')
				);
				
				foreach ( $affiliate_types as $av => $a ) {
					$selected = ( $us['affiliate'] == $av ) ? ' selected="selected"' : '';
					
					echo '<option value="' . $av . '"' . $selected . '>' . $a . "</option>\n";
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="sStatus"><?php echo _('Status'); ?>:</label></td>
			<td>
				<select name="sStatus" class="dd">
				<?php
				$status_types = array( 
					'-1' => _('Requires Activation'),
					0 => _('Inactive'),
					1 => _('Active')
				);
				
				foreach ( $status_types as $sid => $s ) {
					$selected = ( $us['status'] == $sid ) ? ' selected="selected"' : '';
					
					echo '<option value="' . $sid . '"' . $selected . '>' . $s . "</option>\n";
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="tPassword"><?php echo _('Password:'); ?></label></td>
			<td><input type="password" class="tb" name="tPassword" id="tPassword" maxlength="30" /></td>
		</tr>
		<tr>
			<td><label for="tRePassword"><?php echo _('Confirm Password:'); ?></label></td>
			<td><input type="password" class="tb" name="tRePassword" id="tRePassword" maxlength="30" /></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<div id="dPasswordStrength">
					<div id="dPSGrayBar"></div>
					<div id="dPSColorBar"></div>
					<br />
					<span id="sPSPercent">0%</span>
					&nbsp;&nbsp;
					<span id='sPSResult'><?php echo _('Enter your password'); ?></span>
				</div>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="Save" class="button" /></td>
		</tr>
	</table>
	<?php nonce::field('update-user'); ?>
	</form>
	<?php echo $v->js_validation(); ?>
	<br clear="all" />
	<?php if ( is_array( $linked_users ) ) { ?>
	<h2>Users</h2>
	<?php foreach ( $linked_users as $lu ) { ?>
	<p><a href="/user/?uid=<?php echo $lu['user_id']; ?>" title="View <?php echo $lu['name']; ?>"><?php echo $lu['name']; ?></a></p>
	<?php } } ?>
</div>

<?php get_footer(); ?>