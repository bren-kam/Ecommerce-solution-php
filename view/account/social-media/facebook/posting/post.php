<?php
/**
 * @package Grey Suit Retail
 * @page Post | Posting | Facebook | Social Media
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var SocialMediaFacebookPage $page
 * @var SocialMediaPosting $sm_posting
 * @var SocialMediaPostingPost $post
 * @var array $pages
 * @var DateTime $new_date_posted
 * @var DateTime $now
 * @var string $errs
 * @var string $js_validation
 */


$upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );

$new_date_posted = new DateTime( $post->date_posted );
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                Posting - <?php echo $page->name ?>
                <div class="pull-right">
                    <a class="btn btn-default btn-sm" href="/social-media/facebook/posting/?smfbpid=<?php echo $page->id ?>">List Posts</a>
                </div>
            </header>
            <div class="panel-body">
                <?php
                if ( !$post->fb_page_id ) :
                    // Define instructions
                    $instructions = array(
                        1 => array(
                            'title' => _('Go to the Posting application')
                           , 'text' => _('Go to the') . ' <a href="http://apps.facebook.com/op-posting/" title="' . _('Online Platform - Posting') . '" target="_blank">' . _('Posting') . '</a> ' . _('application page') . '.'
                           , 'image' => false
                        )
                        , 2 => array(
                            'title' => _('Install The App')
                          , 'text' => _('Enter your Facebook Connection Key into the slot labeled Facebook Connection Key and click connect. Note, be sure the page you want to connect to is selected where it says Facebook Page: ') . $sm_posting->key
                        )
                    );

                    foreach ( $instructions as $step => $data ):
                        echo "<h3>Step $step: {$data['title']}</h3>";

                        if ( isset( $data['text'] ) )
                            echo '<p>', $data['text'], '</p>';

                        if ( !isset( $data['image'] ) || $data['image'] != false )
                            echo '<p><a href="http://account.imagineretailer.com/images/social-media/facebook/posting/', $step, '.png"><img src="http://account.imagineretailer.com/images/social-media/facebook/sweepstakes/', $step, '.png" alt="', $data['title'], '" /></a></p>';

                        echo '<hr />';
                    endforeach;
                else:
                    ?>

                    <?php if ( is_array( $pages ) ) : ?>

                        <form method="post" role="form">

                            <p>
                                <label>Page:</label>
                                <?php echo $pages[$sm_posting->fb_page_id]['name']; ?>
                            </p>

                            <div class="form-group">
                                <label for="taPost">Post:</label>
                                <textarea class="form-control" id="taPost" name="taPost" rows="10"></textarea>
                            </div>

                            <div class="row">
                                <div class="col-lg-4">
                                    <label for="tDate">Send Date:</label>
                                    <input type="text" class="form-control" name="tDate" id="tDate" value="<?php echo ( !empty( $_POST['tDate'] ) && !$post->id ) ? $new_date_posted->format('m/d/Y') : $now->format('m/d/Y'); ?>" />
                                </div>
                                <div class="col-lg-2">
                                    <label for="tTime">At</label>
                                    <input type="text" class="form-control" name="tTime" id="tTime" value="<?php echo ( empty( $_POST['tTime'] ) && !$post->id ) ? $new_date_posted->format('h:i a') : $now->format('h:i a'); ?>" />
                                </div>
                            </div>

                            <p>
                                <?php nonce::field('post') ?>
                                <button type="submit" class="btn btn-primary">Post in Facebook</button>
                            </p>
                        </form>
                        <?php echo $js_validation; ?>

                    <?php else: ?>
                        <p>In order to post to one of your Facebook pages you will need to connect them first.</p>
                        <p><strong>Connection key</strong> <?php echo $sm_posting->key; ?></p>
                        <p><a href="http://apps.facebook.com/op-posting/" title="Online Platform - Posting" target="_blank">Connect your Facebook pages here.</a></p>
                    <?php endif; ?>

                <?php endif; ?>
            </div>
        </section>
    </div>
</div>