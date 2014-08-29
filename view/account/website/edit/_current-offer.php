<?php nonce::field( 'upload_image', '_upload_image' ); ?>

<section class="panel" id="current-offer-settings">
    <header class="panel-heading">
        Current Offer Settings
    </header>

    <div class="panel-body">
        <div>
            <label>Current Coupon:</label>
            <p id="current-coupon">
                <?php if ( !empty( $coupon ) ): ?>
                    <img src="<?php echo ( stristr( $coupon->value, 'http' ) ) ? $coupon->value : 'http://' . $user->account->domain . $coupon->value; ?>" alt="Coupon" />
                <?php else: ?>
                    No Coupon Yet!
                <?php endif; ?>
            </p>

            <button type="button" class="btn btn-default" id="upload">Upload Coupon</button>
            <div class="progress progress-sm hidden" id="upload-loader">
                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
            <!-- Where the uploader lives -->
            <div id="uploader"></div>
        </div>
        <div class="form-group">
            <label for="tEmail">Notification email:</label>
            <input type="text" class="form-control" name="tEmail" id="tEmail" value="<?php echo $metadata['email'] ?>" placeholder="Email address" />
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="cbDisplayCoupon" value="yes" <?php if ( 'yes' == $metadata['display-coupon'] ) echo 'checked="checked"'; ?> />
                Display coupon on Current Offer page?
            </label>
        </div>
        <div class="checkbox">
            <label>
                <input type="checkbox" name="cbEmailCoupon" value="yes"<?php if ( 'no' != $metadata['email-coupon'] ) echo ' checked="checked"'; ?> />
                Email coupon on Current Offer page?
            </label>
        </div>
    </div>
</section>
