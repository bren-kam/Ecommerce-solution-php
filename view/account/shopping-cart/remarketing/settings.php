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
                        <div id="popup-image">
                            <a href="javascript:;"
                                    data-media-manager
                                    data-upload-url="<?php echo $upload_url ?>"
                                    data-search-url="<?php echo $search_url ?>"
                                    data-delete-url="<?php echo $delete_url ?>"
                                    data-image-target="#popup-image">
                                <img class="img-responsive" src="<?php echo $settings['remarketing-popup-image'] ? $settings['remarketing-popup-image'] : '//placehold.it/700x200/eee/a1a1a1&text=700+x+200+px+image' ?>" />
                                <input type="hidden" name="popup-image" value="<?php echo $settings['remarketing-popup-image'] ? $settings['remarketing-popup-image'] : '' ?>" />
                            </a>
                        </div>
                        <div id="popup-body">
                            <textarea class="form-control" id="popup-title" name="title" placeholder="Your Title Goes Here..." rows="2"><?php echo $settings['remarketing-title'] ?></textarea>
                            <textarea class="form-control" id="popup-text" name="intro-text" placeholder="Your Text Goes Here..." rows="6"><?php echo $settings['remarketing-intro-text'] ?></textarea>
                        </div>
                        <div id="popup-form">
                            <div id="popup-fields">
                                <div class="popup-field"></div>
                                <div class="popup-field"></div>
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
                                    <?php for($i=300; $i<=3600; $i+=300): ?>
                                        <option value="<?php echo $i ?>" <?php if ($settings['remarketing-idle-seconds'] == $i) echo 'selected' ?>>Popup after <?php echo $i / 60?> minutes</option>
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
                                <img class="img-responsive" src="<?php echo $settings['remarketing-coupon'] ? $settings['remarketing-coupon'] : '//placehold.it/700x200/eee/a1a1a1&text=upload+coupon' ?>" />
                                <input type="hidden" name="coupon-path" value="<?php echo $settings['remarketing-coupon'] ?>" />
                            </a>
                            <a href="javascript:;" id="delete-coupon"><i class="fa fa-trash-o"></i></a>
                        </div>

                        <p class="text-right">
                            <?php nonce::field('settings')?>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </p>
                    </div>
                </form>


            </div>
        </section>
    </div>
</div>