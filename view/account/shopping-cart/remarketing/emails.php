<?php
$upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );
?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Abandoned Cart Emails
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

                    <?php for($email_number=1; $email_number<=3; $email_number++): $email_number_text = str_replace([1, 2, 3], ['First', 'Second', 'Third'], $email_number ); ?>
                        <div class="email-settings">
                            <h3>
                                <?php echo $email_number_text ?> Email
                                <a class="popover-container" href="javascript:;" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="right" data-title="What is this." data-content="Here you can design the email that gets sent to users who abandoned their carts.">
                                    <span class="glyphicon glyphicon-question-sign"></span>
                                </a>
                            </h3>

                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="email<?php echo $email_number?>-enabled" value="1" <?php echo $settings["remarketing-email{$email_number}-enabled"] ? 'checked' : '' ?> />
                                    Enable this email
                                </label>

                            </div>

                            <div class="form-group">
                                <select class="form-control" name="email<?php echo $email_number?>-delay">
                                    <?php for($i=60; $i<=1800; $i+=60): ?>
                                        <option value="<?php echo $i ?>" <?php if ($settings["remarketing-email{$email_number}-delay"] == $i) echo 'selected' ?>>Send <?php echo $email_number_text ?> email after <?php echo $i / 60 ?> hour(s) abandoned.</option>
                                    <?php endfor; ?>
                                    <?php for($i=3600; $i<=3600*24; $i+=3600): ?>
                                        <option value="<?php echo $i ?>" <?php if ($settings["remarketing-email{$email_number}-delay"] == $i) echo 'selected' ?>>Send <?php echo $email_number_text ?> email after <?php echo $i / 3600 ?> hour(s) abandoned.</option>
                                    <?php endfor; ?>
                                    <?php for($i=3600*48; $i<=3600*96; $i+=3600*24): ?>
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
                                    <img class="img-responsive" src="<?php echo $settings["remarketing-email{$email_number}-header"] ? $settings["remarketing-email{$email_number}-header"] : "/images/remarketing-default-email.jpg" ?>" />
                                    <input type="hidden" name="email<?php echo $email_number?>-header" value="<?php echo $settings["remarketing-email{$email_number}-header"] ?>" />
                                    <span class="upload-tooltip">640x180px <i class="fa fa-upload"></i></span>
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
                        <?php nonce::field('emails') ?>
                        <button class="btn btn-primary" type="submit">Save</button>
                    </p>

                </form>

            </div>
        </section>
    </div>
</div>
