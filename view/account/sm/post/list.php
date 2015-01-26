<?php
    $remove_nonce = nonce::create( 'remove' );
?>

<?php foreach( $website_sm_posts as $website_sm_post ): ?>
    <div class="row-fluid" data-post-id="<?php echo $website_sm_post->id ?>">
        <div class="col-lg-12">
            <section class="panel">
                <div class="panel-body">

                    <div class="fb-user-thumb">
                        <i class="fa fa-<?php echo $website_sm_post->sm ?>"></i>
                    </div>
                    <div class="fb-user-details">
                        <h3><?php echo $website_sm_accounts[$website_sm_post->website_sm_account_id]->title ?></h3>
                        <p>
                            <span class="post-at">
                                <?php echo ( new DateTime( $website_sm_post->post_at ) )->format('l jS F, h:i:s A') ?>
                                <?php if ( isset( $timezones[$website_sm_post->timezone] ) ) echo $timezones[$website_sm_post->timezone] ; ?>
                            </span>
                            <?php if ( !$website_sm_post->posted ): ?>
                                <span class="label label-warning">scheduled</span>
                                <a href="javascript:;" class="edit" title="Re Schedule"><i class="fa fa-clock-o"></i></a>
                                <a href="/sm/post/remove/?id=<?php echo $website_sm_post->id ?>&amp;_nonce=<?php echo $remove_nonce ?>" class="remove" title="Delete this post"><i class="fa fa-trash-o"></i></a>
                            <?php elseif ( $website_sm_post->posted == 1 ): ?>
                                <span class="label label-success">posted</span>
                            <?php elseif ( $website_sm_post->posted == 2 ): ?>
                                <span class="label label-danger" title="Message from SM: <?php echo $website_sm_post->sm_message ?>">failed to post</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="clearfix">
                        <div class="row-fluid">
                            <div class="col-sm-6 col-md-8">

                                <?php if ( $website_sm_post->link ): ?>
                                    <a href="<?php echo $website_sm_post->link ?>" target="_blank"><?php echo $website_sm_post->content ?></a>
                                <?php else: ?>
                                    <?php echo $website_sm_post->content ?>
                                <?php endif; ?>

                                <?php if ( $website_sm_post->photo ): ?>
                                    <a href="javascript:;" class="show-details">Show Posted Images</a>
                                <?php endif; ?>

                            </div>
                            <div class="col-sm-3 col-md-2 col-lg-2 hidden">
                                <?php if ( $website_sm_post->photo ): ?>
                                    <img class="img-responsive" src="<?php echo $website_sm_post->photo ?>" />
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
        </div>
    </div>
<?php endforeach; ?>