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
 */

echo $template->start( _('Edit Page') );

if ( !empty( $errs ) )
    echo "<p class='red'>$errs</p>";
?>
<form name="fEditPage" action="<?php echo url::add_query_arg( 'apid', $page->id, '/website/edit/' ); ?>" method="post">
    <div id="title-container">
        <input name="tTitle" id="tTitle" class="tb" value="<?php echo $page_title; ?>" tmpval="<?php echo _('Page Title...'); ?>" />
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
        <a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a>
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
                <h2><?php echo _('Narrow Your Search'); ?></h2>
                <br />
                <table id="tNarrowSearch">
                    <tr>
                        <td width="264">
                            <select id="sAutoComplete">
                                <option value="sku"><?php echo _('SKU'); ?></option>
                                <option value="product"><?php echo _('Product Name'); ?></option>
                                <option value="brand"><?php echo _('Brand'); ?></option>
                            </select>
                        </td>
                        <td valign="top"><input type="text" class="tb" id="tAutoComplete" tmpval="<?php echo _('Enter SKU...'); ?>" style="width: 100% !important;" /></td>
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
    </div>

    <?php if ( $user->account->mobile_marketing ) { ?>
        <p><input type="checkbox" class="cb" name="cbIsMobile" id="cbIsMobile" <?php if ( $page->mobile ) echo "checked"; ?> /> <label for="cbIsMobile"><?php echo _('Link to Mobile Website'); ?></label></p>
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
<?php echo $js_validation; ?>
<br />

<div id="dUploadFile" class="hidden">
    <ul id="ulUploadFile">
        <?php
        if ( !empty( $files ) ) {
            // Set variables
            $delete_file_nonce = nonce::create('delete_file');
            $confirm = _('Are you sure you want to delete this file?');

            /**
             * @var AccountFile $file
             */
            foreach ( $files as $file ) {
                $file_name = f::name( $file->file_path );
                echo '<li id="li' . $file->id . '"><a href="', $file->file_path, '" id="aFile', $file->id, '" class="file" title="', $file_name, '">', $file_name, '</a><a href="' . url::add_query_arg( array( '_nonce' => $delete_file_nonce, 'afid' => $file->id ), '/website/delete-file/' ) . '" class="float-right" title="' . _('Delete File') . '" ajax="1" confirm="' . $confirm . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete File') . '" /></a></li>';
            }
        } else {
            echo '<li class="no-files">', _('You have not uploaded any files.') . '</li>';
        }
        ?>
    </ul>
    <br />

    <input type="text" class="tb" id="tFileName" tmpval="<?php echo _('Enter File Name'); ?>..." error="<?php echo _('You must type in a file name before uploading a file.'); ?>" />
    <a href="#" id="aUploadFile" class="button" title="<?php echo _('Upload'); ?>"><?php echo _('Upload'); ?></a>
    <a href="#" class="button loader hidden" id="upload-file-loader" title="<?php echo _('Loading'); ?>"><img src="/images/buttons/loader.gif" alt="<?php echo _('Loading'); ?>" /></a>
    <div class="hidden-fix position-absolute" id="upload-file"></div>
    <br /><br />
    <div id="dCurrentLink" class="hidden">
        <p><strong><?php echo _('Current Link'); ?>:</strong></p>
        <p><input type="text" class="tb" id="tCurrentLink" value="<?php echo _('No link selected'); ?>" style="width:100%;" /></p>
    </div>
</div>
<?php
nonce::field( 'upload_file', '_upload_file' );
nonce::field( 'autocomplete_owned', '_autocomplete_owned' );
?>
<input type="hidden" id="hAccountPageId" value="<?php echo $page->id; ?>" />
<?php echo $template->end(); ?>