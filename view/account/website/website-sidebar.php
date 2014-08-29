<?php
/**
 * @package Grey Suit Retail
 * @page Sidebar
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AccountPage $page
 * @var string $dimensions
 * @var array $files
 * @var AccountPageAttachment[] $attachments
 * @var bool $images_alt
 */

nonce::field( 'update_attachment_status', '_update_attachment_status' );
nonce::field( 'update_attachment_sequence', '_update_attachment_sequence' );
nonce::field( 'remove_attachment', '_remove_attachment');
nonce::field( 'upload_sidebar_video', '_upload_sidebar_video');
nonce::field( 'create_sidebar_image', '_create_sidebar_image');
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
                Sidebar Elements
                <a href="javascript:;" class="btn btn-primary btn-sm pull-right" data-media-manager title="Open Media Manager" data-media-manager data-upload-url="<?php echo $upload_url ?>" data-search-url="<?php echo $search_url ?>" data-delete-url="<?php echo $delete_url ?>"><i class="fa fa-plus"></i> Upload or Select an Image</a>
            </header>

            <div class="panel-body">

                <div id="sidebar-list">

                    <div class="progress progress-sm hidden" id="new-element-loader">
                        <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>

                    <?php foreach ( $attachments as $attachment ): ?>

                        <div class="sidebar-element <?php echo $attachment->key ?> <?php echo $attachment->status == '0' ? 'disabled' : '' ?>" data-attachment-id="<?php echo $attachment->id ?>">

                            <div class="sidebar-actions">
                                <input type="checkbox" data-toggle="switch" value="active" <?php if ( $attachment->status == '1' ) echo 'checked' ?>/>

                                <?php if ( $attachment->key == 'sidebar-image' ): ?>
                                    <a href="javascript:;" class="remove" title="Delete this Element"><i class="fa fa-trash-o"></i></a>
                                <?php endif; ?>
                            </div>

                            <?php if ( $attachment->key == 'email' ): ?>
                                <h3>Email Sign Up</h3>
                                <form action="/website/update-sidebar-email/" method="post" role="form" ajax="1">

                                    <div class="form-group">
                                        <textarea class="form-control" name="taEmail" cols="50" rows="3"><?php echo $attachment->value; ?></textarea>
                                    </div>

                                    <p>
                                        <input type="hidden" name="hAccountPageAttachmentId" value="<?php echo $attachment->id ?>" />
                                        <?php nonce::field( 'update_sidebar_email' ); ?>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </p>
                                </form>
                            <?php elseif ( $attachment->key == 'room-planner' ): ?>
                                <h3>Room Planner</h3>
                            <?php elseif ( $attachment->key == 'search' ): ?>
                                <h3>Search</h3>
                            <?php elseif ( $attachment->key == 'sidebar-image' ): ?>
                                <h3>Image</h3>
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
                            <?php elseif ( $attachment->key == 'video' ): ?>
                                <h3>Video</h3>
                                <?php if ( $attachment->value ): ?>
                                    <video id="sidebar-video" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" data-setup='{"example_option":true}' width="350px" height="190px">
                                        <source src="<?php echo $attachment->value ?>" type="video/mp4" />
                                    </video>
                                <?php else: ?>
                                    <p>No video uploaded yet</p>
                                <?php endif; ?>
                                <p>
                                    <button type="button" id="video-upload" class="btn btn-primary">Upload</button>
                                    <div class="progress progress-sm hidden" id="video-upload-loader">
                                        <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                    <!-- Where the uploader lives -->
                                    <div id="video-uploader"></div>
                                </p>
                            <?php endif; ?>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>
        </section>
    </div>
</div>

<div id="sidebar-image-template" class="sidebar-element sidebar-image hidden">
    <div class="sidebar-actions">
        <input type="checkbox" value="active" checked/>
        <a href="javascript:;" class="remove" title="Delete this Element"><i class="fa fa-trash-o"></i></a>
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
