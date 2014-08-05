<?php
$upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );
?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                Custom 404 Page
                <a href="/website/html-head/" class="pull-right btn btn-default btn-sm">Edit HTML &lt;head&gt;</a>
                <a href="/website/settings/" class="pull-right btn btn-default btn-sm">Edit Settings</a>
            </header>
            <div class="panel-body">
                <p>Set this text to customize the message shown when a visitor enters a non existing Page.</p>

                <form method="post" role="form">
                    <div class="form-group">
                        <label for="text">HTML Code:</label>
                        <textarea class="form-control" id="text" name="text-404" rte="1" rows="10"><?php echo $text_404 ?></textarea>
                    </div>
                    <p>
                        <button type="button" class="btn btn-xs btn-default" title="Open Media Manager" data-media-manager data-upload-url="<?php echo $upload_url ?>" data-search-url="<?php echo $search_url ?>" data-delete-url="<?php echo $delete_url ?>">Upload File</button>
                    </p>

                    <p>
                        <?php nonce::field('custom_404') ?>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </p>
                </form>
            </div>
        </section>
    </div>
</div>