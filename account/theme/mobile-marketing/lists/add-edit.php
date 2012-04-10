<?php
/**
 * @page Add Edit Mobile Lists
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Secure the section
if ( !$user['website']['mobile_marketing'] )
    url::redirect('/');

$m = new Mobile_Marketing;

// Get the mobile id if there is one
$mobile_list_id = ( isset( $_GET['mlid'] ) ) ? $_GET['mlid'] : false;

$v = new Validator();
$v->form_name = 'fAddEditMobileList';
$v->add_validation( 'tName', 'req', _('The "Name" field is required') );

$v->add_validation( 'tFrequency', 'req', _('The "Frequency" field is required') );
$v->add_validation( 'tFrequency', 'int', _('The "Frequency" field may only contain numbers') );

$v->add_validation( 'tDescription', 'req' , 'The "Description" field is required' );
$v->add_validation( 'tDescription', 'maxlen=50' , 'The "Description" field must contain 50 characters or less' );

// Add validation
add_footer( $v->js_validation() );

// Initialize variable
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-mobile-list' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) ) {
		if ( $mobile_list_id ) {
			// Update mobile list
			$success = $m->update_mobile_list( $mobile_list_id, $_POST['tName'], $_POST['tFrequency'], $_POST['tDescription'] );
		} else {
			// Create mobile list
			$success = $m->create_mobile_list( $_POST['tName'], $_POST['tFrequency'], $_POST['tDescription'] );
		}
	}
}

// Get the mobile list if necessary
if ( $mobile_list_id ) {
	$mobile_list = $m->get_mobile_list( $mobile_list_id );
} else {
	$mobile_list = array(
		'name' => ''
        , 'frequency' => '10'
        , 'description' => 'Offers'
	);
}

$selected = "mobile_marketing";
$sub_title = ( $mobile_list_id ) ? _('Edit Mobile List') : _('Add Mobile List');
$title = "$sub_title | " . _('Mobile Lists') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'mobile-marketing/', 'mobile_lists', 'add_edit_mobile_lists' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo ( $mobile_list_id ) ? _('Your mobile list has been updated successfully!') : _('Your mobile list has been added successfully!'); ?></p>
			<p><?php echo _('Click here to'), ' <a href="/mobile-marketing/lists/" title="', _('Mobile Lists'), '">', _('view your mobile lists'), '</a>.'; ?></p>
		</div>
		<?php 
		}
		
		// Allow them to edit the entry they just created
		if ( $success && !$mobile_list_id )
			$mobile_list_id = $success;
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		?>
		<form name="fAddEditMobileList" action="/mobile-marketing/lists/add-edit/<?php if ( $mobile_list_id ) echo "?mlid=$mobile_list_id"; ?>" method="post">
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><label for="tName"><?php echo _('Name'); ?>:</label></td>
					<td><input type="text" class="tb" name="tName" id="tName" maxlength="80" value="<?php echo ( !$success && isset( $_POST['tName'] ) ) ? $_POST['tName'] : $mobile_list['name']; ?>" /></td>
				</tr>
                <tr>
					<td><label for="tFrequency"><?php echo _('Est. Messages/Month'); ?>:</label></td>
					<td><input type="text" class="tb" name="tFrequency" id="tFrequency" maxlength="80" value="<?php echo ( !$success && isset( $_POST['tFrequency'] ) ) ? $_POST['tFrequency'] : $mobile_list['frequency']; ?>" /></td>
				</tr>
                <tr>
					<td><label for="tDescription"><?php echo _('Description'); ?>:</label></td>
					<td><input type="text" class="tb" name="tDescription" id="tDescription" maxlength="80" value="<?php echo ( !$success && isset( $_POST['tDescription'] ) ) ? $_POST['tDescription'] : $mobile_list['description']; ?>" /></td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="button" value="<?php echo ( $mobile_list_id ) ? _('Update Mobile List') : _('Add Mobile List'); ?>" /></td>
				</tr>
			</table>
			<?php nonce::field('add-edit-mobile-list'); ?>
		</form>
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>