<?php
/**
 * @package Grey Suit Retail
 * @page About Us | Facebook | Social Media
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var SocialMediaFacebookPage $page
 * @var SocialMediaAboutUs $about_us
 */

$upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                About Us - <?php echo $page->name ?>
            </header>
            <div class="panel-body">
                <?php
                    if ( !$about_us->fb_page_id ) :
                        // Define instructions
                        $instructions = array(
                            1 => array(
                                'title' => _('Go to the About Us application')
                                , 'text' => _('Go to the') . ' <a href="http://apps.facebook.com/op-about-us/" title="' . _('Online Platform - About Us') . '" target="_blank">' . _('About Us') . '</a> ' . _('application page') . '.'
                                , 'image' => false
                            )
                        , 2 => array(
                                'title' => _('Install The App')
                                , 'text' => _('Click') . ' <strong>' . _('Install This App.') . '</strong> ' . _('on the page shown below:')
                            )
                        , 3 => array(
                                'title' => _('Choose Your Page')
                                , 'text' => _('(Note - You must first be an admin of the page to install the App)')
                            )
                        , 4 => array(
                                'title' => _('Click Add Online Platform - About Us')
                            )
                        , 5 => array(
                                'title' => _('Click on the About Us App')
                                , 'text' => _("Scroll down below the banner, and you'll see your apps (you may need to click on the arrow on the right-hand side to find the app you're looking for) and click on the About Us")
                            )
                        , 6 => array(
                                'title' => _('Click on the Update Settings')
                            )
                        , 7 => array(
                                'title' => _('Click Add Online Platform - About Us')
                                , 'text' => _('Copy and paste the connection code into the Facebook Connection Key box shown below (when done it will say Connected): ') . $about_us->key
                            )
                        );

                        foreach ( $instructions as $step => $data ):
                            echo "<h3>Step $step: {$data['title']}</h3>";

                            if ( isset( $data['text'] ) )
                                echo '<p>', $data['text'], '</p>';

                            if ( !isset( $data['image'] ) || $data['image'] != false )
                                echo '<p><a href="http://account.imagineretailer.com/images/social-media/facebook/about-us/', $step, '.png"><img src="http://account.imagineretailer.com/images/social-media/facebook/about-us/', $step, '.png" alt="', $data['title'], '" /></a></p>';

                            echo '<hr />';
                        endforeach;
                    else:
                ?>
                        <p class="text-right"><a href="http://www.facebook.com/pages/ABC-Company/<?php echo $about_us->fb_page_id; ?>?sk=app_233746136649331" title="<?php echo _('View Facebook Page'); ?>" target="_blank"><?php echo _('View Facebook Page'); ?></a></p>

                        <form method="post" role="form">
                            <div class="form-group">
                                <label for="taContent">Text:</label>
                                <textarea class="form-control" id="taContent" name="taContent" rte="1" rows="10"><?php echo $about_us->content ?></textarea>
                            </div>
                            <p>
                                <button type="button" class="btn btn-xs btn-default" title="Open Media Manager" data-media-manager data-upload-url="<?php echo $upload_url ?>" data-search-url="<?php echo $search_url ?>" data-delete-url="<?php echo $delete_url ?>">Upload File</button>
                            </p>
                            <p>
                                <?php nonce::field('about-us') ?>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </p>
                        </form>

                <?php endif; ?>
            </div>
        </section>
    </div>
</div>