<?php
/**
 * @page Social Media - Facebook - Facebook Site
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


if ( !$facebook_page || !is_array( $social_media_add_ons ) || !in_array( 'facebook-site', $social_media_add_ons ) )
    url::redirect('/social-media/facebook/');

// Instantiate Classes
$wf = new Website_Files;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'facebook-site' ) )
	$success = $sm->update_facebook_site( $_POST['taContent'] );

// Get variables 
$facebook_site = $sm->get_facebook_site();
$website_files = $wf->get_all();
	
if ( !$facebook_site ) {
	$facebook_site['key'] = $sm->create_facebook_site();
	$facebook_site['content'] = '';
}

css( 'jquery.uploadify' );
javascript( 'mammoth', 'swfobject', 'jquery.uploadify', 'website/page' );

$selected = "social_media";
$title = _('Facebook Site') . ' | ' . _('Facebook') . ' | ' . _('Social Media') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Facebook Site'), ' - ', $facebook_page['name']; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'social-media/' ); ?>
	<div id="subcontent">
		<?php if ( 0 == $facebook_site['fb_page_id'] ) { ?>
			<h2 class="title"><?php echo _('Step 1: Go to the Facebook Site application.'); ?></h2>
			<p><?php echo _('Go to the'); ?> <a href="http://apps.facebook.com/op-facebook-site/" title="<?php echo _('Online Platform - Facebook Site'); ?>" target="_blank"><?php echo _('Facebook Site'); ?></a> <?php echo _('application page'); ?>.</p>
			<br /><br />
			
			<h2 class="title"><?php echo _('Step 2: Install on your Fan Page'); ?></h2>
			<p><?php echo _('Click'); ?> <strong><?php echo _('Add to my Page (bottom left of your page).'); ?></strong></p>
			<p><strong><?php echo _('NOTE'); ?>:</strong> <?php echo _("If you do not see this link, it means you either don't have administrative access to any fan pages, or you already have this application installed. (If it is already installed, please ahead skip to Step 4.)"); ?></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/facebook-site/step2.jpg" class="image-border" width="750" height="528" alt="<?php echo _('Step 2'); ?>" /></p>
			<br /><br />
			
			<h2 class="title"><?php echo _('Step 3: Click on the Add to Page Button.'); ?></h2>
			<p><?php echo _('Choose the Facebook Fan Page you want to add your app to by clicking on the'); ?> <strong><?php echo _('Add to Page'); ?></strong> <?php echo _('button to the right of the Fan Page name.'); ?></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/facebook-site/step3.jpg" class="image-border" width="750" height="239" alt="<?php echo _('Step 3'); ?>" /></p>
			<br /><br />
			
			<h2 class="title"><?php echo _('Step 4: Click on the App.'); ?></h2>
			<p><?php echo _('Go to your Fan Page and click on the App you are installing from the list on the left.'); ?></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/facebook-site/step4.jpg" class="image-border" width="750" height="650" alt="<?php echo _('Step 4 - 1'); ?>" /></p>
			<br />
			<p><?php echo _('Click on'); ?> <strong><?php echo _('Update Settings'); ?></strong> <?php echo _('right under the app name. Note: This is only visible to you because you are the admin for this page.'); ?></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/facebook-site/step4-1.jpg" class="image-border" width="678" height="198" alt="<?php echo _('Step 4 - 2'); ?>" /></p>
			<br /><br />
			
			<h2 class="title"><?php echo _('Step 5: Connect the application with your dashboard account'); ?></h2>
			<p><?php echo _('Copy the connection key listed below and paste into the Facebook app.'); ?></p>
			<p><?php echo _('Facebook Connection Key'); ?>: <?php echo $facebook_site['key']; ?></p>
			<p><strong><?php echo _('NOTE'); ?></strong>: <?php echo _('You may see a request for permissions. If this is the case, you first need to Allow Permissions to the application before you will be able to move on.'); ?></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/facebook-site/step5.jpg" class="image-border" width="750" height="170" alt="<?php echo _('Step 5'); ?>" /></p>
			<br />
			<p><?php echo _('When you click Connect, you will see'); ?> <span class="error"><?php echo _('(Not Connected)'); ?></span> <?php echo _('in red change to'); ?> <span class="success"><?php echo _('(Connected)'); ?></span> <?php echo _('in green.'); ?></p>
			<br /><br />
			
			<h2 class="title"><?php echo _('Step 6: Final App Activation.'); ?></h2>
			<p><?php echo _('Click the activate link to complete the installation process. You will then be able to control all the content for the app from this dashboard.'); ?></p>
			<p><a href="/social-media/facebook/facebook-site/" title="<?php echo _('Activate'); ?>"><?php echo _('Activate'); ?></a></p>
			<br />
			<p><img src="http://account.imagineretailer.com/images/social-media/facebook/facebook-site/step6.jpg" class="image-border" width="502" height="188" alt="<?php echo _('Step 6'); ?>" /></p>
			<br /><br />
		<?php } else { ?>
			<p align="right"><a href="http://www.facebook.com/pages/ABC-Company/<?php echo $facebook_site['fb_page_id']; ?>?sk=app_114243368669744" title="<?php echo _('View Facebook Page'); ?>" target="_blank"><?php echo _('View Facebook Page'); ?></a></p>
			<?php if( $success ) { ?>
			<p class="success"><?php echo _('Your facebook site has been successfully updated!'); ?></p>
			<?php } ?>
			
			<form name="fFacebook Site" action="/social-media/facebook/facebook-site/" method="post">
				<h2 class="title"><label for="taContent"><?php echo _('Facebook Site Page'); ?>:</label></h2>
				<textarea name="taContent" id="taContent" cols="50" rows="3" rte="1"><?php echo $facebook_site['content']; ?></textarea>
				
				<p><a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a> | (<?php echo _('Image Width: 810px Image Height: 700px Max'); ?>)</p>
				<br /><br />
				
				<input type="submit" class="button" value="<?php echo _('Save'); ?>" />
				<?php nonce::field('facebook-site'); ?>
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