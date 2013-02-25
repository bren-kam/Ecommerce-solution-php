<?php
/**
 * @package Grey Suit Retail
 * @page Sweepstakes | Facebook | Social Media
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var SocialMediaFacebookPage $page
 * @var SocialMediaSweepstakes $sm_sweepstakes
 * @var AccountFile[] $files
 * @var EmailList[] $email_lists
 * @var string $errs
 * @var string $js_validation
 * @var AccountPage[] $pages
 */

$start_date = new DateTime( $sm_sweepstakes->start_date );
$end_date = new DateTime( $sm_sweepstakes->end_date );

echo $template->start( _('Sweepstakes') . ' - ' . $page->name, 'sidebar' );

if ( !$sm_sweepstakes->fb_page_id ) {
    // Define instructions
    $instructions = array(
        1 => array(
           'title' => _('Go to the Sweepstakes application')
           , 'text' => _('Go to the') . ' <a href="http://apps.facebook.com/op-sweepstakes/" title="' . _('Online Platform - Sweepstakes') . '" target="_blank">' . _('Sweepstakes') . '</a> ' . _('application page') . '.'
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
           'title' => _('Click Add Online Platform - Sweepstakes')
        )
        , 5 => array(
           'title' => _('Click on the Sweepstakes App')
           , 'text' => _("Scroll down below the banner, and you'll see your apps (you may need to click on the arrow on the right-hand side to find the app you're looking for) and click on the Sweepstakes")
        )
        , 6 => array(
           'title' => _('Click on the Update Settings')
        )
        , 7 => array(
           'title' => _('Click Add Online Platform - Sweepstakes')
           , 'text' => _('Copy and paste the connection code into the Facebook Connection Key box shown below (when done it will say Connected): ') . $sm_sweepstakes->key
        )
    );

    foreach ( $instructions as $step => $data ) {
       echo '<h2 class="title">', _('Step'), " $step:", $data['title'], '</h2>';

       if ( isset( $data['text'] ) )
           echo '<p>', $data['text'], '</p>';

       if ( !isset( $data['image'] ) || $data['image'] != false )
           echo '<br /><p><a href="http://account.imagineretailer.com/images/social-media/facebook/sweepstakes/', $step, '.png"><img src="http://account.imagineretailer.com/images/social-media/facebook/sweepstakes/', $step, '.png" alt="', $data['title'], '" width="750" /></a></p>';

       echo '<br /><br />';
    }
} else {
    ?>
    <p class="text-right"><a href="http://www.facebook.com/pages/ABC-Company/<?php echo $sm_sweepstakes->fb_page_id; ?>?sk=app_113993535359575" title="<?php echo _('View Facebook Page'); ?>" target="_blank"><?php echo _('View Facebook Page'); ?></a></p>
    <?php
    if ( !empty( $errs ) )
        echo "<p class='error'>$errs</p>";
    ?>
    <form name="fSweepstakes" action="/social-media/facebook/sweepstakes/" method="post">
        <h2 class="title"><label for="taBefore"><?php echo _('What Non-Fans See'); ?>:</label></h2>
        <textarea name="taBefore" id="taBefore" cols="50" rows="3" rte="1"><?php echo $sm_sweepstakes->before; ?></textarea>
        <p><a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a> | (<?php echo _('Image Width: 810px Image Height: 700px Max'); ?>)</p>
        <br />

        <h2 class="title"><label for="taAfter"><?php echo _('What Fans See After Liking the Page'); ?>:</label></h2>
        <textarea name="taAfter" id="taAfter" cols="50" rows="3" rte="1"><?php echo $sm_sweepstakes->after; ?></textarea>
        <p><a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a> | (<?php echo _('Image Width: 810px Image Height: 700px Max'); ?>)</p>
        <br />

        <h2 class="title"><label for="tStartDate"><?php echo _('Start of Sweepstakes'); ?>:</label></h2>
        <p>
            <input type="text" class="tb" name="tStartDate" id="tStartDate" value="<?php echo $start_date->format( 'm/d/Y' ); ?>" />
            <input type="text" class="tb" name="tStartTime" id="tStartTime" value="<?php echo $start_date->format( 'h:i a' ); ?>" style="width:75px" />
        </p>
        <br />

        <h2 class="title"><label for="tEndDate"><?php echo _('End of Sweepstakes'); ?>:</label></h2>
        <p>
            <input type="text" class="tb" name="tEndDate" id="tEndDate" value="<?php echo $end_date->format( 'm/d/Y' ); ?>" />
            <input type="text" class="tb" name="tEndTime" id="tEndTime" value="<?php echo $end_date->format( 'h:i a' ); ?>" style="width:75px" />
        </p>
        <br />

        <h2 class="title"><label for="sEmailList"><?php echo _('Email List'); ?>:</label></h2>
        <p>
            <select name="sEmailList" id="sEmailList">
                <option value="0">-- <?php echo _('Select Email List'); ?> --</option>
                <?php
                foreach ( $email_lists as $el ) {
                    $selected = ( $el->id == $sm_sweepstakes->email_list_id ) ? ' selected="selected"' : '';
                    ?>
                <option value="<?php echo $el->id; ?>"<?php echo $selected; ?>><?php echo $el->name; ?></option>
                <?php } ?>
            </select>
            <a href="/email-marketing/email-lists/add-edit/" title="<?php echo _('Add New Email List'); ?>" target="_blank"><?php echo _('Add New Email List'); ?></a>
        </p>
        <br />

        <h2 class="title"><label for="contest-rules"><?php echo ( $user->account->pages ) ? _('Contest Rules Page') : _('Contest Rules Link'); ?>:</label></h2>
        <?php if ( $user->account->pages ) { ?>
            <p>
                <select name="contest-rules" id="contest-rules">
                    <option value="">-- <?php echo _('Contest Rule Page'); ?> --</option>
                    <?php
                    foreach ( $pages as $p ) {
                        $link = 'http://' . $user->account->domain . '/' . $p->slug . '/';

                        $selected = ( $link == $sm_sweepstakes->contest_rules_url ) ? ' selected="selected"' : '';
                        ?>
                        <option value="<?php echo $link; ?>"<?php echo $selected; ?>><?php echo $p->title; ?></option>
                    <?php } ?>
                </select>
            </p>
        <?php } else { ?>
            <p><input type="text" class="tb" name="contest-rules" id="contest-rules" value="<?php echo $sm_sweepstakes->contest_rules_url; ?>" /></p>
        <?php } ?>
        <br />

        <h1 class="float-none padding-bottom"><?php echo _('Share Settings'); ?></h1>
        <table>
            <tr>
                <td><label for="tShareTitle"><?php echo _('Share Title'); ?>:</label></td>
                <td><input type="text" class="tb" name="tShareTitle" id="tShareTitle" value="<?php echo $sm_sweepstakes->share_title; ?>" maxlength="100" /></td>
            </tr>
            <tr>
                <td><label for="tShareImageURL"><?php echo _('Share Image Link'); ?>:</label></td>
                <td>
                    <input type="text" class="tb" name="tShareImageURL" id="tShareImageURL" value="<?php echo $sm_sweepstakes->share_image_url; ?>" maxlength="200" /> <a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload'); ?></a>
                    <br />
                    (<?php echo _('image must be 64x64'); ?>)
                </td>
            </tr>
            <tr>
                <td class="top"><label for="taShareText"><?php echo _('Share Text'); ?>:</label></td>
                <td><textarea name="taShareText" id="taShareText" cols="50" rows="3"><?php echo $sm_sweepstakes->share_text; ?></textarea></td>
            </tr>
        </table>

        <br /><br />
        <input type="submit" class="button" value="<?php echo _('Save'); ?>" />
        <?php nonce::field('sweepstakes'); ?>
    </form>
    <?php echo $js_validation; ?>

    <div id="dUploadFile" class="hidden">
        <ul id="ulUploadFile">
            <?php
            if ( !empty( $files ) ) {
                // Set variables
                $delete_file_nonce = nonce::create('delete_file');
                $confirm = _('Are you sure you want to delete this file?');

                /**
                 * @var AccountFile $file
                 */
                foreach ( $files as $file ) {
                    $file_name = f::name( $file->file_path );
                    echo '<li id="li' . $file->id . '"><a href="', $file->file_path, '" id="aFile', $file->id, '" class="file" title="', $file_name, '">', $file_name, '</a><a href="' . url::add_query_arg( array( '_nonce' => $delete_file_nonce, 'afid' => $file->id ), '/website/delete-file/' ) . '" class="float-right" title="' . _('Delete File') . '" ajax="1" confirm="' . $confirm . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete File') . '" /></a></li>';
                }
            } else {
                echo '<li class="no-files">', _('You have not uploaded any files.') . '</li>';
            }
            ?>
        </ul>
        <br />

        <input type="text" class="tb" id="tFileName" tmpval="<?php echo _('Enter File Name'); ?>..." error="<?php echo _('You must type in a file name before uploading a file.'); ?>" />
        <a href="#" id="aUploadFile" class="button" title="<?php echo _('Upload'); ?>"><?php echo _('Upload'); ?></a>
        <div class="hidden" id="upload-file"></div>
        <br /><br />
        <div id="dCurrentLink" class="hidden">
            <p><strong><?php echo _('Current Link'); ?>:</strong></p>
            <p><input type="text" class="tb" id="tCurrentLink" value="<?php echo _('No link selected'); ?>" style="width:100%;" /></p>
        </div>
    </div>
    <?php nonce::field( 'upload_file', '_upload_file' ); ?>
<?php } ?>


<?php echo $template->end(); ?>