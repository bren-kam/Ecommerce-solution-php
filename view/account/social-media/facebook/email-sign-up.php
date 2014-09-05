<?php
/**
 * @package Grey Suit Retail
 * @page Email Sign Up | Facebook | Social Media
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var SocialMediaFacebookPage $page
 * @var SocialMediaEmailSignUp $email_sign_up
 */

$upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                Email Sign Up - <?php echo $page->name ?>
            </header>
            <div class="panel-body">
                <?php
                    if ( !$email_sign_up->fb_page_id ) :
                        // Define instructions
                        $instructions = array(
                            1 => array(
                                'title' => _('Go to the Email Sign Up application')
                            , 'text' => _('Go to the') . ' <a href="http://apps.facebook.com/op-email-sign-up/" title="' . _('Online Platform - Email Sign Up') . '" target="_blank">' . _('Email Sign Up') . '</a> ' . _('application page') . '.'
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
                                'title' => _('Click Add Online Platform - Email Sign Up')
                            )
                        , 5 => array(
                                'title' => _('Click on the Email Sign Up App')
                            , 'text' => _("Scroll down below the banner, and you'll see your apps (you may need to click on the arrow on the right-hand side to find the app you're looking for) and click on the About Us")
                            )
                        , 6 => array(
                                'title' => _('Click on the Update Settings')
                            )
                        , 7 => array(
                                'title' => _('Click Add Online Platform - Email Sign Up')
                            , 'text' => _('Copy and paste the connection code into the Facebook Connection Key box shown below (when done it will say Connected): ') . $email_sign_up->key
                            )
                        );

                        foreach ( $instructions as $step => $data ):
                            echo "<h3>Step $step: {$data['title']}</h3>";

                            if ( isset( $data['text'] ) )
                                echo '<p>', $data['text'], '</p>';

                            if ( !isset( $data['image'] ) || $data['image'] != false )
                                echo '<p><a href="http://account.imagineretailer.com/images/social-media/facebook/email-sign-up/', $step, '.png"><img src="http://account.imagineretailer.com/images/social-media/facebook/email-sign-up/', $step, '.png" alt="', $data['title'], '" /></a></p>';

                            echo '<hr />';
                        endforeach;
                    else:
                ?>

                        <p class="text-right"><a href="http://www.facebook.com/pages/ABC-Company/<?php echo $email_sign_up->fb_page_id; ?>?sk=app_165553963512320" title="<?php echo _('View Facebook Page'); ?>" target="_blank"><?php echo _('View Facebook Page'); ?></a></p>

                        <form method="post" role="form">
                            <div class="form-group">
                                <label for="taContent">Text:</label>
                                <textarea class="form-control" id="taContent" name="taContent" rte="1" rows="10"><?php echo $email_sign_up->content ?></textarea>
                            </div>
                            <p>
                                <button type="button" class="btn btn-xs btn-default" title="Open Media Manager" data-media-manager data-upload-url="<?php echo $upload_url ?>" data-search-url="<?php echo $search_url ?>" data-delete-url="<?php echo $delete_url ?>">Upload File</button>
                            </p>

                            <div class="form-group">
                                <label for="sEmailList">Email List:</label>
                                <select name="sEmailList" id="sEmailList" class="form-control">
                                    <option value="0">-- Select Email List --</option>
                                    <?php foreach ( $email_lists as $el ): ?>
                                        <option value="<?php echo $el->id; ?>"<?php if ( $el->id == $email_sign_up->email_list_id ) echo 'selected' ; ?>><?php echo $el->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <?php if ( $user->account->email_marketing ) : ?>
                                <p>
                                    <a href="/email-marketing/email-lists/add-edit/" target="_blank">Add New Email List</a>
                                </p>
                            <?php endif; ?>

                            <p>
                                <?php nonce::field('email-sign-up') ?>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </p>
                        </form>

                <?php endif; ?>
            </div>
        </section>
    </div>
</div>