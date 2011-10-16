<?php
/**
 * @page Settings
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	login();

$v = new Validator();
$v->form_name = 'fSettings';

$v->add_validation( 'tContactName', 'req', _('The "Contact Name" field is required') );

if( $user['role'] > 1 )
	$v->add_validation( 'tStoreName', 'req', _('The "Store Name" field is required') );

$v->add_validation( 'tPassword|tVerifyPassword', 'match', _('The Password and Confirm Password must match') );
$v->add_validation( 'tWorkPhone', 'phone', _('The "Work Phone" field must contain a valid phone number') );
$v->add_validation( 'tCellPhone', 'phone', _('The "Cell Phone" field must contain a valid phone number') );

// Add validation
add_footer( $v->js_validation() );

// Make sure it's a valid request
if( nonce::verify( $_POST['_nonce'], 'update-account-settings' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if( empty( $errs ) ) {
		// Set the informatin
		$information = array( 'contact_name' => $_POST['tContactName'], 'store_name' => $_POST['tStoreName'], 'work_phone' => $_POST['tWorkPhone'], 'cell_phone' => $_POST['tCellPhone'] );
		
		// If they are an Authorize User, they can't set the store name
		if( 1 == $user['role'] )
			unset( $information['store_name'] );
		
		// Only set the password if necessary
		if( !empty( $_POST['tPassword'] ) )
			$information['password'] = $_POST['tPassword'];
		
		// Update user
		$success = $u->update_information( $user['user_id'], $information );
		
		// Refresh user information - @Fix shouldn't have to do the query twice
		if( $success )
			$user = $u->get_user( $user['user_id'] );
	}
}

$title = _('Settings') . ' | ' . TITLE;

get_header();
?>

<div id="content">
	<h1><?php echo _('Account Settings'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'settings/' ); ?>
	<div id="subcontent">
		<?php if( $success ) { ?>
		<div class="success">
			<p><?php echo _('Your information has been successfully updated!'); ?></p>
		</div>
		<?php 
		}
		
		if( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		?>
		<form action="" method="post" name="fSettings">
			<table cellpadding="0" cellspacing="0">
				<tr><td colspan="2" class="title"><strong><?php echo _('Login Information'); ?></strong></td></tr>
				<tr>
					<td><span class="label"><?php echo _('Email'); ?></span></td>
					<td><span class="value"><?php echo $user['email']; ?></span></td>
				</tr>
				<tr>
					<td><label for="tPassword"><?php echo _('Password'); ?></label></td>
					<td><input type="password" class="tb" id="tPassword" name="tPassword" size="20" value="" /></td>
				</tr>
				<tr>
					<td><label for="tVerifyPassword"><?php echo _('Confirm Password'); ?></label></td>
					<td><input type="password" class="tb" id="tVerifyPassword" name="tVerifyPassword" size="20" value="" /></td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr><td colspan="2" class="title"><strong><?php echo _('Personal Information'); ?></strong></td></tr>
				<tr>
					<td><label for="tContactName"><?php echo _('Contact Name'); ?></label></td>
					<td><input type="text" class="tb" id="tContactName" name="tContactName" value="<?php echo $user['contact_name'] ?>" /></td>
				</tr>
				<?php if( $user['role'] > 1 ) { ?>
				<tr>
					<td><label for="tStoreName"><?php echo _('Store Name'); ?></label></td>
					<td><input type="text" class="tb" id="tStoreName" name="tStoreName" value="<?php echo $user['store_name'] ?>" /></td>
				</tr>
				<?php } ?>
				<tr>
					<td><label for="tWorkPhone"><?php echo _('Work Phone'); ?></label></td>
					<td><input type="text" class="tb" id="tWorkPhone" name="tWorkPhone" value="<?php echo $user['work_phone'] ?>" /></td>
				</tr>
				<tr>
					<td><label for="tCellPhone"><?php echo _('Cell Phone'); ?></label></td>
					<td><input type="text" class="tb" id="tCellPhone" name="tCellPhone" value="<?php echo $user['cell_phone'] ?>" /></td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" value="<?php echo _('Update Information'); ?>" class="button" /></td>
				</tr>
			</table>
			<?php nonce::field('update-account-settings'); ?>
		</form>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>