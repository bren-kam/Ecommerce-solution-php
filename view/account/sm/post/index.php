<?php

$upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );

$delete_nonce = nonce::create( 'delete' );
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
			<div class="" id="character-count">Characters:<span></span></div>
                    </div>

                    <div class="row">
                        <div class="col-lg-9" id="post-actions">
                            <a href="javascript:;"  title="Open Media Manager"
                                    data-media-manager
                                    data-submit-text="Use Image"
                                    data-upload-url="<?php echo $upload_url ?>"
                                    data-search-url="<?php echo $search_url ?>"
                                    data-delete-url="<?php echo $delete_url ?>"
                                    data-image-target="#photo">
                                <i class="fa fa-picture-o"></i>
                            </a>

                            <a href="javascript:;" id="show-link-container"><i class="fa fa-link"></i></a>
                            <input type="text" class="form-control" id="link" name="link" placeholder="Link"/>
                        </div>
                        <div class="col-lg-3 text-right">
                            <select class="form-control" id="post-at-toggle">
                                <option value="now">Post Now</option>
                                <option value="later">Post Later</option>
                            </select>
                        </div>
                    </div>

                    <div class="clearfix">
                        <div class="image-selector pull-left" id="photo">
                            <input type="hidden" name="photo" value="" />
                            <img src="//placehold.it/150x150&amp;text=No+Image" class="hidden"/>
                            <a id="remove-image" href="javascript:;" class="hidden"><i class="fa fa-trash-o"> </i></a>
                        </div>
                    </div>


                    <div id="post-at-container" class="hidden">
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
                    </div>

                    <p class="panel-heading" id="post-to-title">Post To Account(s):</p>
                    <div class="row" id="post-to-container">
                        <div class="col-lg-4">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="post-to-all" />
                                    All Accounts
                                </label>
                            </div>
                        </div>
                        <?php foreach ( $website_sm_accounts as $website_sm_account ): ?>
                            <div class="col-lg-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="website_sm_accounts[<?php echo $website_sm_account->id ?>]" value="<?php echo $website_sm_account->id ?>">
                                        <i class="fa fa-<?php echo $website_sm_account->sm ?>"></i> <?php echo $website_sm_account->title ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
                    Manage Accounts:
                </header>

                <div class="panel-body">
                    <?php foreach ( $website_sm_accounts as $website_sm_account ): ?>
                        <div class="checkbox">
                            <label>
                                <i class="fa fa-<?php echo $website_sm_account->sm ?>"></i> <?php echo $website_sm_account->title ?>
                                <a href="/sm/delete/?id=<?php echo $website_sm_account->id ?>. '&amp;_nonce=<?php echo $delete_nonce ?>" confirm="Do you want to remove this Social Media Account? Cannot be undone">Delete</a>
                                <?php if ( $website_sm_account->sm == 'facebook' ): ?>
                                    | <a href="/sm/settings/?id=<?php echo $website_sm_account->id ?>">Settings</a>
                                <?php endif; ?>
                            </label>
                        </div>
                    <?php endforeach; ?>

                    <p>
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" id="ddAddSMAccount" data-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-plus"></i>
                                Add Social Media Account
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="ddAddSMAccount">
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="/sm/facebook-connect/?website-id=<?php echo $user->account->id ?>&amp;user-id=<?php echo $user->id ?>"><i class="fa fa-facebook"></i> Facebook</a></li>
                                <li role="presentation"><a role="menuitem" tabindex="-1" href="/sm/twitter-connect/?website-id=<?php echo $user->account->id ?>&amp;user-id=<?php echo $user->id ?>"><i class="fa fa-twitter"></i> Twitter</a></li>
                            </ul>
                        </div>
                    </p>

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
