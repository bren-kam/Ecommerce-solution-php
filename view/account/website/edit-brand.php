<?php
/**
 * @package Grey Suit Retail
 * @page Edit Brand
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AccountBrand $brand
 */

$upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );
?>

<form id="fEditPage" action="/website/edit-brand/?bid=<?php echo $_GET['bid'] ?>" method="post" role="form">

    <div class="row-fluid">
        <div class="col-lg-9">
            <section class="panel">
                <header class="panel-heading">
                    Edit Brand - <?php echo $brand->name ?>
                </header>

                <div class="panel-body">
                    <div class="form-group">
                        <label for="tTitle">Title:</label>
                        <input type="text" class="form-control" name="tTitle" id="tTitle" placeholder="Title" value="<?php echo $brand->name ?>"/>
                    </div>

                    <div class="form-group">
                        <label for="taContent">Content:</label>
                        <textarea name="taContent" id="taContent" rows="3" cols="60" rte="1"><?php echo $brand->content ?></textarea>
                    </div>

                    <p>
                        <button type="button" class="btn btn-xs btn-default" title="Open Media Manager" data-media-manager data-upload-url="<?php echo $upload_url ?>" data-search-url="<?php echo $search_url ?>" data-delete-url="<?php echo $delete_url ?>">Upload File</button>
                    </p>

                    <p class="clearfix">
                        <?php nonce::field( 'edit_brand' ) ?>
                        <button type="submit" class="btn btn-primary pull-right">Save</button>
                    </p>
                </div>
            </section>

        </div>

        <div class="col-lg-3">
            <section class="panel">
                <header class="panel-heading">
                    Settings
                </header>

                <div class="panel-body">
                    <div class="form-group">
                        <label for="tMetaTitle">Meta Title:</label>
                        <input type="text" class="form-control" name="tMetaTitle" id="tMetaTitle" value="<?php echo $brand->meta_title ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="tMetaDescription">Meta Description:</label>
                        <input type="text" class="form-control" name="tMetaDescription" id="tMetaDescription" value="<?php echo $brand->meta_description ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="tMetaKeywords">Meta Keywords:</label>
                        <input type="text" class="form-control" name="tMetaKeywords" id="tMetaKeywords" value="<?php echo $brand->meta_keywords ?>"/>
                    </div>

                    <p><strong>Text Placement:</strong></p>
                    <div class="radio">
                        <label>
                            <input type="radio" name="rPosition" value="1"<?php if ( '0' != $brand->top ) echo ' checked="checked"'; ?> />
                            Text above products
                        </label>
                        <label>
                            <input type="radio" name="rPosition" value="0"<?php if ( '0' == $brand->top ) echo ' checked="checked"'; ?> />
                            Text after products
                        </label>
                    </div>

                </div>
            </section>
        </div>
    </div>



</form>