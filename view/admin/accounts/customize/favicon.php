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
                <ul class="nav nav-tabs tab-bg-dark-navy-blue" role="tablist">
                    <li><a href="/accounts/edit/?aid=<?php echo $account->id ?>">Account</a></li>
                    <li><a href="/accounts/website-settings/?aid=<?php echo $account->id ?>">Website</a></li>
                    <li><a href="/accounts/other-settings/?aid=<?php echo $account->id ?>">Other</a></li>
                    <li><a href="/accounts/actions/?aid=<?php echo $account->id ?>">Actions</a></li>

                    <?php if ( $user->has_permission( User::ROLE_SUPER_ADMIN ) ): ?>
                        <li><a href="/accounts/dns/?aid=<?php echo $account->id ?>">DNS</a></li>
                    <?php endif; ?>

                    <li><a href="/accounts/notes/?aid=<?php echo $account->id ?>">Notes</a></li>
                    <li class="dropdown active">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">Customize <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="/accounts/customize/settings/?aid=<?php echo $account->id ?>">Settings</a></li>
                            <li><a href="/accounts/customize/stylesheet/?aid=<?php echo $account->id ?>">LESS/CSS</a></li>
                            <li><a href="/accounts/customize/favicon/?aid=<?php echo $account->id ?>">Favicon</a></li>
<!--                            <li><a href="/accounts/customize/ashley-express-shipping-prices/?aid=--><?php //echo $account->id ?><!--">Ashley Express - Shipping Prices</a></li>-->
                        </ul>
                    </li>
                </ul>
                <h3>Favicon: <?php echo $account->title ?></h3>
            </header>

            <div class="panel-body">

                <form name="fTop" action="/accounts/customize/favicon/" method="post" role="form">
                    <p>
                        <?php if ( empty($favicon) ):  ?>
                            No Favicon Yet.
                        <?php else: ?>
                            Favicon: <img src="<?php echo $favicon; ?>" alt="<?php echo _('Favicon'); ?>" />
                        <?php endif; ?>
                    </p>

                    <p>
                        <a href="#" id="aUploadFavicon" class="btn btn-primary btn-lg">Upload</a>
                        <div id="upload-favicon"></div>
                        <input type="hidden" value="<?php echo $_GET["aid"] ?>" name="aid" id="aid" />
                        <?php nonce::field('upload_favicon', '_upload_favicon'); ?>
                        <?php nonce::field('favicon'); ?>
                    </p>
                </form>

            </div>
        </section>
    </div>
</div>

