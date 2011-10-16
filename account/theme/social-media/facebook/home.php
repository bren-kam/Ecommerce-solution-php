<?php
/**
 * @page Social Media - Facebook - Home
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if( !$user )
	login();

// Instantiate Classes
$sm = new Social_Media;
	$wf = new Website_Files;

if( nonce::verify( $_POST['_nonce'], 'home' ) )
	$success = $sm->update_home( $_POST['taContent'] );

// Get variables 
$home = $sm->get_home();
$website_files = $wf->get_all();
	
if( !$home ) {
	$home['key'] = $sm->create_home();
	$home['content'] = '';
}

css( 'jquery.uploadify' );
javascript( 'mammoth', 'swfobject', 'jquery.uploadify', 'website/page' );

$selected = "social_media";
$title = _('Home') . ' | ' . _('Facebook') . ' | ' . _('Social Media') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Home'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'social-media/' ); ?>
	<div id="subcontent">
		<?php if( $success ) { ?>
		<p class="success"><?php echo _('Your home page has been successfully updated!'); ?></p>
		<?php } ?>
		
		<p><strong><?php echo _('Facebook Connection Key:'); ?></strong> <?php echo $home['key']; ?> <strong><?php echo ( 0 == $home['fb_page_id'] ) ? '<span class="error">(' . _('Not Connected') . ')</span>' : '<span class="success">(' . _('Connected') . ')</span>'; ?></strong></p>
		<br />
		
		<form name="fHome" action="/social-media/facebook/home/" method="post">
			<h2 class="title"><label for="taContent"><?php echo _('Home Page'); ?>:</label></h2>
			<textarea name="taContent" id="taContent" cols="50" rows="3" rte="1"><?php echo $home['content']; ?></textarea>
			
			<p><a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a> | (<?php echo _('Image Width: 520px'); ?>)</p>
			<br /><br />
			
			<input type="submit" class="button" value="<?php echo _('Save'); ?>" />
			<?php nonce::field('home'); ?>
		</form>
		
		<div id="dUploadFile" class="hidden">
			<ul id="ulUploadFile">
				<?php
				if( is_array( $website_files ) ) {
					// Set variables
					$ajax_delete_file_nonce = nonce::create('delete-file');
					$confirm = _('Are you sure you want to delete this file?');
					
					foreach( $website_files as $wf ) {
						$file_name = format::file_name( $wf['file_path'] );
						echo '<li id="li' . $wf['website_file_id'] . '"><a href="', $wf['file_path'], '" id="aFile', $wf['website_file_id'], '" class="file" title="', $file_name, '">', $file_name, '</a><a href="/ajax/website/page/delete-file/?_nonce=' . $ajax_delete_file_nonce . '&amp;wfid=' . $wf['website_file_id'] . '" class="float-right" title="' . _('Delete File') . '" ajax="1" confirm="' . $confirm . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete File') . '" /></a></li>';
					}
				} else {
					echo '<li>', _('You have not uploaded any files.') . '</li>';
				}
				?>
			</ul>
			<br />
			
			<input type="text" class="tb" id="tFileName" tmpval="<?php echo _('New File Name'); ?>" error="<?php echo _('You must type in a file name before uploading a file.'); ?>" style="position:relative; bottom: 11px;" /> 
			<input type="file" name="fUploadFile" id="fUploadFile" />
			<br /><br />
			<div id="dCurrentLink" class="hidden">
				<p><strong><?php echo _('Current Link'); ?>:</strong></p>
				<p><input type="text" class="tb" id="tCurrentLink" value="<?php echo _('No link selected'); ?>" style="width:100%;" /></p>
				<br />
			</div>
			<p align="right"><a href="javascript:;" class="button close" title="<?php echo _('Close'); ?>"><?php echo _('Close'); ?></a></p>
		</div>
		<?php nonce::field( 'upload-file', '_ajax_upload_file' ); ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>