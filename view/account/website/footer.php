<?php
$upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );
?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                Website Footer
            </header>
            <div class="panel-body">
                <form method="post" role="form">
                    <div class="form-group">
                        <label for="text">Website Footer:</label>
                        <textarea class="form-control" id="text" name="footer" rte="1" rows="10"><?php echo $footer ?></textarea>
                    </div>
                    <p>
                        <button type="button" class="btn btn-xs btn-default" title="Open Media Manager" data-media-manager data-upload-url="<?php echo $upload_url ?>" data-search-url="<?php echo $search_url ?>" data-delete-url="<?php echo $delete_url ?>">Upload File</button>
                    </p>

                    <p>
                        <?php nonce::field('footer') ?>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </p>
                </form>
            </div>
        </section>
    </div>
</div>