<?php
/**
 * @package Grey Suit Retail
 * @page Step1 | Create | Campaigns | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var User $user
 * @var EmailMessage $campaign
 * @var EmailList[] $email_lists
 * @var array $settings
 * @var string $timezone
 * @var string $server_timezone
 * @var AccountFile[] $files
 * @var string $default_from
 * @var boolean $overwrite_from
 * @var DateTime $scheduled_datetime
 */
?>

<div class="email-marketing-wrapper clear">

    <div class="email-marketing-left">
        <div class="email-layout" id="email-editor"><?php echo $campaign->message ?></div>

        <p class="float-left">
            <a href="#" data-step="1" class="button" title="<?php echo _('< Back'); ?>"><?php echo _('< Back'); ?></a>
            <a class="button save-draft" title="<?php echo _('Save Draft'); ?>"><?php echo _('Save Draft'); ?></a>
        </p>
        <p class="float-right">
            <a href="#" data-step="3" class="button" title="<?php echo _('Next'); ?>"><?php echo _('Next >'); ?></a>
        </p>
        <br clear="all" />
    </div><!-- .email-marketing-left -->

    <div class="email-marketing-right">
        <ul class="idTabs clear">
            <li><a href="#email-content" class="selected">Content</a></li>
            <li><a href="#email-layouts">Layout</a></li>
            <li><a href="#email-settings">Settings</a></li>
        </ul>
        <div id="email-content" class="tab-content">
            <ul class="content-thumbnails clear">
                <li data-content-type="product"><img src="/images/campaigns/product.png" /><br>Add Product</li>
                <li data-content-type="text"><img src="/images/campaigns/text.png" /><br>Add Text</li>
                <li data-content-type="image"><img src="/images/campaigns/image.png" /><br>Add Image</li>
            </ul>
        </div>
        <div id="email-layouts" class="tab-content">
            <ul class="layout-thumbnails clear">
                <li data-layout="layout-1"><img src="/images/campaigns/layout-1.jpg" /></li>
                <li data-layout="layout-2"><img src="/images/campaigns/layout-2.jpg" /></li>
                <li data-layout="layout-3"><img src="/images/campaigns/layout-3.jpg" /></li>
                <li data-layout="layout-4"><img src="/images/campaigns/layout-4.jpg" /></li>
                <li data-layout="layout-5"><img src="/images/campaigns/layout-5.jpg" /></li>
                <li data-layout="layout-6"><img src="/images/campaigns/layout-6.jpg" /></li>
                <li data-layout="layout-7"><img src="/images/campaigns/layout-7.jpg" /></li>
                <li data-layout="layout-8"><img src="/images/campaigns/layout-8.jpg" /></li>
            </ul>
        </div>
        <div id="email-settings" class="tab-content">
            <label for="no-template">
                <input type="checkbox" class="cb" name="no-template" id="no-template" value="1" <?php if ( $campaign->id && !$campaign->email_template_id ) echo 'checked="checked"' ?>>
                Remove Header/Footer
            </label>
        </div>
    </div><!-- .email-marketing-right -->

</div><!-- .email-marketing-wrapper -->

<div class="hidden" id="email-builder-types">

    <div class="content-type-template" data-content-type="product">
        <div class="placeholder-actions">
            <a data-action="clear" href="#"><img src="/images/icons/x.png" /></a>
            <a data-action="edit" href="#"><img src="/images/icons/edit.png" /></a>

            <input type="text" class="products-autocomplete" placeholder="SKU or Name." />
        </div>
        <div class="placeholder-content content-type-product"></div>
    </div>

    <div class="content-type-template" data-content-type="text">
        <div class="placeholder-actions">
            <a data-action="clear" href="#"><img src="/images/icons/x.png" /></a>
            <a data-action="edit" href="#dTextEditor" title="<?php echo _('Edit Content'); ?>" rel="dialog" class="open-text-editor"><img src="/images/icons/edit.png" /></a>
        </div>
        <div class="placeholder-content content-type-text"></div>
    </div>

    <div class="content-type-template" data-content-type="image">
        <div class="placeholder-actions">
            <a data-action="clear" href="#"><img src="/images/icons/x.png" /></a>
            <a data-action="edit"  href="#dUploadFile" title="<?php echo _('Media Manager'); ?>" rel="dialog" class="open-media-manager"><img src="/images/icons/edit.png" /></a>
            <a data-action="edit-link" class="hidden" href="#"><img src="/images/icons/link.png" /></a>

            <input type="text" class="image-link-url hidden" placeholder="Enter URL" />
            <a data-action="save-link" class="hidden" href="#"><img src="/images/icons/disk.png" /></a>
        </div>
        <div class="placeholder-content content-type-image"></div>
    </div>

    <div data-layout="layout-1">
        <table width="600">
            <tr class="email-row-4">
                <td colspan="6" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="6" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-3">
                <td colspan="3" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
        </table>
    </div>

    <div data-layout="layout-2">
        <table width="600">
            <tr class="email-row-6">
                <td colspan="12" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
        </table>
    </div>

    <div data-layout="layout-3">
        <table width="600">
            <tr class="email-row-1">
                <td colspan="12" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-3">
                <td colspan="4" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="4" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="4" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-4">
                <td colspan="4" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="4" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="4" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
        </table>
    </div>

    <div data-layout="layout-4">
        <table width="600">
            <tr class="email-row-4">
                <td colspan="6" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="6" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-4">
                <td colspan="6" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="6" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
        </table>
    </div>

    <div data-layout="layout-5">
        <table width="600">
            <tr class="email-row-6">
                <td colspan="4">
                    <table>
                        <tr class="email-row-3">
                            <td colspan="12" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                        </tr>
                        <tr class="email-row-6">
                            <td colspan="12" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                        </tr>
                    </table>
                </td>
                <td colspan="8">
                    <table>
                        <tr class="email-row-6">
                            <td colspan="12" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                        </tr>
                        <tr class="email-row-6">
                            <td colspan="12" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div data-layout="layout-6">
        <table width="600">
            <tr class="email-row-6">
                <td colspan="8">
                    <table>
                        <tr class="email-row-6">
                            <td colspan="12" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                        </tr>
                        <tr class="email-row-6">
                            <td colspan="12" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                        </tr>
                    </table>
                </td>
                <td colspan="4">
                    <table>
                        <tr class="email-row-3">
                            <td colspan="12" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                        </tr>
                        <tr class="email-row-6">
                            <td colspan="12" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div data-layout="layout-7">
        <table width="600">
            <tr class="email-row-1">
                <td colspan="12" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-3">
                <td colspan="6" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="6" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-4">
                <td colspan="6" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="6" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
        </table>
    </div>

    <div data-layout="layout-8">
        <table width="600">
            <tr class="email-row-1">
                <td colspan="12" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-3">
                <td colspan="12" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-4">
                <td colspan="12" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
        </table>
    </div>

</div>

<div id="dUploadFile" class="hidden">
    <input type="text" class="tb" id="tFileName" placeholder="<?php echo _('Enter File Name'); ?>..." error="<?php echo _('You must type in a file name before uploading a file.'); ?>" />
    <a href="#" id="aUploadFile" class="button" title="<?php echo _('Upload'); ?>"><?php echo _('Browse'); ?></a>
    <a href="#" class="button loader hidden" id="upload-file-loader" title="<?php echo _('Loading'); ?>"><img src="/images/buttons/loader.gif" alt="<?php echo _('Loading'); ?>" /></a>
    <div class="hidden-fix position-absolute" id="upload-file"></div>
    <br /><br />

    <div id="file-list">/
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
                <td class="text-right"><a href="#" id="select-image" class="button close"><?php echo _('Select'); ?></a></td>
            </tr>
        </table>
    </div>
</div>
<?php
nonce::field( 'upload_file', '_upload_file' );
nonce::field( 'autocomplete_owned', '_autocomplete_owned' );
nonce::field( 'get_product_dialog_info', '_get_product_dialog_info' );
nonce::field( 'preview', '_preview' );
?>

<div id="dTextEditor" class="hidden">
    <div id="editor-container"></div>
    <br/>
    <p class="text-right">
        <a href="#" id="save-text" class="button close"><?php echo _('Save'); ?></a>
    </p>
</div>


<br clear="all" />