<?php
$upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );
?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                Website Header
            </header>
            <div class="panel-body">
                <p>If you add any content to the text box, your phone number that is displayed in the top right will disappear, and you'll have to re-add it with any customizations made to your header</p>
                <form method="post" role="form">
                    <div class="form-group">
                        <label for="text">Website Header:</label>
                        <textarea class="form-control" id="text" name="header" rte="1" rows="10"><?php echo $header ?></textarea>
                    </div>
                    <p>
                        <button type="button" class="btn btn-xs btn-default" title="Open Media Manager" data-media-manager data-upload-url="<?php echo $upload_url ?>" data-search-url="<?php echo $search_url ?>" data-delete-url="<?php echo $delete_url ?>">Upload File</button>
                    </p>

                    <p>
                        <?php nonce::field('header') ?>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </p>
                </form>
            </div>
        </section>
    </div>
</div>