<?php
/**
 * @package Grey Suit Retail
 * @page Custom 404
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $text_404
 * @var array $files
 */

echo $template->start( _('Custom 404 Page') );
?>

<hr />
<br />

<p>Set this text to customize the message shown when a visitor enters a non existing Page.</p>

<form action="" name="fHeader" method="post">
    <p>
        <label for="taContent">404 Page Text:</label>
        <textarea id="taContent" name="text-404" rte="1"><?php echo $text_404 ?></textarea>
    </p>
    <p>
        <a href="#dUploadFile" title="<?php echo _('Media Manager'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a>
    </p>
    <input type="submit" class="button" value="<?php echo _('Save'); ?>">
    <?php nonce::field('custom_404'); ?>
</form>
<br clear="all"><br>

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
<?php
nonce::field( 'upload_file', '_upload_file' );
?>

<?php echo $template->end(); ?>