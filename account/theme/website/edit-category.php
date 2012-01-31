<?php
/**
 * @page Edit Website Category
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$category_id = (int) $_GET['cid'];

// Send to website listing category
if ( empty( $category_id ) )
	url::redirect('/website/categories/');

// Instantiate classes
$c = new Categories;
$wf = new Website_Files;

// Get cateogry
$category = $c->get_website_category( $category_id );

// Get all the website files
$website_files = $wf->get_all();

// Initialize variable
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'edit-category' ) ) {
    // We don't want to submit that as it will override the default category
    if ( _('Category Title...') == $_POST['tTitle'] )
        $_POST['tTitle'] = '';

    // We don't want to submit that as it will override the default category
    if ( _('Category Title...') == $_POST['tTitle'] )
        $_POST['tTitle'] = '';
    // Update the category
    $success = $c->update_website_category( $category_id, stripslashes( $_POST['tTitle'] ), stripslashes( $_POST['tSlug'] ), stripslashes( $_POST['taContent'] ), stripslashes( $_POST['tMetaTitle'] ), stripslashes( $_POST['tMetaDescription'] ), stripslashes( $_POST['tMetaKeywords'] ), $_POST['rPosition'] );

    // Get new category
    $category = $c->get_website_category( $category_id );
}

$selected = "pages";
$title = _('Edit Category') . ' | ' . TITLE;
css( 'jquery.uploadify', 'website/page');
javascript( 'mammoth','swfobject', 'jquery.uploadify', 'website/category' );
get_header();
?>

<div id="content">
	<h1><?php echo _('Edit category'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'website/' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo _('Your category has been updated.'); ?> <a href="<?php echo $c->category_url( $category_id ); ?>" title="<?php echo _('View Category'); ?>" target="_blank"><?php echo _('View the category.'); ?></a></p>
			<p><a href="/website/categories/" title="<?php echo _('Edit Other Categories'); ?>"><?php echo _('Click here to edit other categories.'); ?></a></p>
		</div>
		<?php
		}
		
		if ( isset( $errs ) )
			echo "<p class='red'>$errs</p>";
		?>
		<form name="fEditCategory" action="/website/edit-category/?cid=<?php echo $category_id; ?>" method="post">
            <div id="dTitleContainer">
                <input name="tTitle" id="tTitle" class="tb" value="<?php echo $category['title']; ?>" tmpval="<?php echo _('Category Title...'); ?>" />
            </div>
            <?php /*
            <div id="dCategorySlug">
            	<span><strong><?php echo _('Link:'); ?></strong> http://<?php echo $user['website']['domain']; ?>/<span id="sCategorySlug"><?php echo $category['slug']; ?></span><input type="text" name="tCategorySlug" id="tCategorySlug" maxlength="50" class="tb hidden" value="<?php echo $category['slug']; ?>" />/</span>
                &nbsp;
                <a href="javascript:;" id="aCancelCategorySlug" title="Cancel" class="hidden"><?php echo _('Cancel'); ?></a>
                <a href="javascript:;" id="aEditCategorySlug" title="<?php echo _('Edit Link'); ?>"><?php echo _('Edit'); ?></a>&nbsp;
                <a href="javascript:;" id="aSaveCategorySlug" title="<?php echo _('Save Link'); ?>" class="button hidden round"><?php echo _('Save'); ?></a>
            </div>*/ ?>
            <br />
            <textarea name="taContent" id="taContent" cols="50" rows="3" rte="1"><?php echo $category['content']; ?></textarea>
            <p><a href="javascript:;" id="aMetaData" title="<?php echo _('Meta Data'); ?>"><?php echo _('Meta Data'); ?> [ + ]</a> | <a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a></p>
            <br />
            <div id="dMetaData" class="hidden">
                <p>
                    <label for="tMetaTitle"><?php echo _('Meta Title'); ?></label> <small>(<?php echo _('Recommended not to exceed 70 characters'); ?>)</small><br />
                    <input type="text" class="tb" name="tMetaTitle" id="tMetaTitle" value="<?php echo $category['meta_title']; ?>" />
                </p>
                <p>
                    <label for="tMetaDescription"><?php echo _('Meta Description'); ?></label> <small>(<?php echo _('Recommended not to exceed 250 characters'); ?>)</small><br />
                    <input type="text" class="tb"  name="tMetaDescription" id="tMetaDescription" value="<?php echo $category['meta_description']; ?>" />
                </p>
                <p>
                    <label for="tMetaKeywords"><?php echo _('Meta Keywords'); ?></label> <small>(<?php echo _('Recommended not to exceed 250 characters'); ?>)</small><br />
                    <input type="text" class="tb" name="tMetaKeywords" id="tMetaKeywords" value="<?php echo $category['meta_keywords']; ?>" />
                </p>
                <br />
            </div>
            <br />
            <table>
                <tr>
                    <td class="top"><label for="rPosition1"><?php echo _('Position'); ?>:</label></td>
                    <td>
                        <p><input type="radio" class="rb" name="rPosition" id="rPosition1" value="1"<?php if ( '0' != $category['top'] ) echo ' checked="checked"'; ?> /> <label for="rPosition1"><?php echo _('Top'); ?></label></p>
                        <p><input type="radio" class="rb" name="rPosition" id="rPosition2" value="0"<?php if ( '0' == $category['top'] ) echo ' checked="checked"'; ?> /> <label for="rPosition2"><?php echo _('Bottom'); ?></label></p>
                    </td>
                </tr>
            </table>
            <br /><br />
            <br /><br />
            <p><input type="submit" id="bSubmit" value="<?php echo _('Save'); ?>" class="button" /></p>
            <?php nonce::field( 'edit-category' ); ?>
		</form>
		<br />
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
					echo '<li class="no-files">', _('You have not uploaded any files.') . '</li>';
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
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>