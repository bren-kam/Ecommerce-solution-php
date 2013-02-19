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

echo $template->start( _('Posting') . ' - ' . $page->name, '../sidebar' );

if ( !$sm_posting->fb_page_id  ) {
    // Define instructions
    $instructions = array(
        1 => array(
            'title' => _('Go to the Posting application')
            , 'text' => _('Go to the') . ' <a href="http://apps.facebook.com/op-posting/" title="' . _('Online Platform - Posting') . '" target="_blank">' . _('Posting') . '</a> ' . _('application page') . '.'
            , 'image' => false
        )
        , 2 => array(
            'title' => _('Install The App')
            , 'text' => _('Enter your Facebook Connection Key into the slot labeled Facebook Connection Key and click connect. Note, be sure the page you want to connect to is selected where it says Facebook Page: ') . $sm_posting['key']
        )
    );

    foreach ( $instructions as $step => $data ) {
        echo '<h2 class="title">', _('Step'), " $step:", $data['title'], '</h2>';

        if ( isset( $data['text'] ) )
            echo '<p>', $data['text'], '</p>';

        if ( !isset( $data['image'] ) || $data['image'] != false )
            echo '<br /><p><a href="http://account.imagineretailer.com/images/social-media/facebook/posting/', $step, '.png"><img src="http://account.imagineretailer.com/images/social-media/facebook/posting/', $step, '.png" alt="', $data['title'], '" width="750" /></a></p>';

        echo '<br /><br />';
    }
} else {
    ?>
    <?php
    if ( !empty( $errs ) )
        echo "<p class='red'>$errs</p>";

    if ( is_array( $pages ) ) {
        ?>
        <form action="" method="post" name="fFBPost" id="fFBPost">
            <table>
                <tr>
                    <td><strong><?php echo _('Page'); ?>:</strong></td>
                    <td><?php echo $pages[$sm_posting->fb_page_id]['name']; ?></td>
                </tr>
                <tr>
                    <td class="top"><label for="taPost"><?php echo _('Post'); ?>:</label></td>
                    <td><textarea name="taPost" id="taPost" rows="5" cols="50"></textarea></td>
                </tr>
                <tr>
                    <td><label for="tDate"><?php echo _('Send Date'); ?>:</label></td>
                    <td><input type="text" class="tb" name="tDate" id="tDate" style="width: 100px;" value="<?php echo ( !empty( $_POST['tDate'] ) && !$post->id ) ? $new_date_posted->format('m/d/Y') : $now->format('m/d/Y'); ?>" maxlength="10" /></td>
                </tr>
                <tr>
                    <td><label for="tTime"><?php echo _('Time'); ?></label>:</td>
                    <td><input type="text" class="tb" name="tTime" id="tTime" style="width: 100px;" value="<?php echo ( empty( $_POST['tTime'] ) && !$post->id ) ? $new_date_posted->format('h:i a') : $now->format('h:i a'); ?>" maxlength="8" /></td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" class="button" id="sSubmit" value="<?php echo _('Post to Facebook'); ?>" /></td>
                </tr>
            </table>
            <?php nonce::field('post'); ?>
        </form>
        <?php echo $js_validation; ?>
    <?php } elseif ( empty( $errs ) ) { ?>
        <p><?php echo _('In order to post to one of your Facebook pages you will need to connect them first.'); ?></p>
        <p><strong><?php echo _('Connection key'), ': '; ?></strong> <?php echo $sm_posting->key; ?></p>
        <p><a href="http://apps.facebook.com/op-posting/" title="<?php echo _('Online Platform - Posting'); ?>" target="_blank"><?php echo _('Connect your Facebook pages here.'); ?></a></p>
<?php
    }
}

echo $template->end();
?>