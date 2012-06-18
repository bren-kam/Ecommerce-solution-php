<?php
/**
 * @page Social Media - Facebook - Posting
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Make sure they have access to this page
$w = new Websites;
$social_media_add_ons = @unserialize( $w->get_setting( 'social-media-add-ons' ) );

if ( !is_array( $social_media_add_ons ) || !in_array( 'posting', $social_media_add_ons ) )
    url::redirect('/social-media/facebook/');

// Instantiate Classes
$sm = new Social_Media;
$v = new Validator;
$fb = new FB( '268649406514419', '6ca6df4c7e9d909a58d95ce7360adbf3' );
$w = new Websites;

// Add validation
$v->form_name = 'fFBPost';
$v->trigger = true;
$v->add_validation( 'taPost', 'req', _('The Post field is required') );

add_footer( $v->js_validation() );

// Get variables
$posting = $sm->get_posting();
$timezone = $w->get_setting('timezone');

// Figure out what the time is
$now = new DateTime( dt::adjust_timezone( 'now', config::setting('server-timezone'), $timezone ) );

// Get the posting variable if it exists
if ( 0 != $posting['fb_page_id'] ) {
	$fb->setAccessToken( $posting['access_token'] );
	
	try {
		$accounts = $fb->api( '/' . $posting['fb_user_id'] . '/accounts' );
	} catch( Exception $e ) {
		$response = Response::fb_exception( $e );
		
		switch ( $response->error_code() ) {
			case 190:
				$errs = _('Due to a recent change to your Facebook account, your Online Specialist needs to reconnect your Posting App to Facebook.');
			break;
		}
	}
	
	$pages = ar::assign_key( $accounts['data'], 'id' );
} elseif ( !$posting ) {
	$posting = array(
		'key' => $sm->create_posting()
	);
}

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'fb-post' ) ) {
    $errs = $v->validate();
    
    if ( empty( $errs ) ) {
        $date_posted = $_POST['tDate'];
    
        // Turn it into machine-readable time
        if ( !empty( $_POST['tTime'] ) ) {
            list( $time, $am_pm ) = explode( ' ', $_POST['tTime'] );
            
            if ( 'pm' == strtolower( $am_pm ) ) {
                list( $hour, $minute ) = explode( ':', $time );
                
                $date_posted .= ' ' . ( $hour + 12 ) . ':' . $minute . ':00';
            } else {
                $date_posted .= ' ' . $time . ':00';
            }
        }
        
        // Adjust for time zone
        $new_date_posted = new DateTime( dt::adjust_timezone( $date_posted, $timezone, config::setting('server-timezone') ) );

        // Make sure we don't have anything extra
        $_POST['taPost'] = str_replace( array( '“', '”', '’' ), array( '"', '"', "'" ), $_POST['taPost'] );
        
        // Get link
        preg_match( '/(?:(http|ftp|https):\/\/|www\.)[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;:\/~\+#]*[\w\-\@?^=%&amp;\/~\+#])?/', $_POST['taPost'], $matches );
        
        if ( !empty( $matches[0] ) ) {
            $link = ( stristr( $matches[0], 'http://' ) ) ? $matches[0] : 'http://' . $matches[0];
        } else {
            $link = '';
        }
        
        if ( time() >= $new_date_posted->getTimestamp() ) {
            $fb->setAccessToken( $pages[$posting['fb_page_id']]['access_token'] );
            
            // Information:
            // http://developers.facebook.com/docs/reference/api/page/#posts
            $fb->api( $posting['fb_page_id'] . '/feed', 'POST', array( 'message' => $_POST['taPost'], 'link' => $link ) );
            
            $success = $sm->create_posting_post( $pages[$posting['fb_page_id']]['access_token'], $_POST['taPost'], $link, $new_date_posted->format('Y-m-d H:i:s'), 1 );
        } else {
            $success = $sm->create_posting_post( $pages[$posting['fb_page_id']]['access_token'], $_POST['taPost'], $link, $new_date_posted->format('Y-m-d H:i:s') );
        }
    }
}

css( 'jquery.uploadify', 'jquery.timepicker' );
javascript( 'mammoth', 'swfobject', 'jquery.uploadify', 'jquery.timepicker', 'website/page', 'social-media/facebook/posting' );

// Load the jQuery UI CSS
add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );

$selected = "social_media";
$title = _('Posting') . ' | ' . _('Facebook') . ' | ' . _('Social Media') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Posting'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'social-media/', 'posting' ); ?>
	<div id="subcontent">
		<?php if ( empty( $timezone ) ) { ?>
            <p><?php echo _('Your timezone has not yet been set.'), ' <a href="/social-media/settings/" title="', _('Social Media Settings'), '">', _('Click here to set your timezone.'), '</a>'; ?></p>
		<?php
        } else {
            if ( 0 == $posting['fb_user_id'] ) {
            ?>
                <h2 class="title"><?php echo _('Step 1: Go to the Posting application.'); ?></h2>
                <p><?php echo _('Go to the'); ?> <a href="http://apps.facebook.com/op-posting/" title="<?php echo _('Online Platform - Posting'); ?>" target="_blank"><?php echo _('Posting'); ?></a> <?php echo _('application page'); ?>.</p>
                <br /><br />

                <h2 class="title"><?php echo _('Step 2: Connect the application with your dashboard account'); ?></h2>
                <p><?php echo _('Copy the connection key listed below and paste into the Facebook app.'); ?></p>
                <p><?php echo _('Facebook Connection Key'); ?>: <?php echo $posting['key']; ?></p>
                <p><strong><?php echo _('NOTE'); ?></strong>: <?php echo _('You may see a request for permissions. If this is the case, you first need to Allow Permissions to the application before you will be able to move on.'); ?></p>
                <br /><br />

            <?php } else { ?>
                <h2 class="title"><?php echo _('Post To Your Pages'); ?></h2>
                <?php if ( $success ) { ?>
                    <p class="success"><?php echo _('Your message has been successfully posted or scheduled to your Facebook page!'); ?></p>
                <?php
                }

                if ( isset( $errs ) && !empty( $errs ) )
                    echo "<p class='red'>$errs</p>";

                if ( is_array( $pages ) ) { ?>
                    <form action="" method="post" name="fFBPost" id="fFBPost">
                        <table>
                            <tr>
                                <td><strong><?php echo _('Page'); ?>:</strong></td>
                                <td><?php echo $pages[$posting['fb_page_id']]['name']; ?></td>
                            </tr>
                            <tr>
                                <td class="top"><label for="taPost"><?php echo _('Post'); ?>:</label></td>
                                <td><textarea name="taPost" id="taPost" rows="5" cols="50"></textarea></td>
                            </tr>
                            <tr>
                                <td><label for="tDate"><?php echo _('Send Date'); ?>:</label></td>
                                <td><input type="text" class="tb" name="tDate" id="tDate" value="<?php echo ( isset( $new_date_posted ) && !$success ) ? $new_date_posted->format('m/d/Y') : $now->format('m/d/Y'); ?>" maxlength="10" /></td>
                                <td><label for="tTime"><?php echo _('Time'); ?></label>:</td>
                                <td><input type="text" class="tb" name="tTime" id="tTime" style="width: 75px;" value="<?php echo ( isset( $new_date_posted ) && !$success ) ? $new_date_posted->format('h:i a') : $now->format('h:i a'); ?>" maxlength="8" /></td>
                            </tr>
                            <tr><td colspan="2">&nbsp;</td></tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td><input type="submit" class="button" id="sSubmit" value="<?php echo _('Post to Facebook'); ?>" /></td>
                            </tr>
                        </table>
                        <?php nonce::field('fb-post'); ?>
                    </form>
                <?php } elseif ( empty( $errs ) ) { ?>
                    <p><?php echo _('In order to post to one of your Facebook pages you will need to connect them first.'); ?> <a href="http://apps.facebook.com/op-posting/" title="<?php echo _('Online Platform - Posting'); ?>" target="_blank"><?php echo _('Connect your Facebook pages here.'); ?></a></p>
                <?php
                }
            }
        }
        ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>