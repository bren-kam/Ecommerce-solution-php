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
 * @var bool $hide_sidebar
 */

$upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );
nonce::field( 'set_pagemeta', '_set_pagemeta' );
?>

<form id="fEditPage" action="/website/edit/?apid=<?php echo $page->id ?>" method="post" role="form" data-account-page-id="<?php echo $page->id ?>">

    <div class="row-fluid">
        <div class="col-lg-9">
            <section class="panel">
                <header class="panel-heading">
                    Edit Page
                </header>

                <div class="panel-body">
                    <?php if ( $errs ) { ?>
                        <div class="alert alert-danger">
                            <?php echo $errs; ?>
                        </div>
                    <?php } ?>

                    <div class="form-group">
                        <label for="tTitle">Title:</label>
                        <input type="text" class="form-control" name="tTitle" id="tTitle" placeholder="Title" value="<?php echo $page->title ?>"/>
                    </div>

                    <p><strong>Link:</strong> http://<?php echo $user->account->domain ?>/<input type="text" id="tPageSlug" name="tPageSlug" value="<?php echo $page->slug ?>" placeholder="Slug" />/</p>

                    <div class="form-group">
                        <label for="taContent">Content:</label>
                        <textarea name="taContent" id="taContent" rows="3" cols="60" rte="1"><?php echo $page->content ?></textarea>
                    </div>

                    <p>
                        <button type="button" class="btn btn-xs btn-default" title="Open Media Manager" data-media-manager data-upload-url="<?php echo $upload_url ?>" data-search-url="<?php echo $search_url ?>" data-delete-url="<?php echo $delete_url ?>">Upload File</button>
                    </p>

                    <p class="clearfix">
                        <?php nonce::field( 'edit' ) ?>
                        <button type="submit" class="btn btn-primary pull-right">Save</button>
                    </p>
                </div>
            </section>

            <?php include __DIR__ . '/edit/_products.php' ?>

            <?php
            if ( in_array ($page->slug, array( 'contact-us', 'current-offer', 'financing' ) ) )
                include __DIR__ . '/edit/_' . $page->slug . '.php';
            ?>

        </div>

        <div class="col-lg-3">
            <section class="panel">
                <header class="panel-heading">
                    Settings
                </header>

                <div class="panel-body">
                    <div class="form-group">
                        <label for="tMetaTitle">Meta Title:</label>
                        <input type="text" class="form-control" name="tMetaTitle" id="tMetaTitle" value="<?php echo $page->meta_title ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="tMetaDescription">Meta Description:</label>
                        <input type="text" class="form-control" name="tMetaDescription" id="tMetaDescription" value="<?php echo $page->meta_description ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="tMetaKeywords">Meta Keywords:</label>
                        <input type="text" class="form-control" name="tMetaKeywords" id="tMetaKeywords" value="<?php echo $page->meta_keywords ?>"/>
                    </div>
                    <?php if ( $user->account->is_new_template() ): ?>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="cbHideSidebar" value="yes" <?php if ( $hide_sidebar == '1' ) echo 'checked="checked"'; ?> />
                                Hide Sidebar
                            </label>
                        </div>
                    <?php endif; ?>

                    <?php if ( $product_count == 0 ): ?>
                        <button type="button" id="show-product-form" class="btn btn-xs btn-default">Add Products</button>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>



</form>