<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit an Article
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 * @var array $files
 */

echo $template->start( ( isset( $_GET['kbaid'] ) ? _('Edit Article') : _('Add Article') ), '../sidebar' );
echo $form;
?>
<div id="dUploadFile" class="hidden">
    <input type="text" class="tb" id="tFileName" placeholder="<?php echo _('Enter File Name'); ?>..." error="<?php echo _('You must type in a file name before uploading a file.'); ?>" />
    <a href="#" id="aUploadFile" class="button" title="<?php echo _('Upload'); ?>"><?php echo _('Browse'); ?></a>
    <a href="#" class="button loader hidden" id="upload-file-loader" title="<?php echo _('Loading'); ?>"><img src="/images/buttons/loader.gif" alt="<?php echo _('Loading'); ?>" /></a>
    <div class="hidden-fix position-absolute" id="upload-file"></div>
    <br /><br />

    <input type="text" class="tb" id="file-pattern" placeholder="Narrow your search..." />
    <div id="file-list">
        <?php echo '<p class="no-files">', _('You have not uploaded any files.') . '</p>'; ?>
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
nonce::field( 'get_categories', '_get_categories' );
nonce::field( 'get_pages', '_get_pages' );
echo $template->end();
?>