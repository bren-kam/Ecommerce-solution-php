<?php
/**
 * @page Add Edit Email Lists
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Redirect to main section if they don't have email marketing
if ( !$user['website']['email_marketing'] )
	url::redirect('/email-marketing/subscribers/');

$e = new Email_Marketing;

// Get the email id if there is one
$email_list_id = ( isset( $_GET['elid'] ) ) ? $_GET['elid'] : false;

$v = new Validator();
$v->form_name = 'fAddEditEmailList';
$v->add_validation( 'tName', 'req' , 'The "Name" field is required' );

// Add validation
add_footer( $v->js_validation() );

// Initialize variable
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-email-list' ) ) {
	$errs = $v->validate();
	
	if ( NULL == $_POST['taDescription'] ) 
		$_POST['taDescription'] = '';
	
	// if there are no errors
	if ( empty( $errs ) ) {
		if ( $email_list_id ) {
			// Update email list
			$success = $e->update_email_list( $email_list_id, $_POST['tName'], $_POST['taDescription'] );
		} else {
			// Create email list
			$success = $e->create_email_list( $_POST['tName'], $_POST['taDescription'] );
		}
	}
}

// Get the email list if necessary
if ( $email_list_id ) {
	$email_list = $e->get_email_list( $email_list_id );
} else {
	$email_list = array(
		'name' => ''
		, 'description' => ''
	);
}

$selected = "email_marketing";
$sub_title = ( $email_list_id ) ? _('Edit Email List') : _('Add Email List');
$title = "$sub_title | " . _('Email Lists') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'email-marketing/', 'email_lists', 'add_edit_email_lists' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo ( $email_list_id ) ? _('Your email list has been updated successfully!') : _('Your email list has been added successfully!'); ?></p>
			<p><?php echo _('Click here to'), ' <a href="/email-marketing/email-lists/" title="', _('Email Lists'), '">', _('view your email lists'), '</a>.'; ?></p>
		</div>
		<?php 
		}
		
		// Allow them to edit the entry they just created
		if ( $success && !$email_list_id )
			$email_list_id = $success;
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		?>
		<form name="fAddEditEmailList" action="/email-marketing/email-lists/add-edit/<?php if ( $email_list_id ) echo "?elid=$email_list_id"; ?>" method="post">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><label for="tName"><?php echo _('Name'); ?>:</label></td>
					<td><input type="text" class="tb" name="tName" id="tName" maxlength="80" value="<?php echo ( !$success && isset( $_POST['tName'] ) ) ? $_POST['tName'] : $email_list['name']; ?>" /></td>
				</tr>
				<tr>
					<td valign="top"><label for="taDescription"><?php echo _('Description'); ?>:</label></td>
					<td><textarea name="taDescription" id="taDescription" cols="35" rows="6"><?php echo ( !$success && isset( $_POST['taDescription'] ) ) ? $_POST['taDescription'] : $email_list['description']; ?></textarea></td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="button" value="<?php echo ( $email_list_id ) ? _('Update Email List') : _('Add Email List'); ?>" /></td>
				</tr>
			</table>
			<?php nonce::field('add-edit-email-list'); ?>
		</form>
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>