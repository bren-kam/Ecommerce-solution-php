<?php
/**
 * @package Grey Suit Retail
 * @page Edit Category
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AccountCategory $category
 */

echo $template->start( _('Edit Category') . ' - ' . $category->title );
?>

<form name="fEditCategory" action="<?php echo url::add_query_arg( 'cid', $category->category_id, '/website/edit-category/' ); ?>" method="post">
    <div id="title-container">
        <input name="tTitle" id="tTitle" class="tb" value="<?php echo $category->title; ?>" placeholder="<?php echo _('Category Title...'); ?>" />
    </div>
    <br />
    <textarea name="taContent" id="taContent" cols="50" rows="3" rte="1"><?php echo $category->content; ?></textarea>
    <p><a href="#" id="aMetaData" title="<?php echo _('Meta Data'); ?>"><?php echo _('Meta Data'); ?> [ + ]</a> | <a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a></p>
    <br />
    <div id="dMetaData" class="hidden">
        <p>
            <label for="tMetaTitle"><?php echo _('Meta Title'); ?></label> <small>(<?php echo _('Recommended not to exceed 70 characters'); ?>)</small><br />
            <input type="text" class="tb" name="tMetaTitle" id="tMetaTitle" value="<?php echo $category->meta_title; ?>" />
        </p>
        <p>
            <label for="tMetaDescription"><?php echo _('Meta Description'); ?></label> <small>(<?php echo _('Recommended not to exceed 250 characters'); ?>)</small><br />
            <input type="text" class="tb"  name="tMetaDescription" id="tMetaDescription" value="<?php echo $category->meta_description; ?>" />
        </p>
        <p>
            <label for="tMetaKeywords"><?php echo _('Meta Keywords'); ?></label> <small>(<?php echo _('Recommended not to exceed 250 characters'); ?>)</small><br />
            <input type="text" class="tb" name="tMetaKeywords" id="tMetaKeywords" value="<?php echo $category->meta_keywords; ?>" />
        </p>
        <br />
    </div>
    <br />
    <table>
        <tr>
            <td class="top" width="100"><label for="rPosition1"><?php echo _('Position'); ?>:</label></td>
            <td>
                <p><input type="radio" class="rb" name="rPosition" id="rPosition1" value="1"<?php if ( '0' != $category->top ) echo ' checked="checked"'; ?> /> <label for="rPosition1"><?php echo _('Top'); ?></label></p>
                <p><input type="radio" class="rb" name="rPosition" id="rPosition2" value="0"<?php if ( '0' == $category->top ) echo ' checked="checked"'; ?> /> <label for="rPosition2"><?php echo _('Bottom'); ?></label></p>
            </td>
        </tr>
    </table>
    <br /><br />
    <p><input type="submit" id="bSubmit" value="<?php echo _('Save'); ?>" class="button" /></p>
    <?php nonce::field( 'edit_category' ); ?>
</form>
<br />

<div id="dUploadFile" class="hidden">
    <input type="text" class="tb" id="tFileName" placeholder="<?php echo _('Enter File Name'); ?>..." error="<?php echo _('You must type in a file name before uploading a file.'); ?>" />
    <a href="#" id="aUploadFile" class="button" title="<?php echo _('Upload'); ?>"><?php echo _('Browse'); ?></a>
    <a href="#" class="button loader hidden" id="upload-file-loader" title="<?php echo _('Loading'); ?>"><img src="/images/buttons/loader.gif" alt="<?php echo _('Loading'); ?>" /></a>
    <div class="hidden-fix position-absolute" id="upload-file"></div>
    <br /><br />

    <div id="file-list">
    <?php
    if ( empty( $files ) ) {
        echo '<p class="no-files">', _('You have not uploaded any files.') . '</p>';
    } else {
        // Set variables
        $delete_file_nonce = nonce::create('delete_file');
        $confirm = _('Are you sure you want to delete this file?');

        /**
         * @var AccountFile $file
         */
        foreach ( $files as $file ) {
            $file_name = f::name( $file->file_path );
            $extension = f::extension( $file->file_path );
            $date = new DateTime( $file->date_created );

            if ( in_array( $extension, image::$extensions ) ) {
                // It's an image!
                echo '<div id="file-' . $file->id . '" class="file"><a href="#', $file->file_path, '" id="aFile', $file->id, '" class="file img" title="', $file_name, '" rel="' . $date->format( 'F jS, Y') . '"><img src="' . $file->file_path . '" alt="' . $file_name . '" /></a><a href="' . url::add_query_arg( array( '_nonce' => $delete_file_nonce, 'afid' => $file->id ), '/website/delete-file/' ) . '" class="delete-file" title="' . _('Delete File') . '" ajax="1" confirm="' . $confirm . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete File') . '" /></a></div>';
            } else {
                // It's not an image!
                echo '<div id="file-' . $file->id . '" class="file"><a href="#', $file->file_path, '" id="aFile', $file->id, '" class="file" title="', $file_name, '" rel="' . $date->format( 'F jS, Y') . '"><img src="/images/icons/extensions/' . $extension . '.png" alt="' . $file_name . '" /><span>' . $file_name . '</span></a><a href="' . url::add_query_arg( array( '_nonce' => $delete_file_nonce, 'afid' => $file->id ), '/website/delete-file/' ) . '" class="delete-file" title="' . _('Delete File') . '" ajax="1" confirm="' . $confirm . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete File') . '" /></a></div>';
            }
        }
    }
    ?>
    </div>

    <br /><br />
    <div id="dCurrentLink" class="hidden">
        <p><strong><?php echo _('Current Link'); ?>:</strong></p>
        <p><input type="text" class="tb" id="tCurrentLink" value="<?php echo _('No link selected'); ?>" /></p>
        <br />
        <table class="col-1">
            <tr>
                <td class="col-3"><strong><?php echo _('Date'); ?>:</strong></td>
                <td class="col-3"><strong><?php echo _('Size'); ?>:</strong></td>
                <td class="col-3">&nbsp;</td>
            </tr>
            <tr>
                <td id="tdDate"></td>
                <td id="tdSize"></td>
                <td class="text-right"><a href="#" id="insert-into-post" class="button close"><?php echo _('Insert Into Post'); ?></a></td>
            </tr>
        </table>
    </div>
</div>
<?php nonce::field( 'upload_file', '_upload_file' ); ?>
<input type="hidden" id="hAccountId" value="<?php echo $user->account->id; ?>" />

<?php echo $template->end(); ?>