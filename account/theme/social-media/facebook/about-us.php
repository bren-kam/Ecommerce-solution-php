<?php
/**
 * @page Social Media - Facebook - About Us
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

if ( !$facebook_page || !is_array( $social_media_add_ons ) || !in_array( 'about-us', $social_media_add_ons ) )
    url::redirect('/social-media/facebook/');

if ( $user['website']['pages'] ) {
	// We will need website files
	$wf = new Website_Files;

	$website_files = $wf->get_all();
} else if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'about-us' ) ) {
	$success = $sm->update_about_us( $_POST['taContent'] );
}

// Get variables 
$about_us = $sm->get_about_us();

if ( !$about_us ) {
	$about_us['key'] = $sm->create_about_us();
	$about_us['content'] = '';
}

css( 'jquery.uploadify' );
javascript( 'mammoth', 'swfobject', 'jquery.uploadify', 'website/page' );

$selected = "social_media";
$title = _('About Us') . ' | ' . _('Facebook') . ' | ' . _('Social Media') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('About Us'), ' - ', $facebook_page['name']; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'social-media/' ); ?>
	<div id="subcontent">
		<?php
        if ( !isset( $about_us['fb_page_id'] ) || 0 == $about_us['fb_page_id'] ) {
            // Define instructions
            $instructions = array(
                1 => array(
                    'title' => _('Go to the About Us application')
                    , 'text' => _('Go to the') . ' <a href="http://apps.facebook.com/op-about-us/" title="' . _('Online Platform - About Us') . '" target="_blank">' . _('About Us') . '</a> ' . _('application page') . '.'
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
                    'title' => _('Click Add Online Platform - About Us')
                )
                , 5 => array(
                    'title' => _('Click on the About Us App')
                    , 'text' => _("Scroll down below the banner, and you'll see your apps (you may need to click on the arrow on the right-hand side to find the app you're looking for) and click on the About Us")
                )
                , 6 => array(
                    'title' => _('Click on the Update Settings')
                )
                , 7 => array(
                    'title' => _('Click Add Online Platform - About Us')
                    , 'text' => _('Copy and paste the connection code below into the Facebook Connection Key box shown below (when done it will say Connected)')
                )
            );

            foreach ( $instructions as $step => $data ) {
                echo '<h2 class="title">', _('Step'), " $step:", $data['title'], '</h2>';

                if ( isset( $data['text'] ) )
                    echo '<p>', $data['text'], '</p>';

                if ( !isset( $data['image'] ) || $data['image'] != false )
                    echo '<br /><p><a href="http://account.imagineretailer.com/images/social-media/facebook/about-us/', $step, '.png"><img src="http://account.imagineretailer.com/images/social-media/facebook/about-us/', $step, '.png" alt="', $data['title'], '" width="750" /></a></p>';

                echo '<br /><br />';
            }
         } else {
            ?>
			<p align="right"><a href="http://www.facebook.com/pages/ABC-Company/<?php echo $about_us['fb_page_id']; ?>?sk=app_233746136649331" title="<?php echo _('View Facebook Page'); ?>" target="_blank"><?php echo _('View Facebook Page'); ?></a></p>
			<?php if ( $success ) { ?>
				<p class="success"><?php echo _('Your about us page has been successfully updated!'); ?></p>
				<?php 
			}
			
			if ( $user['website']['pages'] ) {
				echo '<p>', _('Your app is currently active.'), '</p>';
			} else {
			?>
			<form name="fAboutUs" action="/social-media/facebook/about-us/" method="post">
				<h2 class="title"><label for="taContent"><?php echo _('About Us Page'); ?>:</label></h2>
				<textarea name="taContent" id="taContent" cols="50" rows="3" rte="1"><?php echo $about_us['content']; ?></textarea>
				
				<p><a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a> | (<?php echo _('Image Width: 520px Image Height: 700px Max'); ?>)</p>
				<br /><br />
				
				<input type="submit" class="button" value="<?php echo _('Save'); ?>" />
				<?php nonce::field('about-us'); ?>
			</form>
			<?php } ?>
			
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