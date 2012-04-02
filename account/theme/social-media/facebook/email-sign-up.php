<?php
/**
 * @page Social Media - Facebook - Email Sign Up
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Make sure they have access to this page
$w = new Websites;
$social_media_add_ons = @unserialize( $w->get_setting( 'social-media-add-ons' ) );

if ( !is_array( $social_media_add_ons ) || !in_array( 'email-sign-up', $social_media_add_ons ) )
    url::redirect('/social-media/facebook/');

// Instantiate Classes
$sm = new Social_Media;
$e = new Email_Marketing;
$wf = new Website_Files;
$v = new Validator;

// Add validation
$v->form_name = 'fEmailSignUp';
$v->add_validation( 'sEmailList', 'val!=0', _('You must select an email list.') );

// Add Javascript
add_footer( $v->js_validation() );

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'email-sign-up' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) )
		$success = $sm->update_email_sign_up( stripslashes( $_POST['taEmailSignUp'] ), $_POST['sEmailList'] );
}

// Get variables
$email_sign_up = $sm->get_email_sign_up();
$email_lists = $e->get_email_lists();
$website_files = $wf->get_all();

if ( !$email_sign_up ) {
	$email_sign_up['key'] = $sm->create_email_sign_up();
	$email_sign_up['tab'] = $email_sign_up['email_list_id'] = '';
}

css( 'jquery.uploadify' );
javascript( 'mammoth', 'swfobject', 'jquery.uploadify', 'website/page' );

$selected = "social_media";
$title = _('Email Sign Up') . ' | ' . _('Facebook') . ' | ' . _('Social Media') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Email Sign Up'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'social-media/' ); ?>
	<div id="subcontent">
		<?php if ( 0 == $email_sign_up['fb_page_id'] ) { ?>
			<h2 class="title"><?php echo _('Step 1: Go to the Email Sign Up application.'); ?></h2>
			<p><?php echo _('Go to the'); ?> <a href="http://www.facebook.com/apps/application.php?id=165553963512320" title="<?php echo _('Online Platform - Email Sign Up'); ?>" target="_blank"><?php echo _('Email Sign Up'); ?></a> <?php echo _('application page'); ?>.</p>
			<br /><br />
			
			<h2 class="title"><?php echo _('Step 2: Install on your Fan Page'); ?></h2>
			<p><?php echo _('Click'); ?> <strong><?php echo _('Add to my Page (bottom left of your page).'); ?></strong></p>
			<p><strong><?php echo _('NOTE'); ?>:</strong> <?php echo _("If you do not see this link, it means you either don't have administrative access to any fan pages, or you already have this application installed. (If it is already installed, please ahead skip to Step 4.)"); ?></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/email-sign-up/step2.jpg" class="image-border" width="750" height="434" alt="<?php echo _('Step 2'); ?>" /></p>
			<br /><br />
			
			<h2 class="title"><?php echo _('Step 3: Click on the Add to Page Button.'); ?></h2>
			<p><?php echo _('Choose the Facebook Fan Page you want to add your app to by clicking on the'); ?> <strong><?php echo _('Add to Page'); ?></strong> <?php echo _('button to the right of the Fan Page name.'); ?></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/email-sign-up/step3.jpg" class="image-border" width="750" height="236" alt="<?php echo _('Step 3'); ?>" /></p>
			<br /><br />
			
			<h2 class="title"><?php echo _('Step 4: Click on the App.'); ?></h2>
			<p><?php echo _('Go to your Fan Page and click on the App you are installing from the list on the left.'); ?></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/email-sign-up/step4.jpg" class="image-border" width="750" height="559" alt="<?php echo _('Step 4 - 1'); ?>" /></p>
			<br />
			<p><?php echo _('Click on'); ?> <strong><?php echo _('Update Settings'); ?></strong> <?php echo _('right under the app name. Note: This is only visible to you because you are the admin for this page.'); ?></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/email-sign-up/step4-1.jpg" class="image-border" width="905" height="131" alt="<?php echo _('Step 4 - 2'); ?>" /></p>
			<br /><br />
			
			<h2 class="title"><?php echo _('Step 5: Connect the application with your dashboard account'); ?></h2>
			<p><?php echo _('Copy the connection key listed below and paste into the Facebook app.'); ?></p>
			<p><?php echo _('Facebook Connection Key'); ?>: <?php echo $email_sign_up['key']; ?></p>
			<p><strong><?php echo _('NOTE'); ?></strong>: <?php echo _('You may see a request for permissions. If this is the case, you first need to Allow Permissions to the application before you will be able to move on.'); ?></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/email-sign-up/step5.jpg" class="image-border" width="773" height="189" alt="<?php echo _('Step 5'); ?>" /></p>
			<br />
			<p><?php echo _('When you click Connect, you will see'); ?> <span class="error"><?php echo _('(Not Connected)'); ?></span> <?php echo _('in red change to'); ?> <span class="success"><?php echo _('(Connected)'); ?></span> <?php echo _('in green.'); ?></p>
			<br /><br />
			
			<h2 class="title"><?php echo _('Step 6: Final App Activation.'); ?></h2>
			<p><?php echo _('Click the activate link to complete the installation process. You will then be able to control all the content for the app from this dashboard.'); ?></p>
			<p><a href="/social-media/facebook/email-sign-up/" title="<?php echo _('Activate'); ?>"><?php echo _('Activate'); ?></a></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/email-sign-up/step6.jpg" class="image-border" width="492" height="192" alt="<?php echo _('Step 6'); ?>" /></p>
			<br /><br />
		<?php } else { ?>
			<p align="right"><a href="http://www.facebook.com/pages/ABC-Company/<?php echo $email_sign_up['fb_page_id']; ?>?sk=app_165553963512320" title="<?php echo _('View Facebook Page'); ?>" target="_blank"><?php echo _('View Facebook Page'); ?></a></p>
			<form name="fEmailSignUp" action="/social-media/facebook/email-sign-up/" method="post">
                <?php if ( $success ) { ?>
                <p class="success"><?php echo _('Your email sign up page has been successfully updated!'); ?></p>
                <?php
                }

                if ( isset( $errs ) )
                    echo "<p class='error'>$errs</p>";
                ?>
                <textarea name="taEmailSignUp" cols="50" rows="3" rte="1"><?php echo $email_sign_up['tab']; ?></textarea>

                <p><a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a> | (<?php echo _('Image Width: 810px Image Height: 700px Max'); ?>)</p>
                <br />
                <p>
                    <label for="sEmailList"><?php echo _('Email List'); ?>:</label>
                    <select name="sEmailList" id="sEmailList">
                        <option value="">-- <?php echo _('Select Email List'); ?> --</option>
                        <?php
                        foreach ( $email_lists as $el ) {
                            $selected = ( $el['email_list_id'] == $email_sign_up['email_list_id'] ) ? ' selected="selected"' : '';
                            ?>
                        <option value="<?php echo $el['email_list_id']; ?>"<?php echo $selected; ?>><?php echo $el['name']; ?></option>
                        <?php } ?>
                    </select>
                    <a href="/email-marketing/email-lists/add-edit" title="<?php echo _('Add New Email List'); ?>" target="_blank"><?php echo _('Add New Email List'); ?></a>
                </p>

                <input type="submit" class="button" value="<?php echo _('Save'); ?>" />
                <?php nonce::field('email-sign-up'); ?>
            </form>

            <div id="dUploadFile" class="hidden">
                <ul id="ulUploadFile">
                    <?php
                    if ( is_array( $website_files ) ) {
                        // Set variables
                        $ajax_delete_file_nonce = nonce::create('delete-file');
                        $confirm = _('Are you sure you want to delete this file?');

                        foreach ( $website_files as $wf ) {
                            $file_name = format::file_name( $wf['file_path'] );
                            echo '<li id="li' . $wf['website_file_id'] . '"><a href="', $wf['file_path'], '" id="aFile', $wf['website_file_id'], '" class="file" title="', $file_name, '">', $file_name, '</a><a href="/ajax/website/page/delete-file/?_nonce=' . $ajax_delete_file_nonce . '&amp;wfid=' . $wf['website_file_id'] . '" class="float-right" title="' . _('Delete File') . '" ajax="1" confirm="' . $confirm . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete File') . '" /></a></li>';
                        }
                    } else {
                        echo '<li>', _('You have not uploaded any files.') . '</li>';
                    }
                    ?>
                </ul>
                <br />

                <input type="text" class="tb" id="tFileName" tmpval="<?php echo _('Enter File Name'); ?>..." error="<?php echo _('You must type in a file name before uploading a file.'); ?>" style="position:relative; bottom: 11px;" />
                <input type="file" name="fUploadFile" id="fUploadFile" />
                <br /><br />
                <div id="dCurrentLink" class="hidden">
                    <p><strong><?php echo _('Current Link'); ?>:</strong></p>
                    <p><input type="text" class="tb" id="tCurrentLink" value="<?php echo _('No link selected'); ?>" style="width:100%;" /></p>
                </div>
            </div>
            <?php nonce::field( 'upload-file', '_ajax_upload_file' ); ?>
            <input type="hidden" id="hWebsiteID" value="<?php echo $user['website']['website_id']; ?>" />
		<?php } ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>