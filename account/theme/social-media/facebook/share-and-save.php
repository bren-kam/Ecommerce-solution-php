<?php
/**
 * @page Social Media - Facebook - Share and Save
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

if ( !$facebook_page || !is_array( $social_media_add_ons ) || !in_array( 'share-and-save', $social_media_add_ons ) )
    url::redirect('/social-media/facebook/');

// Instantiate Classes
$e = new Email_Marketing;
$wf = new Website_Files;
$v = new Validator;

// Add validation
$v->form_name = 'fShareAndSave';
$v->add_validation( 'sEmailList', 'val!=0', _('You must select an email list.') );

// Add Javascript
add_footer( $v->js_validation() );

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'share-and-save' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) )
		$success = $sm->update_share_and_save( $_POST['sEmailList'], $_POST['sMaximumEmailList'], $_POST['taBefore'], $_POST['taAfter'], $_POST['tMinimum'], $_POST['tMaximum'], $_POST['tShareTitle'], $_POST['tShareImageURL'], $_POST['taShareText'] );
}

// Get variables
$share_and_save = $sm->get_share_and_save();
$email_lists = $e->get_email_lists();
$website_files = $wf->get_all();

if ( !$share_and_save ) {
	$share_and_save = array(
		'key' => $sm->create_share_and_save()
		, 'email_list_id' => ''
		, 'maximum_email_list_id' => ''
		, 'before' => ''
		, 'after' => ''
		, 'minimum' => ''
		, 'maximum' => ''
	);
}

css( 'jquery.uploadify' );
javascript( 'mammoth', 'swfobject', 'jquery.uploadify', 'website/page' );

$selected = "social_media";
$title = _('Share and Save') . ' | ' . _('Facebook') . ' | ' . _('Social Media') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Share and Save'), ' - ', $facebook_page['name']; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'social-media/' ); ?>
	<div id="subcontent">
		<?php
        if ( !isset( $share_and_save['fb_page_id'] ) || 0 == $share_and_save['fb_page_id'] ) {
            // Define instructions
            $instructions = array(
                1 => array(
                    'title' => _('Go to the Share and Save application')
                    , 'text' => _('Go to the') . ' <a href="http://apps.facebook.com/share-and-save/" title="' . _('Online Platform - Share and Save') . '" target="_blank">' . _('Share and Save') . '</a> ' . _('application page') . '.'
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
                    'title' => _('Click Add Online Platform - Share and Save')
                )
                , 5 => array(
                    'title' => _('Click on the Share and Save App')
                    , 'text' => _("Scroll down below the banner, and you'll see your apps (you may need to click on the arrow on the right-hand side to find the app you're looking for) and click on the Share and Save")
                )
                , 6 => array(
                    'title' => _('Click on the Update Settings')
                )
                , 7 => array(
                    'title' => _('Click Add Online Platform - Share and Save')
                    , 'text' => _('Copy and paste the connection code into the Facebook Connection Key box shown below (when done it will say Connected): ') . $share_and_save['key']
                )
            );

            foreach ( $instructions as $step => $data ) {
                echo '<h2 class="title">', _('Step'), " $step:", $data['title'], '</h2>';

                if ( isset( $data['text'] ) )
                    echo '<p>', $data['text'], '</p>';

                if ( !isset( $data['image'] ) || $data['image'] != false )
                    echo '<br /><p><a href="http://account.imagineretailer.com/images/social-media/facebook/share-and-save/', $step, '.png"><img src="http://account.imagineretailer.com/images/social-media/facebook/share-and-save/', $step, '.png" alt="', $data['title'], '" width="750" /></a></p>';

                echo '<br /><br />';
            }
         } else {
            ?>
			<p align="right"><a href="http://www.facebook.com/pages/ABC-Company/<?php echo $share_and_save['fb_page_id']; ?>?sk=app_118945651530886" title="<?php echo _('View Facebook Page'); ?>" target="_blank"><?php echo _('View Facebook Page'); ?></a></p>
			<form name="fShareAndSave" action="/social-media/facebook/share-and-save/" method="post">
				<?php if ( $success ) { ?>
				<p class="success"><?php echo _('Your email share and save page has been successfully updated!'); ?></p>
				<?php 
				}
				
				if ( isset( $errs ) )
					echo "<p class='error'>$errs</p>";
				?>
				
				<h2 class="title"><label for="taBefore"><?php echo _('What Non-Fans See'); ?>:</label></h2>
				<textarea name="taBefore" id="taBefore" cols="50" rows="3" rte="1"><?php echo $share_and_save['before']; ?></textarea>
				<p><a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a> | (<?php echo _('Image Width: 810px Image Height: 700px Max'); ?>)</p>
				<br />
				
				<h2 class="title"><label for="taAfter"><?php echo _('What Fans See After Liking the Page'); ?>:</label></h2>
				<textarea name="taAfter" id="taAfter" cols="50" rows="3" rte="1"><?php echo $share_and_save['after']; ?></textarea>
				<p><a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a> | (<?php echo _('Image Width: 810px Image Height: 700px Max'); ?>)</p>
				<br />
				
				<h2 class="title"><label for="tMinimum"><?php echo _('Minimum Subscribers'); ?>:</label></h2>
				<p><input type="text" class="tb" name="tMinimum" id="tMinimum" value="<?php echo $share_and_save['minimum']; ?>" maxlength="5" style="width: 50px" /></p>
				<br />
				
				<h2 class="title"><label for="sEmailList"><?php echo _('Email List'); ?>:</label></h2>
				<p>
					<select name="sEmailList" id="sEmailList">
						<option value="">-- <?php echo _('Select Email List'); ?> --</option>
						<?php 
						foreach ( $email_lists as $el ) {
							$selected = ( $el['email_list_id'] == $share_and_save['email_list_id'] ) ? ' selected="selected"' : '';
							?>
						<option value="<?php echo $el['email_list_id']; ?>"<?php echo $selected; ?>><?php echo $el['name']; ?></option>
						<?php } ?>
					</select>
					<a href="/email-marketing/email-lists/add-edit" title="<?php echo _('Add New Email List'); ?>" target="_blank"><?php echo _('Add New Email List'); ?></a>
				</p>
				<br />
				
				<h2 class="title"><label for="tMaximum"><?php echo _('Maximum Subscribers'); ?>:</label></h2>
				<p><input type="text" class="tb" name="tMaximum" id="tMaximum" value="<?php echo $share_and_save['maximum']; ?>" maxlength="5" style="width: 50px" /></p>
				<br />
	
				<h2 class="title"><label for="sMaximumEmailList"><?php echo _('Maximum Subscribers Email List'); ?>:</label></h2>
				<p>
					<select name="sMaximumEmailList" id="sMaximumEmailList">
						<option value="">-- <?php echo _('Select Email List'); ?> --</option>
						<?php 
						foreach ( $email_lists as $el ) {
							$selected = ( $el['email_list_id'] == $share_and_save['maximum_email_list_id'] ) ? ' selected="selected"' : '';
							?>
						<option value="<?php echo $el['email_list_id']; ?>"<?php echo $selected; ?>><?php echo $el['name']; ?></option>
						<?php } ?>
					</select>
					<a href="/email-marketing/email-lists/add-edit" title="<?php echo _('Add New Email List'); ?>" target="_blank"><?php echo _('Add New Email List'); ?></a>
				</p>
				<br />
				
				<h1 class="float-none padding-bottom"><?php echo _('Share Settings'); ?></h1>
				<table cellpadding="0" cellspacing="0" class="form">
					<tr>
						<td><label for="tShareTitle"><?php echo _('Share Title'); ?>:</label></td>
						<td><input type="text" class="tb" name="tShareTitle" id="tShareTitle" value="<?php echo $share_and_save['share_title']; ?>" maxlength="100" /></td>
					</tr>
					<tr>
						<td><label for="tShareImageURL"><?php echo _('Share Image Link'); ?>:</label></td>
						<td>
							<input type="text" class="tb" name="tShareImageURL" id="tShareImageURL" value="<?php echo $share_and_save['share_image_url']; ?>" maxlength="200" /> <a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload'); ?></a>
							<br />
							(<?php echo _('image must be 64x64'); ?>)
						</td>
					</tr>
					<tr>
						<td class="top"><label for="taShareText"><?php echo _('Share Text'); ?>:</label></td>
						<td><textarea name="taShareText" id="taShareText" cols="50" rows="3"><?php echo $share_and_save['share_text']; ?></textarea></td>
					</tr>
				</table>
				
				<input type="submit" class="button" value="<?php echo _('Save'); ?>" />
				<?php nonce::field('share-and-save'); ?>
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