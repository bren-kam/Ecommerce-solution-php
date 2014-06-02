<?php
/**
 * @package Grey Suit Retail
 * @page Edit Page
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AccountPage $page
 * @var string $page_title
 * @var array $files
 * @var string $js_validation
 * @var string $errs
 * @var int $product_count
 * @var string $contact_validation
 */

echo $template->start( _('Edit Page') );

if ( !empty( $errs ) )
    echo "<p class='red'>$errs</p>";
?>
<form name="fEditPage" action="<?php echo url::add_query_arg( 'apid', $page->id, '/website/edit/' ); ?>" method="post"
      xmlns="http://www.w3.org/1999/html">
    <div id="title-container">
        <input name="tTitle" id="tTitle" class="tb" value="<?php echo $page_title; ?>" placeholder="<?php echo _('Page Title...'); ?>" />
    </div>
    <?php if ( 'home' != $page->slug ) { ?>
    <div id="dPageSlug">
        <span><strong><?php echo _('Link'); ?>:</strong> http://<?php echo $user->account->domain; ?>/<input type="text" name="tPageSlug" id="tPageSlug" maxlength="50" class="tb" value="<?php echo $page->slug; ?>" />/</span>
    </div>
    <?php } ?>
    <br />
    <textarea name="taContent" id="taContent" cols="50" rows="3" rte="1"><?php echo $page->content; ?></textarea>
    <p>
        <a href="#" id="aMetaData" title="<?php echo _('Meta Data'); ?>"><?php echo _('Meta Data'); ?> [ + ]</a> |
        <a href="#" id="aAddProducts" title="<?php echo _('Add Products'); ?>"><?php echo _('Add Products'); ?> [ <?php echo ( empty( $page->products ) ) ? '+' : '&ndash;'; ?> ]</a> |
        <a href="#dUploadFile" title="<?php echo _('Media Manager'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a>
    </p>
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
    <div id="dAddProducts"<?php if ( empty( $page->products ) ) echo ' class="hidden"'; ?>>
        <div id="dNarrowSearchContainer">
            <div id="dNarrowSearch">
                <h3 class="float-right"><?php echo _('Limit'); ?>: <span id="product-count"><?php echo $product_count; ?></span> / 100</h3>
                <h2 class="float-left"><?php echo _('Narrow Your Search'); ?></h2>
                <br class="clr" /><br />
                <table id="tNarrowSearch">
                    <tr>
                        <td width="264">
                            <select id="sAutoComplete">
                                <option value="sku"><?php echo _('SKU'); ?></option>
                                <option value="product"><?php echo _('Product Name'); ?></option>
                                <option value="brand"><?php echo _('Brand'); ?></option>
                            </select>
                        </td>
                        <td valign="top"><input type="text" class="tb" id="tAutoComplete" placeholder="<?php echo _('Enter SKU...'); ?>" style="width: 100% !important;" /></td>
                        <td class="text-right" width="125"><a href="#" id="aSearch" title="<?php echo _('Search'); ?>" class="button"><?php echo _('Search'); ?></a></td>
                    </tr>
                </table>
                <img id="iNYSArrow" src="/images/narrow-your-search.png" alt="" width="76" height="27" />
            </div>
        </div>
        <br clear="left" /><br />
        <br /><br />
        <br />
        <table id="tAddProducts" class="manual dt">
            <thead>
                <tr>
                    <th width="45%" sort="1"><?php echo _('Name'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
                    <th width="25%"><?php echo _('Brand'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
                    <th width="15%"><?php echo _('SKU'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
                    <th width="15%"><?php echo _('Status'); ?><img src="/images/trans.gif" width="10" height="8" /></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <br /><br />

        <h2><?php echo _('Selected Products'); ?></h2>
        <div id="dSelectedProducts">
            <?php
            if ( isset( $page->products ) ) {
                /**
                 * @var Product $product
                 */
                foreach ( $page->products as $product ) {
                    $images = $product->get_images();
                    $product_image = 'http://' . $product->industry . '.retailcatalog.us/products/' . $product->id . '/' . current( $images );
                    ?>
                    <div id="dProduct_<?php echo $product->id; ?>" class="product">
                        <h4><?php echo $product->name; ?></h4>
                        <p align="center"><img src="<?php echo $product_image; ?>" alt="<?php echo $product->name; ?>" height="110" style="margin:10px" /></p>
                        <p><?php echo _('Brand'); ?>: <?php echo $product->brand; ?></p>
                        <p class="product-actions" id="pProductAction<?php echo $product->id; ?>"><a href="#" class="remove-product" title="<?php echo _('Remove'); ?>"><?php echo _('Remove'); ?></a></p>
                        <input type="hidden" name="products[]" class="hidden" value="<?php echo $product->id; ?>" />
                    </div>
                <?php
                }
            }
            ?>
        </div>
        <br clear="left" />
        <br />
        <table>
            <tr>
                <td class="top" width="100"><label for="rPosition1"><?php echo _('Text Position'); ?>:</label></td>
                <td>
                    <p><input type="radio" class="rb" name="rPosition" id="rPosition1" value="1"<?php if ( '0' != $page->top ) echo ' checked="checked"'; ?> /> <label for="rPosition1"><?php echo _('Top'); ?></label></p>
                    <p><input type="radio" class="rb" name="rPosition" id="rPosition2" value="0"<?php if ( '0' == $page->top ) echo ' checked="checked"'; ?> /> <label for="rPosition2"><?php echo _('Bottom'); ?></label></p>
                </td>
            </tr>
        </table>
    </div>
    <br />
    <?php
    if ( in_array( $page->slug, array( 'contact-us', 'current-offer', 'financing', 'products' ) ) )
        require VIEW_PATH . 'website/pages/' . $page->slug . '.php';
    ?>
    <br /><br />
    <br /><br />
    <p><input type="submit" id="bSubmit" value="<?php echo _('Save'); ?>" class="button" /></p>
    <?php nonce::field( 'edit' ); ?>
</form>
<?php echo $js_validation; ?>
<br />

<?php if ( 'contact-us' == $page->slug ) { ?>
<div id="dAddEditLocation" class="hidden">
    <form action="/website/add-edit-location/" name="fAddEditLocation" id="fAddEditLocation" method="post" ajax="1">
        <table class="form width-auto">
            <tr>
                <td><input type="text" class="tb" name="name" id="name" tabindex="1" placeholder="Name"></td>
                <td width="10%">&nbsp;</td>
                <td><input type="text" class="tb" name="address" id="address" tabindex="6" placeholder="Address"></td>
            </tr>
            <tr>
                <td><input type="text" class="tb" name="phone" id="phone" maxlength="21" tabindex="2" placeholder="Phone"></td>
                <td>&nbsp;</td>
                <td><input type="text" class="tb" name="city" id="city" tabindex="7" placeholder="City"></td>
            </tr>
            <tr>
                <td><input type="text" class="tb" name="fax" id="fax" maxlength="21" tabindex="2" placeholder="Fax"></td>
                <td>&nbsp;</td>
                <td>
                    <select name="state" id="state" tabindex="8">
                        <option value="">-- <?php echo _('Select State'); ?> --</option>
                        <?php data::states(); ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><input type="text" class="tb" name="email" id="email" maxlength="200" tabindex="3" placeholder="Email"></td>
                <td>&nbsp;</td>
                <td><input type="text" class="tb" name="zip" id="zip" maxlength="10" tabindex="9" placeholder="Zip"></td>
            </tr>
            <tr>
                <td><input type="text" class="tb" name="website" id="website" maxlength="200" tabindex="4" placeholder="Website"></td>
            </tr>
            <tr>
                <td><textarea name="store-hours" id="store-hours" cols="30" rows="3" tabindex="5" placeholder="Store Hours"></textarea></td>
            </tr>
            <tr>
                <td>
                    <a href="#" id="aUploadStoreImage" class="button" title="<?php echo _('Upload Store Image'); ?>"><?php echo _('Upload Store Image'); ?></a>
                    <a href="#" class="button loader hidden" id="upload-store-image-loader" title="<?php echo _('Loading'); ?>"><img src="/images/buttons/loader.gif" alt="<?php echo _('Loading'); ?>" /></a>
                    <div class="hidden-fix position-absolute" id="upload-store-image"></div>
                </td>
                <td>&nbsp;</td>
                <td>
                    <input type="hidden" name="store-image" id="store-image" />
                    <img id="store-image-preview" />
                </td>
            </tr>
        </table>
        <input type="submit" class="hidden" id="bAddEditLocation" value="<?php echo _('Submit'); ?>">
        <input type="hidden" name="wlid" id="wlid" value="">
        <?php nonce::field('add_edit_location'); ?>
    </form>
    <?php echo $contact_validation; ?>
    <div class="boxy-footer hidden">
        <p class="col-2 float-left"><a href="#" class="close"><?php echo _('Cancel'); ?></a></p>
        <p class="text-right col-2 float-right"><input type="submit" id="bSubmitLocation" class="button" value="<?php echo _('Submit'); ?>" /></div>
    </div>
</div>
<?php } ?>

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
nonce::field( 'autocomplete_owned', '_autocomplete_owned' );
?>
<input type="hidden" id="hAccountPageId" value="<?php echo $page->id; ?>" />
<?php echo $template->end(); ?>