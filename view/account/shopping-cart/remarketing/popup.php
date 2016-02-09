<?php
$upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );
?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Email popup box & coupon
                <p class="pull-right">
                    <?php if ( $settings['remarketing-enabled'] ): ?>
                        <a class="btn btn-default" href="/shopping-cart/remarketing/disable/?_nonce=<?php echo nonce::create('disable') ?>">Remarketing is Enabled, disable it now.</a>
                    <?php else: ?>
                        <a class="btn btn-primary" href="/shopping-cart/remarketing/enable/?_nonce=<?php echo nonce::create('enable') ?>">Remarketing is Disabled, enable it now.</a>
                    <?php endif; ?>
                </p>
            </header>
            <div class="panel-body">

                <form method="post" role="form" action="">

                    <div id="popup-editor">
                        <h3>
                            Email Capture Popup

                            <a class="popover-container" href="javascript:;" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="right" data-title="What is this." data-content="Use this popup to capture email data and send reminders if they abandon the site.">
                                <span class="glyphicon glyphicon-question-sign"></span>
                            </a>
                        </h3>

                        <div id="popup-image">
                            <a href="javascript:;"
                                    data-media-manager
                                    data-upload-url="<?php echo $upload_url ?>"
                                    data-search-url="<?php echo $search_url ?>"
                                    data-delete-url="<?php echo $delete_url ?>"
                                    data-image-target="#popup-image">
                                <img class="img-responsive" src="<?php echo $settings['remarketing-popup-image'] ? $settings['remarketing-popup-image'] : '/images/remarketing-default-popup.jpg' ?>" />
                                <input type="hidden" name="popup-image" value="<?php echo $settings['remarketing-popup-image'] ? $settings['remarketing-popup-image'] : '' ?>" />
                                <span class="upload-tooltip">700x200px <i class="fa fa-upload"></i></span>
                            </a>
                        </div>
                        <div id="popup-body">
                            <textarea class="form-control" id="popup-title" name="title" placeholder="Your Title Goes Here..." rows="2"><?php echo $settings['remarketing-title'] ?></textarea>
                            <textarea class="form-control" id="popup-text" name="intro-text" placeholder="Your Text Goes Here..." rows="6"><?php echo $settings['remarketing-intro-text'] ?></textarea>
                        </div>
                        <div id="popup-form">
                            <div id="popup-fields">
                                <div class="popup-field-left">Name</div>
                                <div class="popup-field-right">Email</div>
                            </div>

                            <a id="submit-color" href="javascript:;" style="background-color: <?php echo $settings['remarketing-submit-color'] ?>;">
                                edit color
                                <span id="selected-color"><?php echo $settings['remarketing-submit-color'] ? $settings['remarketing-submit-color'] : '#72CEE0' ?></span>
                                <input type="hidden" name="submit-color" id="popup-submit-color" value="<?php echo $settings['remarketing-submit-color'] ?>">
                            </a>
                        </div>


                    </div>

                    <div id="settings-editor">
                        <div class="row">

                            <div class="form-group">
                                <label>Autoresponder </label>
                                <textarea class="form-control" rows="3" name="autoresponder" placeholder="Autoresponder"><?php echo $settings['remarketing-autoresponder']; ?></textarea>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <select class="form-control" name="idle-seconds">
                                    <?php for($i=60; $i<=1800; $i+=60): ?>
                                        <option value="<?php echo $i ?>" <?php if ($settings['remarketing-idle-seconds'] == $i) echo 'selected' ?>>Popup after <?php echo $i/60; ?> min</option>
                                    <?php endfor; ?>
                                    <?php for($i=3600; $i<=3600*24; $i+=3600): ?>
                                        <option value="<?php echo $i ?>" <?php if ($settings['remarketing-idle-seconds'] == $i) echo 'selected' ?>>Popup after <?php echo $i/3600 ?> hr</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" placeholder="Notification email" name="notification-email" value="<?php echo $settings['remarketing-notification-email'] ?>">
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div id="coupon-image">
                            <a href="javascript:;"
                               data-media-manager
                               data-upload-url="<?php echo $upload_url ?>"
                               data-search-url="<?php echo $search_url ?>"
                               data-delete-url="<?php echo $delete_url ?>"
                               data-image-target="#coupon-image">
                                <img class="img-responsive" src="<?php echo $settings['remarketing-coupon'] ? $settings['remarketing-coupon'] : '/images/remarketing-default-coupon.jpg' ?>" />
                                <input type="hidden" name="coupon-path" value="<?php echo $settings['remarketing-coupon'] ?>" />
                                <span class="upload-tooltip" style="right: 33%;">Upload Coupon <i class="fa fa-upload"></i></span>
                            </a>
                            <a href="javascript:;" id="delete-coupon"><i class="fa fa-trash-o"></i></a>
                        </div>
                    </div>

                    <p class="text-right">
                        <?php nonce::field('popup') ?>
                        <button class="btn btn-primary" type="submit">Save</button>
                    </p>

                </form>

            </div>
        </section>
    </div>
</div>
