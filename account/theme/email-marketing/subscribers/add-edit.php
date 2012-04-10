<?php
/**
 * @page Add Edit Subscriber
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$e = new Email_Marketing;

// Get the email id if there is one
$email_id = ( isset( $_GET['eid'] ) ) ? $_GET['eid'] : false;

$v = new Validator();
$v->form_name = 'fAddEditEmail';
$v->add_validation( 'tEmail', 'req' , 'The "Email" field is required' );
$v->add_validation( 'tEmail', 'email' , 'The "Email" field must contain a valid email' );

$v->add_validation( 'tPhone', 'phone' , 'The "Phone" field may only contain a valid phone number' );

// Add validation
add_footer( $v->js_validation() );

// Initialize variable
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-email' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) ) {
		if ( $email_id ) {
			// Update email
			$success = $e->update_email_lists_subscription( $email_id, $_POST['cbEmailLists'] ) && $e->update_email( $email_id, $_POST['tEmail'], $_POST['tName'], $_POST['tPhone'] );
		} else {
			// Add email
			if ( $em = $e->email_exists( $_POST['tEmail'] ) && '2' == $em['status'] ) {
				$errs .= _('This email has been unsubscribed by the user.') . '<br />';
				$success = false;
			} else if ( $email && '1' == $email['status'] ) {
				$success = $e->update_email_lists_subscription( $email_id, $_POST['cbEmailLists'] ) && $e->update_email( $email_id, $_POST['tEmail'], $_POST['tName'], $_POST['tPhone'] );
				
				if ( !$success )
					$errs .= _('An error occurred while adding email address.') . '<br />';
			} else {
				$success = $e->create_email( $_POST['tEmail'], $_POST['tName'], $_POST['tPhone'] );
				
				if ( !$success ) {
					$errs .= _('An error occurred while adding email address.') . '<br />';
				} else {
					$success = $e->update_email_lists_subscription( $success, $_POST['cbEmailLists'] );
					
					if ( !$success )
						$errs .= _('An error occurred while adding email address.') . '<br />';
				}
			}
		}
	}
}

// Get email lists
$email_lists = $e->get_email_lists();

// Get the email if necessary
if ( $email_id ) {
	$email = $e->get_email( $email_id );
} else {
	// Initialize variable
	$email = array(
		'name' => ''
		, 'email' => ''
		, 'phone' => ''
		, 'email_lists' => ''
	);
}

$selected = "email_marketing";
$sub_title = ( $email_id ) ? _('Edit Email') : _('Add Email');
$title = "$sub_title | " . _('Email Subscribers') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'email-marketing/', 'subscribers', 'add_edit_email_subscribers' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo ( $email_id ) ? _('Your email has been updated successfully!') : _('Your email has been added successfully!'); ?></p>
			<p><?php echo _('Click here to'), ' <a href="/email-marketing/subscribers/" title="', _('Subscribers'), '">', _('view your subscribers'), '</a>.'; ?></p>
		</div>
		<?php 
		}
		
		// Allow them to edit the entry they just created
		if ( $success && !$email_id )
			$email_id = $success;
		
		if ( isset( $errs ) && !empty( $errs ) )
            echo "<p class='red'>$errs</p>";
		?>
		<form name="fAddEditEmail" action="/email-marketing/subscribers/add-edit/<?php if ( $email_id ) echo "?eid=$email_id"; ?>" method="post">
			<?php nonce::field( 'add-edit-email' ); ?>
			<table cellpadding="0" cellspacing="0">
				<tr><td colspan="2" class="title"><strong><?php echo _('Basic Information'); ?></strong></td></tr>
				<tr>
					<td><label for="tName"><?php echo _('Name'); ?>:</label></td>
					<td><input type="text" class="tb" name="tName" id="tName" maxlength="80" value="<?php echo ( !$success && isset( $_POST['tName'] ) ) ? $_POST['tName'] : $email['name']; ?>" /></td>
				</tr>
				<tr>
					<td><label for="tEmail"><?php echo _('Email'); ?>:</label></td>
					<td><input type="text" class="tb" name="tEmail" id="tEmail" maxlength="200" value="<?php echo ( !$success && isset( $_POST['tEmail'] ) ) ? $_POST['tEmail'] : $email['email']; ?>" /></td>
				</tr>
				<tr>
					<td><label for="tPhone"><?php echo _('Phone'); ?>:</label></td>
					<td><input type="text" class="tb" name="tPhone" id="tPhone" maxlength="20" value="<?php echo ( !$success && isset( $_POST['tPhone'] ) ) ? $_POST['tPhone'] : $email['phone']; ?>" /></td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr><td colspan="2" class="title"><strong><?php echo _('Email List Subscriptions'); ?></strong></td></tr>
				<tr>
					<td>
					<p>
						<?php 
						$selected_email_lists = ( !$success && isset( $_POST['cbEmailLists'] ) ) ? $_POST['cbEmaiLists'] : $email['email_lists'];
						
						if ( !is_array( $selected_email_lists ) )
							$selected_email_lists = array();
						
						foreach ( $email_lists as $el ) {
							$checked = ( in_array( $el['email_list_id'], $selected_email_lists ) ) ? ' checked="checked"' : '';
						?>
						<input type="checkbox" class="cb" name="cbEmailLists[]" id="cbEmailList<?php echo $el['email_list_id']; ?>" value="<?php echo $el['email_list_id']; ?>"<?php echo $checked; ?> /> <label for="cbEmailList<?php echo $el['email_list_id']; ?>"><?php echo $el['name']; ?></label>
						<br />
						<?php } ?>
					</p>
					</td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="button" value="<?php echo ( $email_id ) ? _('Update Email') : _('Add Email'); ?>" /></td>
				</tr>
			</table>
		</form>
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>