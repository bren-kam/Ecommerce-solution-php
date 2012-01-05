<?php
/**
 * @page Add Account
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// If their permissions are too low....
if ( $user['role'] < 7 )
	url::redirect( '/accounts/' );

$v = new Validator();
$v->form_name = 'fAddAccount';

$v->add_validation( 'tDomain', 'req', _('The "Domain" field is required') );

$v->add_validation( 'tTitle', 'req', _('The "Title" field is required') );

$v->add_validation( 'sUserID', 'req', _('The "User" field is required') );

// We are not successful in anything yet
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-account' ) ) {
	$errs = $v->validate();
	
	if ( empty( $errs ) ) {
		$w = new Websites;
		
		$success = $w->create( $_POST['sUserID'], $_POST['sOSUserID'], $_POST['tDomain'], '', $_POST['tTitle'], $_POST['sType'] );
	}
}
		
css( 'form', 'accounts/add' );
javascript( 'validator', 'jquery', 'accounts/add' );

$selected = 'accounts';
$title = _('Add Account') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Add Account'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'accounts/' ); ?>
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
					$error_message .= ( !empty( $error_message ) ) ? "<br />$e" : $e;
				}
				
				echo "<p class='red'>$error_message</p>";
			}
			?>
			<form action="/accounts/add/" name="fAddAccount" id="fAddAccount" method="post">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><label for="tDomain"><?php echo _('Domain'); ?>: <span class="red">*</span></label></td>
					<td><input type="text" name="tDomain" id="tDomain" maxlength="130" value="<?php if ( !$success && isset( $_POST['tDomain'] ) ) echo $_POST['tDomain']; ?>" class="tb" /></td>
				</tr>
				<?php /*<tr>
					<td><label for="tSubDomain"><?php echo _('Sub Domain'); ?>: <span class="red">*</span></label></td>
					<td><input type="text" name="tSubDomain" id="tSubDomain" maxlength="130" value="<?php if ( !$success && isset( $_POST['tSubDomain'] ) ) echo $_POST['tSubDomain']; ?>" class="tb" /></td>
				</tr>*/ ?>
				<tr>
					<td><label for="tTitle"><?php echo _('Title'); ?>: <span class="red">*</span></label></td>
					<td><input type="text" name="tTitle" id="tTitle" maxlength="80" value="<?php if ( !$success && isset( $_POST['tTitle'] ) ) echo $_POST['tTitle']; ?>" class="tb" /></td>
				</tr>
				<tr>
					<td><label for="sUserID"><?php echo _('User'); ?>: <span class="red">*</span></label></td>
					<td>
						<select name="sUserID" id="sUserID">
							<option value="">-- <?php echo _('Select a User'); ?> --</option>
							<?php
							// Get users
							$users = $u->get_users();
							
							// Loop through users
							foreach ( $users as $u ) { 
								// We don't want any empty users
								if ( empty( $u['contact_name'] ) )
									continue;
								
								$selected = ( !$success && isset( $_POST['sUserID'] ) && $_POST['sUserID'] == $u['user_id'] ) ? ' selected="selected"' : '';
							?>
							<option value="<?php echo $u['user_id']; ?>"<?php echo $selected; ?>><?php echo $u['contact_name']; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="sOSUserID"><?php echo _('Online Specialist'); ?>: <span class="red">*</span></label></td>
					<td>
						<select name="sOSUserID" id="sOSUserID">
							<option value="">-- <?php echo _('Select an Online Specialist'); ?> --</option>
							<?php 
							foreach ( $users as $u ) { 
								// We don't want any empty users
								if ( empty( $u['contact_name'] ) || $u['role'] < 7 )
									continue;
								
								$selected = ( !$success && isset( $_POST['sOSUserID'] ) && $_POST['sOSUserID'] == $u['user_id'] ) ? ' selected="selected"' : '';
							?>
							<option value="<?php echo $u['user_id']; ?>"<?php echo $selected; ?>><?php echo $u['contact_name']; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="sType"><?php echo _('Type'); ?>:</label></td>
					<td>
						<select name="sType" id="sType">
							<option value="<?php echo _('Furniture'); ?>"<?php if ( !$success && isset( $_POST['sType'] ) && 'Furniture' == $_POST['sType'] ) echo ' selected="selected"'; ?>><?php echo _('Furniture'); ?></option>
							<option value="<?php echo _('RTO'); ?>"<?php if ( !$success && isset( $_POST['sType'] ) && 'RTO' == $_POST['sType'] ) echo ' selected="selected"'; ?>><?php echo _('RTO'); ?></option>
							<option value="<?php echo _('EVR'); ?>"<?php if ( !$success && isset( $_POST['sType'] ) && 'EVR' == $_POST['sType'] ) echo ' selected="selected"'; ?>><?php echo _('EVR'); ?></option>
							<option value="<?php echo _('High Impact'); ?>"<?php if ( !$success && isset( $_POST['sType'] ) && 'High Impact' == $_POST['sType'] ) echo ' selected="selected"'; ?>><?php echo _('High Impact'); ?></option>
						</select>
					</td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="button" value="<?php echo _('Add Account'); ?>" /></td>
				</tr>
				<?php nonce::field( 'add-account' ); ?>
			</table>
			</form>
			<?php add_footer( $v->js_validation() ); ?>
		</div>
		<div id="dSuccess"<?php echo $success_class; ?>>
			<p><?php echo _('Account has been successfully added!'); ?></p>
			<p><?php echo _('Click here to <a href="/accounts/" title="View Accounts">view all accounts</a> or <a href="#" id="aAddAnother" title="Add an Account">add another</a>.'); ?></p>
		</div>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>
