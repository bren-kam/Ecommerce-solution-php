<?php
/**
 * @page Add Edit Facebook Page
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Secure the section
if ( !$user['website']['social_media'] )
    url::redirect('/');

// Get the mobile subscriber id if there is one
$sm_facebook_page_id = ( isset( $_GET['smfbpid'] ) ) ? $_GET['smfbpid'] : false;

$w = new Websites;
$sm = new Social_Media();

$facebook_pages = (int) $w->get_setting( 'facebook-pages' );
$facebook_page_count = $sm->count_facebook_pages('');

$has_permission = $sm_facebook_page_id || $facebook_page_count < $facebook_pages || empty( $facebook_page_count );

$v = new Validator();
$v->form_name = 'fAddEditFacebookPage';
$v->add_validation( 'tName', 'req' , _('The "Name" field is required') );

// Add validation
add_footer( $v->js_validation() );

// Initialize variable
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-facebook-page' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) ) {
		if ( $sm_facebook_page_id ) {
			// Update subscriber
			$success = $sm->update_facebook_page( $sm_facebook_page_id, $_POST['tName'] );
		} else {
            $success = $sm->create_facebook_page( $_POST['tName'] );

            if ( !$success )
                $errs .= _('An error occurred while adding subscriber.') . '<br />';
		}
	}
}

// Get the Facebook Page if necessary
if ( $sm_facebook_page_id || $success ) {
    $sm_facebook_page_id = ( !$sm_facebook_page_id && $success ) ? $success : $sm_facebook_page_id;
	$facebook_page = $sm->get_facebook_page( $sm_facebook_page_id );
} else {
	// Initialize variable
	$facebook_page = array(
		'name' => ''
	);
}

$sub_title = ( $sm_facebook_page_id ) ? _('Edit Facebook Page') : _('Add Facebook Page');
$title = "$sub_title | " . _('Mobile Marketing') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'social-media/' ); ?>
	<div id="subcontent">
		<?php if ( !$has_permission ) { ?>
            <p><?php echo _('You have reached your maximum amount of facebook pages, please see your online specialist about getting more.'); ?></p>
        <?php
        } else {

            if ( $success ) { ?>
            <div class="success">
                <p><?php echo ( $sm_facebook_page_id ) ? _('Your facebook page has been updated successfully!') : _('Your facebook page has been added successfully!'); ?></p>
                <p><?php echo '<a href="/social-media/facebook/" title="', _('Facebook Pages'), '">', _('Click here to view your facebook pages'), '</a>.'; ?></p>
            </div>
            <?php
            }

            // Allow them to edit the entry they just created
            if ( $success && !$sm_facebook_page_id )
                $mobile_subscriber_id = $success;

            if ( isset( $errs ) )
                echo "<p class='red'>$errs</p>";
            ?>
            <form name="fAddEditFacebookPage" action="/social-media/facebook/add-edit/<?php if ( $sm_facebook_page_id ) echo "?smfbpid=$sm_facebook_page_id"; ?>" method="post">
                <?php nonce::field( 'add-edit-facebook-page' ); ?>
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td><label for="tName"><?php echo _('Name'); ?>:</label></td>
                        <td><input type="text" class="tb" name="tName" id="tName" maxlength="100" value="<?php echo ( !$success && isset( $_POST['tName'] ) ) ? $_POST['tName'] : $facebook_page['name']; ?>" /></td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><input type="submit" class="button" value="<?php echo ( $sm_facebook_page_id ) ? _('Save') : _('Add'); ?>" /></td>
                    </tr>
                </table>
            </form>
        <?php } ?>
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>