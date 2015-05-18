<?php
$upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );
?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Settings
            </header>
            <div class="panel-body">

                <form method="post" role="form" action="">

                    <div id="popup-editor">
                        <h3>Email Capture Popup</h3>

                        <div id="popup-image">
                            <a href="javascript:;"
                                    data-media-manager
                                    data-upload-url="<?php echo $upload_url ?>"
                                    data-search-url="<?php echo $search_url ?>"
                                    data-delete-url="<?php echo $delete_url ?>"
                                    data-image-target="#popup-image">
                                <img class="img-responsive" src="<?php echo $settings['remarketing-popup-image'] ? $settings['remarketing-popup-image'] : '/images/email-capture-banner.png' ?>" />
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
                            <div class="col-lg-6">
                                <select class="form-control" name="idle-seconds">
                                    <option value="-1">Disable Email Capture Popup</option>
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
                                <img class="img-responsive" src="<?php echo $settings['remarketing-coupon'] ? $settings['remarketing-coupon'] : '//placehold.it/700x200/eee/a1a1a1&text=+' ?>" />
                                <input type="hidden" name="coupon-path" value="<?php echo $settings['remarketing-coupon'] ?>" />
                                <span class="upload-tooltip">700x200px <i class="fa fa-upload"></i></span>
                            </a>
                            <a href="javascript:;" id="delete-coupon"><i class="fa fa-trash-o"></i></a>
                        </div>
                    </div>

                    <?php for($email_number=1; $email_number<=3; $email_number++): $email_number_text = str_replace([1, 2, 3], ['First', 'Second', 'Third'], $email_number ); ?>
                        <div class="email-settings">
                            <h3><?php echo $email_number_text ?> Email</h3>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="email<?php echo $email_number?>-enabled" value="1" <?php echo $settings["remarketing-email{$email_number}-enabled"] ? 'checked' : '' ?> />
                                    Enable this email
                                </label>

                            </div>

                            <div class="form-group">
                                <select class="form-control" name="email<?php echo $email_number?>-delay">
                                    <?php for($i=60; $i<=1800; $i+=60): ?>
                                        <option value="<?php echo $i ?>" <?php if ($settings["remarketing-email{$email_number}-delay"] == $i) echo 'selected' ?>>Send <?php echo $email_number_text ?> email after <?php echo $i / 3600 ?> hour(s) abandoned.</option>
                                    <?php endfor; ?>
                                    <?php for($i=3600; $i<=3600*24; $i+=3600): ?>
                                        <option value="<?php echo $i ?>" <?php if ($settings["remarketing-email{$email_number}-delay"] == $i) echo 'selected' ?>>Send <?php echo $email_number_text ?> email after <?php echo $i / 3600 ?> hour(s) abandoned.</option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div id="email<?php echo $email_number?>-header" class="email-header">
                                <a href="javascript:;"
                                   data-media-manager
                                   data-upload-url="<?php echo $upload_url ?>"
                                   data-search-url="<?php echo $search_url ?>"
                                   data-delete-url="<?php echo $delete_url ?>"
                                   data-image-target="#email<?php echo $email_number?>-header">
                                    <img class="img-responsive" src="<?php echo $settings["remarketing-email{$email_number}-header"] ? $settings["remarketing-email{$email_number}-header"] : "//placehold.it/700x200/eee/a1a1a1&text=+" ?>" />
                                    <input type="hidden" name="email<?php echo $email_number?>-header" value="<?php echo $settings["remarketing-email{$email_number}-header"] ?>" />
                                    <span class="upload-tooltip">700x200px <i class="fa fa-upload"></i></span>
                                </a>
                            </div>

                            <div class="form-group">
                                <input class="form-control" name="email<?php echo $email_number?>-title" value="<?php echo $settings["remarketing-email{$email_number}-title"] ?>" placeholder="Email Title"/>
                            </div>

                            <div class="form-group">
                                <textarea class="form-control" rows="3" name="email<?php echo $email_number?>-body" placeholder="Email Body"><?php echo $settings["remarketing-email{$email_number}-body"] ?></textarea>
                            </div>
                        </div>
                    <?php endfor; ?>

                    <p class="text-right">
                        <?php nonce::field('settings') ?>
                        <button class="btn btn-primary" type="submit">Save</button>
                    </p>

                </form>

            </div>
        </section>
    </div>
</div>