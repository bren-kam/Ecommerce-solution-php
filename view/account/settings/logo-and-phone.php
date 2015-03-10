<?php
/**
 * Logo and phone
 *
 * @var array $settings
 */
if ( $settings['website-logo'] ) {
    $logo = $settings['website-logo'];
} else {
    $logo = $user->account->logo ? $user->account->logo : '//placehold.it/200x200&text=No Logo';
}

nonce::field( 'upload_logo', '_upload_logo' );
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Logo &amp; Phone
            </header>
            <div class="panel-body">
                <form method="post" role="form">
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="tel" class="form-control" name="tPhone" id="phone" value="<?php echo $settings['phone'] ?>" />
                    </div>
                    <div class="form-group">
                        <label for="logo">Logo:</label>
                        <p>
                            <img src="<?php echo $logo; ?>" id="logo" />
                            <button type="button" class="btn btn-default btn-sm" id="upload">Upload Logo</button>
                            <div class="progress progress-sm hidden" id="upload-loader">
                                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                            <!-- Where the uploader lives -->
                            <div id="uploader"></div>
                        </p>
                    </div>
                    <div class="form-group">
                        <label for="logo_title">Logo Title:</label>
                        <input type="text" class="form-control" name="tLogoTitle" id="logo_title" value="<?php echo $settings['logo-title'] ?>" />
                    </div>
                    <div class="form-group">
                        <label for="logo_alt">Logo Alternate Text:</label>
                        <input type="text" class="form-control" name="tLogoAlt" id="logo_alt" value="<?php echo $settings['logo-alt'] ?>" />
                    </div>
                    <div class="form-group">
                        <label for="logo_link">Logo Link:</label>
                        <input type="text" class="form-control" name="tLogoLink" id="logo_link" value="<?php echo $settings['logo-link'] ?>" />
                    </div>
                    <p>
                        <?php nonce::field( 'logo_and_phone' ); ?>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </p>
                </form>
            </div>
        </section>
    </div>
</div>