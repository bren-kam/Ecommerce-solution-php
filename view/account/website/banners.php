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
<input type="hidden" id="page-id" value="<?php echo $page->id ?>" />

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Banners
                <a href="javascript:;" class="btn btn-primary btn-sm pull-right" data-media-manager title="Open Media Manager" data-media-manager data-upload-url="<?php echo $upload_url ?>" data-search-url="<?php echo $search_url ?>" data-delete-url="<?php echo $delete_url ?>" data-submit-text="Use Image as Banner"><i class="fa fa-plus"></i> Upload or Select an Image</a>
            </header>

            <div class="panel-body">

                <p><small>(<?php echo ($slideshow_fixed_width ? 'Max. image' : 'Suggested') . ' size: ' . $dimensions?>)</small></p>

                <div id="banner-list">

                    <div class="progress progress-sm hidden" id="new-element-loader">
                        <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>

                    <?php
                        foreach ( $attachments as $attachment ):

                        if ( $user->account->is_new_template() ):
                            $extra = json_decode($attachment->extra, true);
                            if ( $extra ) {
                                $scheduled = isset( $extra['date-range'] ) && $extra['date-range'];
                                $running = $extra['date-end'] && ( date('Y-m-d') >= $extra['date-start'] && date('Y-m-d') <= $extra['date-end'] );
                                $over = $extra['date-end'] && ( date('Y-m-d') > $extra['date-end'] );
                            } else {
                                $scheduled = false;
                                $running = false;
                                $over = false;
                            }
                        endif;
                    ?>

                        <div class="banner <?php echo $attachment->status == '0' ? 'disabled' : '' ?>" data-attachment-id="<?php echo $attachment->id ?>">

                            <div class="banner-actions">
                                <small><?php echo $dimensions; ?></small>
                                <label for="status-on" class="banner-status-label <?php if ( $attachment->status == '1' && !$scheduled ) echo 'selected' ?>">
                                    <input type="radio" class="status" name="status-<?php echo $attachment->id ?>" value="on" id="status-on" <?php if ( $attachment->status == '1' && !$scheduled ) echo 'checked' ?> /> On
                                </label>
                                <?php if ( $user->account->is_new_template() ): ?>
                                    <label for="status-scheduled" class="banner-status-label <?php if ( $attachment->status == '1' && $scheduled ) echo ' selected'; if ( $running ) echo ' running' ; if ( $over ) echo ' over' ?>">
                                        <input type="radio" class="status" name="status-<?php echo $attachment->id ?>" value="scheduled" id="status-scheduled" <?php if ( $attachment->status == '1' && $scheduled ) echo 'checked' ?>/> Scheduled
                                    </label>
                                <?php endif; ?>
                                <label for="status-off" class="banner-status-label <?php if ( $attachment->status == '0' ) echo 'selected' ?>">
                                    <input type="radio" class="status" name="status-<?php echo $attachment->id ?>" value="off" id="status-off" <?php if ( $attachment->status == '0' ) echo 'checked' ?> /> Off
                                </label>
                                <a href="javascript:;" class="remove" title="Delete this Banner"><i class="fa fa-trash-o"></i></a>
                            </div>

                            <img src="<?php echo $attachment->value ?>" />

                            <?php if ( $user->account->is_new_template() ): ?>
                                <?php
                                    $extra = json_decode($attachment->extra, true);
                                    if ( $extra ):
                                        $image_link = $extra['link'];
                                        $date_range = isset( $extra['date-range'] ) && $extra['date-range'];
                                        $date_start = ( new DateTime( $extra['date-start'] ) )->format('m/d/Y');
                                        $date_end = ( new DateTime( $extra['date-end'] ) )->format('m/d/Y');
                                    else:
                                        $image_link = $attachment->extra;
                                        $date_range = null;
                                        $date_start = null;
                                        $date_end = null;
                                    endif;
                                ?>
                                <form action="/website/update-attachment-extra/" method="post" role="form" ajax="1">
                                    <div class="form-group">
                                        <label>Image Link:</label>
                                        <input type="text" class="form-control" name="extra[link]" value="<?php echo $image_link ?>" placeholder="Link URL" />
                                    </div>
                                    <input type="hidden" name="extra[date-range]" class="has-date-range" value="<?php echo $date_range ? 1 : 0 ?>" />
                                    <div class="input-daterange input-group <?php if ( !$date_range ) echo 'hidden' ?>">
                                        <input type="text" class="input-sm form-control" name="extra[date-start]" value="<?php echo $date_start ?>" placeholder="start date" />
                                        <span class="input-group-addon">to</span>
                                        <input type="text" class="input-sm form-control" name="extra[date-end]" value="<?php echo $date_end ?>" placeholder="end date"/>
                                    </div>
                                    <p>
                                        <br>
                                        <input type="hidden" name="hAccountPageAttachmentId" value="<?php echo $attachment->id ?>" />
                                        <?php echo $update_extra_nonce ?>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </p>
                                </form>
                            <?php else: ?>
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
                            <?php endif; ?>

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
        <label for="status-on" class="banner-status-label selected">
            <input type="radio" class="status" name="status" value="on" id="status-on" checked /> On
        </label>
        <?php if ( $user->account->is_new_template() ): ?>
            <label for="status-scheduled" class="banner-status-label">
                <input type="radio" class="status" name="status" value="scheduled" id="status-scheduled" /> Scheduled
            </label>
        <?php endif; ?>
        <label for="status-off" class="banner-status-label">
            <input type="radio" class="status" name="status" value="off" id="status-off" /> Off
        </label>
        <a href="javascript:;" class="remove" title="Delete this Banner"><i class="fa fa-trash-o"></i></a>
    </div>

    <h3>Image</h3>
    <img src="" />
    <?php if ( $user->account->is_new_template() ): ?>
        <form action="/website/update-attachment-extra/" method="post" role="form" ajax="1">
            <div class="form-group">
                <label>Image Link:</label>
                <input type="text" class="form-control" name="extra[link]" placeholder="Link URL" />
            </div>
            <div class="input-daterange input-group <?php if ( !$date_range ) echo 'hidden' ?>">
                <input type="text" class="input-sm form-control" name="extra[date-start]" placeholder="start date"/>
                <span class="input-group-addon">to</span>
                <input type="text" class="input-sm form-control" name="extra[date-end]" placeholder="end date"/>
            </div>
            <p>
                <input type="hidden" name="hAccountPageAttachmentId" />
                <?php echo $update_extra_nonce ?>
                <button type="submit" class="btn btn-primary">Save</button>
            </p>
        </form>
    <?php else: ?>
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
    <?php endif; ?>
</div>
