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
 * @var SocialMediaFanOffer $fan_offer
 */

$upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );

$start_date = new DateTime( $fan_offer->start_date );
$end_date = new DateTime( $fan_offer->end_date );
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                Fan Offer - <?php echo $page->name ?>
            </header>
            <div class="panel-body">
                <?php
                    if ( !$fan_offer->fb_page_id ) :
                        // Define instructions
                        $instructions = array(
                            1 => array(
                                'title' => _('Go to the Fan Offer application')
                            , 'text' => _('Go to the') . ' <a href="http://apps.facebook.com/op-fan-offer/" title="' . _('Online Platform - Fan Offer') . '" target="_blank">' . _('Fan Offer') . '</a> ' . _('application page') . '.'
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
                                'title' => _('Click Add Online Platform - Fan Offer')
                            )
                        , 5 => array(
                                'title' => _('Click on the Fan Offer App')
                            , 'text' => _("Scroll down below the banner, and you'll see your apps (you may need to click on the arrow on the right-hand side to find the app you're looking for) and click on the About Us")
                            )
                        , 6 => array(
                                'title' => _('Click on the Update Settings')
                            )
                        , 7 => array(
                                'title' => _('Click Add Online Platform - Fan Offer')
                            , 'text' => _('Copy and paste the connection code into the Facebook Connection Key box shown below (when done it will say Connected): ') . $fan_offer->key
                            )
                        );

                        foreach ( $instructions as $step => $data ):
                            echo "<h3>Step $step: {$data['title']}</h3>";

                            if ( isset( $data['text'] ) )
                                echo '<p>', $data['text'], '</p>';

                            if ( !isset( $data['image'] ) || $data['image'] != false )
                                echo '<p><a href="http://account.imagineretailer.com/images/social-media/facebook/fan-offer/', $step, '.png"><img src="http://account.imagineretailer.com/images/social-media/facebook/fan-offer/', $step, '.png" alt="', $data['title'], '" /></a></p>';

                            echo '<hr />';
                        endforeach;
                    else:
                ?>

                        <p class="text-right"><a href="http://www.facebook.com/pages/ABC-Company/<?php echo $fan_offer->fb_page_id; ?>?sk=app_165348580198324" title="<?php echo _('View Facebook Page'); ?>" target="_blank"><?php echo _('View Facebook Page'); ?></a></p>

                        <form method="post" role="form">
                            <div class="form-group">
                                <label for="taBefore">What Non-Fans See:</label>
                                <textarea class="form-control" id="taBefore" name="taBefore" rte="1" rows="10"><?php echo $fan_offer->before ?></textarea>
                            </div>
                            <p>
                                <button type="button" class="btn btn-xs btn-default" title="Open Media Manager" data-media-manager data-upload-url="<?php echo $upload_url ?>" data-search-url="<?php echo $search_url ?>" data-delete-url="<?php echo $delete_url ?>">Upload File</button>
                            </p>

                            <div class="form-group">
                                <label for="taAfter">What Fans See After Linking the Page:</label>
                                <textarea class="form-control" id="taAfter" name="taAfter" rte="1" rows="10"><?php echo $fan_offer->after ?></textarea>
                            </div>
                            <p>
                                <button type="button" class="btn btn-xs btn-default" title="Open Media Manager" data-media-manager data-upload-url="<?php echo $upload_url ?>" data-search-url="<?php echo $search_url ?>" data-delete-url="<?php echo $delete_url ?>">Upload File</button>
                            </p>

                            <div class="row">
                                <div class="col-lg-4">
                                    <label for="tStartDate">Start of Fan Offer:</label>
                                    <input type="text" class="form-control" name="tStartDate" id="tStartDate" value="<?php echo $start_date->format('Y-m-d') ?>" />
                                </div>
                                <div class="col-lg-2">
                                    <label for="tStartTime">At</label>
                                    <input type="text" class="form-control" name="tStartTime" id="tStartTime" value="<?php echo $start_date->format('h:i a') ?>" />
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4">
                                    <label for="tEndDate">End of Fan Offer:</label>
                                    <input type="text" class="form-control" name="tEndDate" id="tEndDate" value="<?php echo $end_date->format('Y-m-d') ?>" />
                                </div>
                                <div class="col-lg-2">
                                    <label for="tEndTime">At</label>
                                    <input type="text" class="form-control" name="tEndTime" id="tEndTime" value="<?php echo $end_date->format('h:i a') ?>" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="sEmailList">Email List:</label>
                                <select name="sEmailList" id="sEmailList" class="form-control">
                                    <option value="0">-- Select Email List --</option>
                                    <?php foreach ( $email_lists as $el ): ?>
                                        <option value="<?php echo $el->id; ?>"<?php if ( $el->id == $fan_offer->email_list_id ) echo 'selected' ; ?>><?php echo $el->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <?php if ( $user->account->email_marketing ) : ?>
                                <p>
                                    <a href="/email-marketing/email-lists/add-edit/" target="_blank">Add New Email List</a>
                                </p>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="tShareTitle">Share Title:</label>
                                <input type="text" class="form-control" name="tShareTitle" id="tShareTitle" value="<?php echo $fan_offer->share_title ?>">
                            </div>

                            <div class="form-group">
                                <label for="tShareImageURL">Share Image Link:</label>
                                <input type="text" class="form-control" name="tShareImageURL" id="tShareImageURL" value="<?php echo $fan_offer->share_image_url ?>">
                            </div>
                            <p>
                                <button type="button" class="btn btn-xs btn-default" title="Open Media Manager" data-media-manager data-upload-url="<?php echo $upload_url ?>" data-search-url="<?php echo $search_url ?>" data-delete-url="<?php echo $delete_url ?>">Upload File</button>
                            </p>

                            <div class="form-group">
                                <label for="taShareText">Share Text:</label>
                                <textarea class="form-control" id="taShareText" name="taShareText" rows="3"><?php echo $fan_offer->share_text ?></textarea>
                            </div>

                            <p>
                                <?php nonce::field('fan-offer') ?>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </p>
                        </form>

                <?php endif; ?>
            </div>
        </section>
    </div>
</div>