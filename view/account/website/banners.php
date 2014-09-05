<?php
/**
 * @package Grey Suit Retail
 * @page Banners
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AccountPage $page
 * @var string $dimensions
 * @var bool $images_alt
 * @var bool $slideshow_fixed_width
 */
nonce::field( 'update_attachment_status', '_update_attachment_status' );
nonce::field( 'update_attachment_sequence', '_update_attachment_sequence' );
nonce::field( 'remove_attachment', '_remove_attachment');
nonce::field( 'create_banner', '_create_banner');
$update_extra_nonce = nonce::field( 'update_attachment_extra', '_nonce', false );
$upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );
?>
<input type="hidden" id="page-id" value="<?php echo current($attachments)->website_page_id ?>" />

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Banners
                <a href="javascript:;" class="btn btn-primary btn-sm pull-right" data-media-manager title="Open Media Manager" data-media-manager data-upload-url="<?php echo $upload_url ?>" data-search-url="<?php echo $search_url ?>" data-delete-url="<?php echo $delete_url ?>"><i class="fa fa-plus"></i> Upload or Select an Image</a>
            </header>

            <div class="panel-body">

                <p><small>(<?php echo ($slideshow_fixed_width ? 'Max. image' : 'Suggested') . ' size: ' . $dimensions?>)</small></p>

                <div id="banner-list">

                    <div class="progress progress-sm hidden" id="new-element-loader">
                        <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>

                    <?php foreach ( $attachments as $attachment ): ?>

                        <div class="banner <?php echo $attachment->status == '0' ? 'disabled' : '' ?>" data-attachment-id="<?php echo $attachment->id ?>">

                            <div class="banner-actions">
                                <small><?php echo $dimensions; ?></small>
                                <input type="checkbox" data-toggle="switch" value="active" <?php if ( $attachment->status == '1' ) echo 'checked' ?>/>

                                <a href="javascript:;" class="remove" title="Delete this Banner"><i class="fa fa-trash-o"></i></a>
                            </div>

                            <img src="<?php echo $attachment->value ?>" />
                            <form action="/website/update-attachment-extra/" method="post" role="form" ajax="1">
                                <div class="form-group">
                                    <label>Image Link:</label>
                                    <input type="text" class="form-control" name="extra" value="<?php echo $attachment->extra ?>" placeholder="Link URL" />
                                </div>
                                <p>
                                    <input type="hidden" name="hAccountPageAttachmentId" value="<?php echo $attachment->id ?>" />
                                    <?php echo $update_extra_nonce ?>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </p>
                            </form>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>
        </section>
    </div>
</div>

<div id="banner-template" class="banner hidden">
    <div class="banner-actions">
        <small><?php echo $dimensions; ?></small>
        <input type="checkbox" value="active" checked/>
        <a href="javascript:;" class="remove" title="Delete this Banner"><i class="fa fa-trash-o"></i></a>
    </div>

    <h3>Image</h3>
    <img src="" />
    <form action="/website/update-attachment-extra/" method="post" role="form" ajax="1">
        <div class="form-group">
            <label>Image Link:</label>
            <input type="text" class="form-control" name="extra" placeholder="Link URL" />
        </div>
        <p>
            <input type="hidden" name="hAccountPageAttachmentId" />
            <?php echo $update_extra_nonce ?>
            <button type="submit" class="btn btn-primary">Save</button>
        </p>
    </form>
</div>
