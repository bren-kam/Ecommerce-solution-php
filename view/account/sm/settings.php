<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <?php echo $website_sm_account->title ?> | Settings | Social Media

            </header>

            <div class="panel-body">

                <form method="post" role="form">

                    <?php if ( isset( $fb_pages ) ): ?>

                        <p><strong>Post As:</strong></p>
                        <div class="radio">
                            <label>
                                <input type="radio" name="fb_post_as" value="<?php echo $website_sm_account->auth_information_array['me']['id'] ?>" <?php if ( $website_sm_account->sm_reference_id == $website_sm_account->auth_information_array['me']['id'] ) echo 'checked' ?>/>
                                <?php echo $website_sm_account->auth_information_array['me']['name'] ?> (Me)
                            </label>
                        </div>

                        <?php foreach ( $fb_pages as $fb_page ): ?>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="fb_post_as" value="<?php echo $fb_page['id'] ?>" <?php if ( $website_sm_account->sm_reference_id == $fb_page['id'] ) echo 'checked' ?> />
                                    <?php echo $fb_page['name'] ?>
                                </label>
                            </div>
                        <?php endforeach; ?>

                    <?php endif; ?>

                    <?php if ( isset( $fs_venue_id ) ): ?>
                        <div class="form-group">
                            <label for="fs-venue-id">Foursquare Venue ID (post will be written as tips under this Venue ID)</label>
                            <input type="text" class="form-control" id="fs-venue-id" name="fs_venue_id" value="<?php echo $fs_venue_id ?>" />
                        </div>
                    <?php endif; ?>


                    <p>
                        <?php nonce::field( 'settings' ) ?>
                        <button class="btn btn-primary" type="submit">Save</button>
                    </p>

                </form>

            </div>
        </section>
    </div>
</div>