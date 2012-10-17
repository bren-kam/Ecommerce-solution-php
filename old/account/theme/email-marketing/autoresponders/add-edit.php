<?php
/**
 * @page Add Edit Email Autoresponders
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$e = new Email_Marketing;

// Get the email autoresponder id if there is one
$email_autoresponder_id = ( isset( $_GET['eaid'] ) ) ? $_GET['eaid'] : false;

// Redirect to main section if they don't have email marketing -- they have to be editing the default autoresponder
if ( !$user['website']['email_marketing'] && !$email_autoresponder_id )
	url::redirect('/email-marketing/autoresponders/');

$v = new Validator();
$v->form_name = 'fAddEditAutoresponder';
$v->add_validation( 'tName', 'req' , 'The "Name" field is required' );
$v->add_validation( 'tSubject', 'req' , 'The "Subject" field is required' );
$v->add_validation( 'taMessage', 'req', 'The "Message" field is required' );

// Add validation
add_footer( $v->js_validation() );

// Initialize variable
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-autoresponder' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) ) {
		if ( $email_autoresponder_id ) {
			// Update email list
			$success = $e->update_autoresponder( $email_autoresponder_id, $_POST['tName'], $_POST['tSubject'], $_POST['taMessage'], $_POST['cbCurrentOffer'], $_POST['rbEmailListID'] );
		} else {
			// Create email list
			$success = $e->create_autoresponder( $_POST['tName'], $_POST['tSubject'], $_POST['taMessage'], $_POST['cbCurrentOffer'], $_POST['rbEmailListID'] );
		}
	}
}

// Get the email list if necessary
if ( $email_autoresponder_id ) {
	$autoresponder = $e->get_autoresponder( $email_autoresponder_id );

    if ( !$user['website']['email_marketing'] && 1 != $autoresponder['default'] )
    	url::redirect('/email-marketing/autoresponders/');
} else {
	$autoresponder = array(
		'default' => ''
		, 'name' => ''
		, 'subject' => ''
		, 'email_list_id' => ''
		, 'message' => ''
		, 'current_offer' => ''
	);
}

$email_lists = $e->get_autoresponder_email_lists( !empty( $autoresponder['email_list_id'] ) ? $autoresponder['email_list_id'] : 0 );

javascript( 'mammoth', 'email-marketing/autoresponders/add-edit' );

$selected = "email_marketing";
$sub_title = ( $email_autoresponder_id ) ? _('Edit Autoresponder') : _('Add Autoresponder');
$title = "$sub_title | " . _('Autoresponders') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'email-marketing/', 'autoresponders', 'add_edit_autoresponders' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo ( $email_autoresponder_id ) ? _('Your autoresponder has been updated successfully!') : _('Your autoresponder has been added successfully!'); ?></p>
			<p><?php echo _('Click here to'), ' <a href="/email-marketing/autoresponders/" title="', _('Autoresponders'), '">', _('view your autoresponders'), '</a>.'; ?></p>
		</div>
		<?php 
		}
		
		// Allow them to edit the entry they just created
		if ( $success && !$email_autoresponder_id )
			$email_autoresponder_id = $success;

		if ( isset( $errs ) )
			echo "<p class='red'>$errs</p>";

        if ( 0 == count( $email_lists ) ) {
		?>
            <p><?php echo _('Before adding an Autoresponder, you must have an Email List. These lists are automatically created when someone subscribes to one of those lists. In the meantime, you can update your default autoresponder.'); ?></p>
        <?php } else { ?>
            <form name="fAddEditAutoresponder" action="/email-marketing/autoresponders/add-edit/?eaid=<?php echo $email_autoresponder_id; ?>" method="post">
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
                        <td><label for="tSubject"><?php echo _('Subject'); ?>:</label></td>
                        <td><input type="text" class="tb" name="tSubject" id="tSubject" maxlength="80" value="<?php echo ( !$success && isset( $_POST['tSubject'] ) ) ? $_POST['tSubject'] : $autoresponder['subject']; ?>" /></td>
                    </tr>
                    <tr>
                        <td class="top"><label><?php echo _('Email List'); ?>:</label></td>
                        <td class="top">
                            <?php
                            if ( 1 == $autoresponder['default'] ) {
                                echo _('Default'), '<input type="hidden" name="rbEmailListID" value="', $autoresponder['email_list_id'], '" />';;
                            } else {
                                $i = 0;
                                $email_list_id = ( !$success && isset( $_POST['rbEmailListID'] ) ) ? $_POST['rbEmailListID'] : $autoresponder['email_list_id'];

                                foreach ( $email_lists as $el ) {
                                    if ( 0 != $i )
                                        echo '<br />';

                                    $checked = ( empty( $email_list_id ) && 0 == $i || $email_list_id == $el['email_list_id'] ) ? ' checked="checked"' : '';
                                    $i++;
                                ?>
                                <input type="radio" class="rb" name="rbEmailListID" id="rEmailList<?php echo $el['email_list_id']; ?>" value="<?php echo $el['email_list_id']; ?>"<?php echo $checked; ?> /> <label for="rEmailList<?php echo $el['email_list_id']; ?>"><?php echo $el['name']; ?></label>
                            <?php
                                }
                            }
                            ?>
                        </td>
                    </tr>
                </table>
                <br />
                <textarea name="taMessage" id="taMessage" cols="50" rows="5" rte="1"><?php echo ( !$success && isset( $_POST['taMessage'] ) ) ? $_POST['taMessage'] : $autoresponder['message']; ?></textarea>
                <br />
                <p><input type="checkbox" class="cb" name="cbCurrentOffer" id="cbCurrentOffer" value="1"<?php if ( !$success && isset( $_POST['cbCurrentOffer'] ) && '1' == $_POST['cbCurrentOffer'] || $autoresponder['current_offer'] ) echo ' checked="checked"'; ?> /> <label for="cbCurrentOffer"><?php echo _('Include Current Offer'); ?></label></p>

                <p style="padding-bottom:0"><a href="javascript:;" id="aSendTest" title="<?php echo _('Send Test'); ?>"><?php echo _('Send Test'); ?> [ + ]</a></p>
                <div id="dSendTest" class="hidden">
                    <p id="pSuccessMessage" class="success hidden"><?php echo _('A test email has been sent to the email address provided below.'); ?></p>
                    <?php nonce::field( 'test-autoresponder', '_ajax_test_autoresponder' ); ?>
                    <input type="text" class="tb" id="tTestEmail" maxlength="200" tmpval="<?php echo _('Test email...'); ?>" /> <input type="button" id="bSendTest" class="button" value="<?php echo _('Send Test'); ?>" />
                    <br />
                </div>
                <br />
                <p><input type="submit" class="button" value="<?php echo ( $email_autoresponder_id ) ? _('Update Autresponder') : _('Add Autoresponder'); ?>" /></p>
                <?php nonce::field('add-edit-autoresponder'); ?>
            </form>
            <?php } ?>
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>