<?php nonce::field( 'upload_image', '_upload_image' ); ?>

<section class="panel" id="financing-settings">
    <header class="panel-heading">
        Financing Settings
    </header>

    <div class="panel-body">
        <p>
            Place "[apply-now]" into the page content above to place the location of your image. When you view your website, this will be replaced with the image uploaded.
        </p>

        <div>
            <label>Apply Now Button:</label>
            <p id="apply-now-button">
                <?php if ( !empty( $apply_now ) ): ?>
                    <img src="<?php echo $apply_now->value ; ?>" alt="Apply Now" />
                <?php else: ?>
                    No Button Yet!
                <?php endif; ?>
            </p>

            <button type="button" class="btn btn-default" id="upload">Upload Button</button>
            <div class="progress progress-sm hidden" id="upload-loader">
                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <!-- Where the uploader lives -->
            <div id="uploader"></div>
        </div>
        <div class="form-group">
            <label for="tApplyNowLink">Apply now Link:</label>
            <input type="text" class="form-control" name="tApplyNowLink" id="tApplyNowLink" value="<?php echo $apply_now_link ?>" />
        </div>
    </div>
</section>
