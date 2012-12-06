<?php
/**
 * @package Grey Suit Retail
 * @page Edit Page
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var bool $success
 * @var AccountPage $page
 * @var string $page_title
 * @var array $files
 */

echo $template->start( _('Edit Page') );
?>

<?php if ( $success ) { ?>
<div class="success">
    <p><?php echo _('Your page has been updated.'); ?> <a href="http://<?php echo $user->account->domain, '/', $page->slu; ?>/" title="<?php echo _('View Page'); ?>" target="_blank"><?php echo _('View the page.'); ?></a></p>
    <p><a href="/website/" title="<?php echo _('Edit Other Pages'); ?>"><?php echo _('Click here to edit other pages.'); ?></a></p>
</div>
<?php
}

if ( isset( $errs ) )
    echo "<p class='red'>$errs</p>";
?>
<form name="fEditPage" action="<?php echo url::encode( 'apid', $page->id, '/website/edit/' ); ?>" method="post">
    <div id="title-container">
        <input name="tTitle" id="tTitle" class="tb" value="<?php echo $page_title; ?>" tmpval="<?php echo _('Page Title...'); ?>" />
    </div>
    <?php if ( 'home' != $page->slug ) { ?>
    <div id="dPageSlug">
        <span><strong><?php echo _('Link:'); ?></strong> http://<?php echo $user->account->domain; ?>/<input type="text" name="tPageSlug" id="tPageSlug" maxlength="50" class="tb" value="<?php echo $page->slug; ?>" />/</span>
    </div>
    <?php } ?>
    <br />
    <textarea name="taContent" id="taContent" cols="50" rows="3" rte="1"><?php echo $page->content; ?></textarea>
    <p><a href="#" id="aMetaData" title="<?php echo _('Meta Data'); ?>"><?php echo _('Meta Data'); ?> [ + ]</a> | <a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a></p>
    <br />
    <div id="dMetaData" class="hidden">
        <p>
            <label for="tMetaTitle"><?php echo _('Meta Title'); ?></label> <small>(<?php echo _('Recommended not to exceed 70 characters'); ?>)</small><br />
            <input type="text" class="tb" name="tMetaTitle" id="tMetaTitle" value="<?php echo $page->meta_title; ?>" />
        </p>
        <p>
            <label for="tMetaDescription"><?php echo _('Meta Description'); ?></label> <small>(<?php echo _('Recommended not to exceed 250 characters'); ?>)</small><br />
            <input type="text" class="tb"  name="tMetaDescription" id="tMetaDescription" value="<?php echo $page->meta_description; ?>" />
        </p>
        <p>
            <label for="tMetaKeywords"><?php echo _('Meta Keywords'); ?></label> <small>(<?php echo _('Recommended not to exceed 250 characters'); ?>)</small><br />
            <input type="text" class="tb" name="tMetaKeywords" id="tMetaKeywords" value="<?php echo $page->meta_keywords; ?>" />
        </p>
        <br />
    </div>

    <?php if ( $user->account->mobile_marketing ) { ?>
        <p><input type="checkbox" class="cb" name="cbIsMobile" id="cbIsMobile" <?php if ( $page['mobile'] ) echo "checked"; ?> /> <label for="cbIsMobile"><?php echo _('Link to Mobile Website'); ?></label></p>
        <br />
    <?php
    }

    if ( in_array( $page->slug, array( 'contact-us', 'current-offer', 'financing', 'products' ) ) )
        require VIEW_PATH . 'website/pages/' . $page->slug . '.php';
    ?>
    <br /><br />
    <br /><br />
    <p><input type="submit" id="bSubmit" value="<?php echo _('Save'); ?>" class="button" /></p>
    <?php nonce::field( 'edit' ); ?>
</form>
<br />

<div id="dUploadFile" class="hidden">
    <ul id="ulUploadFile">
        <?php
        if ( is_array( $files ) ) {
            // Set variables
            $ajax_delete_file_nonce = nonce::create('delete-file');
            $confirm = _('Are you sure you want to delete this file?');

            /**
             * @var AccountFile $file
             */
            foreach ( $files as $file ) {
                $file_name = f::name( $file->file_path );
                echo '<li id="li' . $file->id . '"><a href="', $file->file_path, '" id="aFile', $file->id, '" class="file" title="', $file_name, '">', $file_name, '</a><a href="/ajax/website/page/delete-file/?_nonce=' . $ajax_delete_file_nonce . '&amp;wfid=' . $wf['website_file_id'] . '" class="float-right" title="' . _('Delete File') . '" ajax="1" confirm="' . $confirm . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete File') . '" /></a></li>';
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
<?php
nonce::field( 'upload-file', '_ajax_upload_file' );
nonce::field( 'upload-image', '_ajax_upload_image' );
?>
<input type="hidden" id="hWebsiteID" value="<?php echo $user->account->id; ?>" />
<input type="hidden" id="hWebsitePageID" value="<?php echo $page->id; ?>" />
<?php echo $template->end(); ?>