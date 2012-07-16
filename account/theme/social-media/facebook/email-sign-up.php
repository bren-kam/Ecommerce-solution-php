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

// Secure the section
if ( !$user['website']['social_media'] )
    url::redirect('/');

// Make Sure they chose a facebook page
if ( !isset( $_SESSION['sm_facebook_page_id'] ) )
    url::redirect('/social-media/facebook/');

// Make sure they have access to this page
$sm = new Social_Media;
$w = new Websites;
$social_media_add_ons = @unserialize( $w->get_setting( 'social-media-add-ons' ) );
$facebook_page = $sm->get_facebook_page( $_SESSION['sm_facebook_page_id'] );

if ( !$facebook_page || !is_array( $social_media_add_ons ) || !in_array( 'email-sign-up', $social_media_add_ons ) )
    url::redirect('/social-media/facebook/');

// Instantiate Classes
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
		$success = $sm->update_email_sign_up( $_POST['taEmailSignUp'], $_POST['sEmailList'] );
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
	<h1><?php echo _('Email Sign Up'), ' - ', $facebook_page['name']; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'social-media/' ); ?>
	<div id="subcontent">
		<?php
        if ( !isset( $email_sign_up['fb_page_id'] ) || 0 == $email_sign_up['fb_page_id'] ) {
            // Define instructions
            $instructions = array(
                1 => array(
                    'title' => _('Go to the Email Sign Up application')
                    , 'text' => _('Go to the') . ' <a href="http://apps.facebook.com/email-sign-up/" title="' . _('Online Platform - Email Sign Up') . '" target="_blank">' . _('Email Sign Up') . '</a> ' . _('application page') . '.'
                    , 'image' => false
                )
                , 2 => array(
                    'title' => _('Install The App')
                    , 'text' => _('Click') . ' <strong>' . _('Install This App.') . '</strong> ' . _('on the page shown below:')
                )
                , 3 => array(
                    'title' => _('Choose Your Page')
                    , 'text' => _('(Note - You must first be an admin of the page to install the App)')
                )
                , 4 => array(
                    'title' => _('Click Add Online Platform - Email Sign Up')
                )
                , 5 => array(
                    'title' => _('Click on the Email Sign Up App')
                    , 'text' => _("Scroll down below the banner, and you'll see your apps (you may need to click on the arrow on the right-hand side to find the app you're looking for) and click on the About Us")
                )
                , 6 => array(
                    'title' => _('Click on the Update Settings')
                )
                , 7 => array(
                    'title' => _('Click Add Online Platform - Email Sign Up')
                    , 'text' => _('Copy and paste the connection code below into the Facebook Connection Key box shown below (when done it will say Connected)')
                )
            );

            foreach ( $instructions as $step => $data ) {
                echo '<h2 class="title">', _('Step'), " $step:", $data['title'], '</h2>';

                if ( isset( $data['text'] ) )
                    echo '<p>', $data['text'], '</p>';

                if ( !isset( $data['image'] ) || $data['image'] != false )
                    echo '<br /><p><a href="http://account.imagineretailer.com/images/social-media/facebook/email-sign-up/', $step, '.jpg"><img src="http://admin.imagineretailer.com/images/social-media/facebook/about-us/', $step, '.jpg" alt="', $data['title'], '" width="750" /></a></p>';

                echo '<br /><br />';
            }
         } else {
            ?>
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
                            $file_name = f::name( $wf['file_path'] );
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