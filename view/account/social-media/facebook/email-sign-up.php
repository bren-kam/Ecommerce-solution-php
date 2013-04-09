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
 * @var AccountFile[] $files
 * @var EmailList[] $email_lists
 * @var string $errs
 * @var string $js_validation
 */

echo $template->start( _('Email Sign Up') . ' - ' . $page->name, 'sidebar' );

if ( !$email_sign_up->fb_page_id ) {
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

    foreach ( $instructions as $step => $data ) {
        echo '<h2 class="title">', _('Step'), " $step:", $data['title'], '</h2>';

        if ( isset( $data['text'] ) )
            echo '<p>', $data['text'], '</p>';

        if ( !isset( $data['image'] ) || $data['image'] != false )
            echo '<br /><p><a href="http://account.imagineretailer.com/images/social-media/facebook/email-sign-up/', $step, '.png"><img src="http://account.imagineretailer.com/images/social-media/facebook/email-sign-up/', $step, '.png" alt="', $data['title'], '" width="750" /></a></p>';

        echo '<br /><br />';
    }
 } else {
    ?>
    <p class="text-right"><a href="http://www.facebook.com/pages/ABC-Company/<?php echo $email_sign_up->fb_page_id; ?>?sk=app_165553963512320" title="<?php echo _('View Facebook Page'); ?>" target="_blank"><?php echo _('View Facebook Page'); ?></a></p>
    <?php
    if ( !empty( $errs ) )
        echo "<p class='error'>$errs</p>";
    ?>
    <form name="fEmailSignUp" action="/social-media/facebook/email-sign-up/" method="post">
        <textarea name="taTab" id="taTab" cols="50" rows="3" rte="1"><?php echo $email_sign_up->tab; ?></textarea>

        <p><a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a> | (<?php echo _('Image Width: 810px Image Height: 700px Max'); ?>)</p>
        <br /><br />

        <p>
            <select name="sEmailList" id="sEmailList">
                <option value="0">-- <?php echo _('Select Email List'); ?> --</option>
                <?php
                foreach ( $email_lists as $el ) {
                    $selected = ( $el->id == $email_sign_up->email_list_id ) ? ' selected="selected"' : '';
                    ?>
                <option value="<?php echo $el->id; ?>"<?php echo $selected; ?>><?php echo $el->name; ?></option>
                <?php } ?>
            </select>
            <br />
            <a href="/email-marketing/email-lists/add-edit/" title="<?php echo _('Add New Email List'); ?>" target="_blank"><?php echo _('Add New Email List'); ?></a>
        </p>

        <br /><br />
        <input type="submit" class="button" value="<?php echo _('Save'); ?>" />
        <?php nonce::field('email_sign_up'); ?>
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
        <a href="#" class="button loader hidden" id="upload-file-loader" title="<?php echo _('Loading'); ?>"><img src="/images/buttons/loader.gif" alt="<?php echo _('Loading'); ?>" /></a>
        <div class="transparent position-absolute" id="upload-file"></div>
        <br /><br />
        <div id="dCurrentLink" class="hidden">
            <p><strong><?php echo _('Current Link'); ?>:</strong></p>
            <p><input type="text" class="tb" id="tCurrentLink" value="<?php echo _('No link selected'); ?>" style="width:100%;" /></p>
        </div>
    </div>
    <?php nonce::field( 'upload_file', '_upload_file' ); ?>
<?php } ?>


<?php echo $template->end(); ?>