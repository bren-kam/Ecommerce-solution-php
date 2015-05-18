<?php
/**
 * @package Grey Suit Retail
 * @page Favicon | Customize | Account
 *

 */
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                Favicon
            </header>

            <div class="panel-body">

                <form name="fTop" action="/website/favicon/" method="post" role="form">
                    <p>
                        <?php if ( empty($favicon) ):  ?>
                            No Favicon Yet.
                        <?php else: ?>
                            Favicon: <img src="<?php echo $favicon; ?>" alt="<?php echo _('Favicon'); ?>" />
                        <?php endif; ?>
                    </p>

                    <p>
                        <a href="#" id="aUploadFavicon" class="btn btn-primary btn-lg">Upload</a>
                        <div id="uploader"></div>
                        <?php nonce::field('upload_favicon', '_upload_favicon'); ?>
                        <?php nonce::field('favicon'); ?>
                    </p>
                </form>

            </div>
        </section>
    </div>
</div>

