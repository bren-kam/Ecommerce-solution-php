<?php
/**
 * @page Add Edit Mobile Autoresponders
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Secure the section
if ( !$user['website']['mobile_marketing'] )
    url::redirect('/');

// Get Mobile Marketing
$m = new Mobile_Marketing;

// Get the mobile autoresponder id if there is one
$mobile_autoresponder_id = ( isset( $_GET['maid'] ) ) ? $_GET['maid'] : false;


$v = new Validator();
$v->form_name = 'fAddEditAutoresponder';
$v->add_validation( 'tName', 'req' , _('The "Name" field is required') );
$v->add_validation( 'taMessage', 'req', _('The "Message" field is required') );

// Add validation
add_footer( $v->js_validation() );

// Initialize variable
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-autoresponder' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) ) {
		if ( $mobile_autoresponder_id ) {
			// Update email list
			$success = $m->update_autoresponder( $mobile_autoresponder_id, $_POST['tName'], $_POST['taMessage'],$_POST['rbMobileListID'] );
		} else {
			// Create email list
			$success = $m->create_autoresponder( $_POST['tName'], $_POST['taMessage'], $_POST['rbMobileListID'] );
		}
	}
}

// Get the mobile list if necessary
if ( $mobile_autoresponder_id ) {
	$autoresponder = $m->get_autoresponder( $mobile_autoresponder_id );
} else {
	$autoresponder = array(
		'default' => ''
		, 'name' => ''
		, 'mobile_list_id' => ''
		, 'message' => ''
	);
}

$mobile_lists = $m->get_autoresponder_mobile_lists( isset( $autoresponder['mobile_list_id'] ) ? $autoresponder['mobile_list_id'] : 0 );

javascript( 'mammoth', 'mobile-marketing/autoresponders/add-edit' );

$selected = "mobile_marketing";
$sub_title = ( $mobile_autoresponder_id ) ? _('Edit Autoresponder') : _('Add Autoresponder');
$title = "$sub_title | " . _('Autoresponders') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'mobile-marketing/', 'autoresponders', 'add_edit_autoresponders' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo ( $mobile_autoresponder_id ) ? _('Your autoresponder has been updated successfully!') : _('Your autoresponder has been added successfully!'); ?></p>
			<p><?php echo _('Click here to'), ' <a href="/mobile-marketing/autoresponders/" title="', _('Autoresponders'), '">', _('view your autoresponders'), '</a>.'; ?></p>
		</div>
		<?php 
		}
		
		// Allow them to edit the entry they just created
		if ( $success && !$mobile_autoresponder_id )
			$mobile_autoresponder_id = $success;
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		?>
		<form name="fAddEditAutoresponder" action="/mobile-marketing/autoresponders/add-edit/?eaid=<?php echo $mobile_autoresponder_id; ?>" method="post">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><label for="tName"><?php echo _('Name'); ?>:</label></td>
					<?php if ( 1 == $autoresponder['default'] ) { ?>
					<td><?php echo _('Default'); ?><input type="hidden" name="tName" value="<?php echo _('Default'); ?>" /></td>
					<?php } else { ?>
					<td><input type="text" class="tb" name="tName" id="tName" maxlength="80" value="<?php echo ( !$success && isset( $_POST['tName'] ) ) ? $_POST['tName'] : $autoresponder['name']; ?>" /></td>
					<?php } ?>
				</tr>
				<tr>
					<td class="top"><label><?php echo _('Mobile List'); ?>:</label></td>
					<td class="top">
						<?php
						if ( 1 == $autoresponder['default'] ) {
							echo _('Default'), '<input type="hidden" name="rbMobileListID" value="', $autoresponder['mobile_list_id'], '" />';;
						} else {
							$i = 0;
							$mobile_list_id = ( !$success && isset( $_POST['rbMailListID'] ) ) ? $_POST['rbMailListID'] : $autoresponder['mobile_list_id'];
							
							foreach ( $mobile_lists as $ml ) {
								if ( 0 != $i ) 
									echo '<br />';
								
								$checked = ( empty( $mobile_list_id ) && 0 == $i || $mobile_list_id == $ml['mobile_list_id'] ) ? ' checked="checked"' : '';
								$i++;
							?>
							<input type="radio" class="rb" name="rbMobileListID" id="rMobileList<?php echo $ml['mobile_list_id']; ?>" value="<?php echo $ml['mobile_list_id']; ?>"<?php echo $checked; ?> /> <label for="rMobileList<?php echo $ml['mobile_list_id']; ?>"><?php echo $ml['name']; ?></label>
						<?php 
							}
						}
						?>
					</td>
				</tr>
			</table>
			<br />
			<textarea name="taMessage" id="taMessage" cols="50" rows="5"><?php echo ( !$success && isset( $_POST['taMessage'] ) ) ? $_POST['taMessage'] : $autoresponder['message']; ?></textarea>
			<br />

			<br />
			<p><input type="submit" class="button" value="<?php echo ( $mobile_autoresponder_id ) ? _('Update Autresponder') : _('Add Autoresponder'); ?>" /></p>
			<?php nonce::field('add-edit-autoresponder'); ?>
		</form>
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>