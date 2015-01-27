<?php

$upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );

?>

<form method="post" id="post-form" role="form">
    <div class="row-fluid">
        <div class="col-lg-8">
            <section class="panel">
                <header class="panel-heading">
                    Create Post
                </header>

                <div class="panel-body">


                    <div class="form-group">
                        <textarea class="form-control" rows="5" placeholder="Your Message" name="content"></textarea>
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control" name="link" placeholder="Link"/>
                    </div>

                    <p class="image-selector" id="photo">
                        <img src="//placehold.it/150x150&amp;text=No+Image" />
                        <br>
                        <input type="hidden" name="photo" value="" />
                        <button type="button" class="btn btn-xs btn-default" title="Open Media Manager"
                                data-media-manager
                                data-upload-url="<?php echo $upload_url ?>"
                                data-search-url="<?php echo $search_url ?>"
                                data-delete-url="<?php echo $delete_url ?>"
                                data-image-target="#photo">
                            Select a Photo
                        </button>
                    </p>

                    <div class="datetime-container">
                        <div class="form-group">
                            <input type="text" class="form-control" name="post-at[day]" id="post-at" placeholder="Post now or select date"/>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="post-at[time]" >
                                <?php foreach( $time_options as $k => $h ): ?>
                                    <option value="<?php echo $k ?>"><?php echo $h ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <select name="timezone" class="form-control">
                            <?php foreach ( $timezones as $tz_key => $tz_name ) : ?>
                                <option value="<?php echo $tz_key ?>" <?php if ( $settings['timezone'] == $tz_key ) echo 'selected' ?>><?php echo $tz_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <p>
                        <?php nonce::field( 'index' ) ?>
                        <button type="submit" class="btn btn-primary">Post</button>
                    </p>

                </div>
            </section>
        </div>

        <div class="col-lg-4">
            <section class="panel">
                <header class="panel-heading">
                    Post to Account(s):
                    <div class="pull-right"><a href="/sm/" class="btn btn-primary">Manage Accounts</a></div>
                </header>

                <div class="panel-body">
                    <?php foreach ( $website_sm_accounts as $website_sm_account ): ?>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="website_sm_accounts[<?php echo $website_sm_account->id ?>]" value="<?php echo $website_sm_account->id ?>">
                                <i class="fa fa-<?php echo $website_sm_account->sm ?>"></i> <?php echo $website_sm_account->title ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>

    </div>

</form>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Posts
                <select class="form-control" id="show-posted">
                    <option value="">Scheduled &amp; Posted</option>
                    <option value="1">Only Posted</option>
                    <option value="0">Only Scheduled</option>
                </select>
                <select class="form-control" id="show-account">
                    <option value="">All Social Media Accounts</option>
                    <?php foreach ( $website_sm_accounts as $website_sm_account ): ?>
                        <option value="<?php echo $website_sm_account->id ?>"><?php echo $website_sm_account->title ?></option>
                    <?php endforeach; ?>
                </select>
            </header>
        </section>
    </div>
</div>

<div id="post-list">
</div>

<div class="modal fade" id="edit-post-modal">
    <div class="modal-dialog">
        <form method="post" action="/sm/post/edit/" ajax="1" id='edit-post-form'>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit Post</h4>
                </div>
                <div class="modal-body">

                    <input type="hidden" name="id" id="edit-post-id" value="" />

                    <p><strong>Post at:</strong></p>
                    <div class="datetime-container">
                        <div class="form-group">
                            <input type="text" class="form-control" name="post-at[day]" id="edit-post-at-day" />
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="post-at[time]" id="edit-post-at-time" >
                                <?php foreach( $time_options as $k => $h ): ?>
                                    <option value="<?php echo $k ?>"><?php echo $h ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <?php nonce::field('edit'); ?>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>