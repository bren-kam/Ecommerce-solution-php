<?php
/**
 * @page Social Media - Facebook - Fan Offer
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Secure the section
if ( !$user['website']['social_media'] )
    url::redirect('/');

// Make Sure they chose a facebook page
if ( !isset( $_SESSION['sm_facebook_page_id'] ) )
    url::redirect('/social-media/facebook/');

// Make sure they have access to this page
$sm = new Social_Media;
$w = new Websites;
$social_media_add_ons = @unserialize( $w->get_setting( 'social-media-add-ons' ) );
$facebook_page = $sm->get_facebook_page( $_SESSION['sm_facebook_page_id'] );

if ( !$facebook_page || !is_array( $social_media_add_ons ) || !in_array( 'fan-offer', $social_media_add_ons ) )
    url::redirect('/social-media/facebook/');

// Instantiate Classes
$e = new Email_Marketing;
$wf = new Website_Files;

// Get Timezone
$timezone = $w->get_setting('timezone');

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'fan-offer' ) ) {
	$start_date = dt::date('Y-m-d', strtotime( $_POST['tStartDate'] ) );
	$end_date = dt::date('Y-m-d', strtotime( $_POST['tEndDate'] ) );
	
	// Turn start time into machine-readable time
	list( $start_time, $am_pm ) = explode( ' ', $_POST['tStartTime'] );
	
	if ( 'pm' == $am_pm ) {
		list( $hour, $minute ) = explode( ':', $start_time );
		
		$start_date .= ' ' . ( $hour + 12 ) . ':' . $minute . ':00';
	} else {
		$start_date .= ' ' . $start_time . ':00';
	}

	// Turn end time into machine-readable time
	list( $end_time, $am_pm ) = explode( ' ', $_POST['tEndTime'] );
	
	if ( 'pm' == $am_pm ) {
		list( $hour, $minute ) = explode( ':', $end_time );
		
		$end_date .= ' ' . ( $hour + 12 ) . ':' . $minute . ':00';
	} else {
		$end_date .= ' ' . $end_time . ':00';
	}
	
	// Adjust for time zone
	$start_date = dt::adjust_timezone( $start_date, $timezone, config::setting('server-timezone') );
	$end_date = dt::adjust_timezone( $end_date, $timezone, config::setting('server-timezone') );
		
	$success = $sm->update_fan_offer( $_POST['sEmailList'], $_POST['taBefore'], $_POST['taAfter'], $start_date, $end_date, $_POST['tShareTitle'], $_POST['tShareImageURL'], $_POST['taShareText'] );
}

// Get variables
$fan_offer = $sm->get_fan_offer();
$email_lists = $e->get_email_lists();
$website_files = $wf->get_all();

if ( !$fan_offer ) {
	$fan_offer = array( 
		'key' => $sm->create_fan_offer()
		, 'before' => ''
	);
	
	// Adjust for timezone
	$fan_offer['start_date'] = strtotime( dt::adjust_timezone( 'now', config::setting('server-timezone'), $timezone ) );
	$fan_offer['end_date'] = strtotime( dt::adjust_timezone( '+1 weeks', config::setting('server-timezone'), $timezone ) );
} else {
	// Make sure they have a good starting date
	$start_date = ( 0 == $fan_offer['start_date'] ) ? 'now' : dt::date( 'Y-m-d H:i:s', $fan_offer['start_date'] );
    $end_date = ( 0 == $fan_offer['end_date'] ) ? '+1 weeks' : dt::date( 'Y-m-d H:i:s', $fan_offer['end_date'] );

	$fan_offer['start_date'] = strtotime( dt::adjust_timezone( $start_date, config::setting('server-timezone'), $timezone ) );
    $fan_offer['end_date'] = strtotime( dt::adjust_timezone( $end_date, config::setting('server-timezone'), $timezone ) );
}

// Load the jQuery UI CSS
add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );

css( 'jquery.uploadify', 'jquery.timepicker' );
javascript( 'mammoth', 'swfobject', 'jquery.uploadify', 'jquery.timepicker', 'website/page', 'social-media/facebook/dates' );

$selected = "social_media";
$title = _('Fan Offer') . ' | ' . _('Facebook') . ' | ' . _('Social Media') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Fan Offer'), ' - ', $facebook_page['name']; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'social-media/' ); ?>
	<div id="subcontent">
        <?php if ( empty( $timezone ) ) { ?>
            <p><?php echo _('Your timezone has not yet been set.'), ' <a href="/social-media/settings/" title="', _('Social Media Settings'), '">', _('Click here to set your timezone.'), '</a>'; ?></p>
		<?php 
        } else { 
           if( !isset( $fan_offer['fb_page_id'] ) || 0 == $fan_offer['fb_page_id'] ) {
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
                    , 'text' => _('Copy and paste the connection code below into the Facebook Connection Key box shown below (when done it will say Connected)')
                )
            );

            foreach ( $instructions as $step => $data ) {
                echo '<h2 class="title">', _('Step'), " $step:", $data['title'], '</h2>';

                if ( isset( $data['text'] ) )
                    echo '<p>', $data['text'], '</p>';

                if ( !isset( $data['image'] ) || $data['image'] != false )
                    echo '<br /><p><a href="http://account.imagineretailer.com/images/social-media/facebook/fan-offer/', $step, '.jpg"><img src="http://admin.imagineretailer.com/images/social-media/facebook/about-us/', $step, '.jpg" alt="', $data['title'], '" width="750" /></a></p>';

                echo '<br /><br />';
            }
         } else {
            ?>
                <p align="right"><a href="http://www.facebook.com/pages/ABC-Company/<?php echo $fan_offer['fb_page_id']; ?>?sk=app_165348580198324" title="<?php echo _('View Facebook Page'); ?>" target="_blank"><?php echo _('View Facebook Page'); ?></a></p>
                <form name="fFanOffer" action="/social-media/facebook/fan-offer/" method="post">
                    <?php if ( $success ) { ?>
                    <p class="success"><?php echo _('Your fan offer page has been successfully updated!'); ?></p>
                    <?php } ?>

                    <h2 class="title"><label for="taBefore"><?php echo _('What Non-Fans See'); ?>:</label></h2>
                    <textarea name="taBefore" id="taBefore" cols="50" rows="3" rte="1"><?php echo $fan_offer['before']; ?></textarea>
                    <p><a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a> | (<?php echo _('Image Width: 810px Image Height: 700px Max'); ?>)</p>
                    <br />

                    <h2 class="title"><label for="taAfter"><?php echo _('What Fans See After Liking the Page'); ?>:</label></h2>
                    <textarea name="taAfter" id="taAfter" cols="50" rows="3" rte="1"><?php echo $fan_offer['after']; ?></textarea>

                    <p><a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload File'); ?></a> | (<?php echo _('Image Width: 810px Image Height: 700px Max'); ?>)</p>
                    <br />

                    <h2 class="title"><label for="tStartDate"><?php echo _('Start of Fan Offer'); ?>:</label></h2>
                    <p>
                        <input type="text" class="tb" name="tStartDate" id="tStartDate" value="<?php echo dt::date( 'm/d/Y', $fan_offer['start_date'] ); ?>" />
                        <input type="text" class="tb" name="tStartTime" id="tStartTime" value="<?php echo dt::date( 'h:i a', $fan_offer['start_date'] ); ?>" style="width:75px" />
                    </p>
                    <br />

                    <h2 class="title"><label for="tEndDate"><?php echo _('End of Fan Offer'); ?>:</label></h2>
                    <p>
                        <input type="text" class="tb" name="tEndDate" id="tEndDate" value="<?php echo dt::date( 'm/d/Y', $fan_offer['end_date'] ); ?>" />
                        <input type="text" class="tb" name="tEndTime" id="tEndTime" value="<?php echo dt::date( 'h:i a', $fan_offer['end_date'] ); ?>" style="width:75px" />
                    </p>
                    <br />

                    <h2 class="title"><label for="sEmailList"><?php echo _('Email List'); ?>:</label></h2>
                    <p>
                        <select name="sEmailList" id="sEmailList">
                            <option value="http://account.imagineretailer.com/images/social-media/facebook/sweepstakes/">-- <?php echo _('Select Email List'); ?> --</option>
                            <?php
                            foreach ( $email_lists as $el ) {
                                $selected = ( $el['email_list_id'] == $fan_offer['email_list_id'] ) ? ' selected="selected"' : '';
                                ?>
                            <option value="<?php echo $el['email_list_id']; ?>"<?php echo $selected; ?>><?php echo $el['name']; ?></option>
                            <?php } ?>
                        </select>
                        <a href="/email-marketing/email-lists/add-edit/" title="<?php echo _('Add New Email List'); ?>" target="_blank"><?php echo _('Add New Email List'); ?></a>
                    </p>
                    <br /><br />

                    <h1 class="float-none padding-bottom"><?php echo _('Share Settings'); ?></h1>
                    <table cellpadding="0" cellspacing="0" class="form">
                        <tr>
                            <td><label for="tShareTitle"><?php echo _('Share Title'); ?>:</label></td>
                            <td><input type="text" class="tb" name="tShareTitle" id="tShareTitle" value="<?php echo $fan_offer['share_title']; ?>" maxlength="100" /></td>
                        </tr>
                        <tr>
                            <td><label for="tShareImageURL"><?php echo _('Share Image Link'); ?>:</label></td>
                            <td>
                                <input type="text" class="tb" name="tShareImageURL" id="tShareImageURL" value="<?php echo $share_and_save['share_image_url']; ?>" maxlength="200" /> <a href="#dUploadFile" title="<?php echo _('Upload File (Media Manager)'); ?>" rel="dialog"><?php echo _('Upload'); ?></a>
                                <br />
                                (<?php echo _('image must be 64x64'); ?>)
                            </td>
                        </tr>
                        <tr>
                            <td class="top"><label for="taShareText"><?php echo _('Share Text'); ?>:</label></td>
                            <td><textarea name="taShareText" id="taShareText" cols="50" rows="3"><?php echo $fan_offer['share_text']; ?></textarea></td>
                        </tr>
                    </table>

                    <input type="submit" class="button" value="<?php echo _('Save'); ?>" />
                    <?php nonce::field('fan-offer'); ?>
                </form>

                <div id="dUploadFile" class="hidden">
                    <ul id="ulUploadFile">
                        <?php
                        if ( is_array( $website_files ) ) {
                            // Set variables
                            $ajax_delete_file_nonce = nonce::create('delete-file');
                            $confirm = _('Are you sure you want to delete this file?');

                            foreach ( $website_files as $wf ) {
                                $file_name = f::name( $wf['file_path'] );
                                echo '<li id="li' . $wf['website_file_id'] . '"><a href="', $wf['file_path'], '" id="aFile', $wf['website_file_id'], '" class="file" title="', $file_name, '">', $file_name, '</a><a href="/ajax/website/page/delete-file/?_nonce=' . $ajax_delete_file_nonce . '&amp;wfid=' . $wf['website_file_id'] . '" class="float-right" title="' . _('Delete File') . '" ajax="1" confirm="' . $confirm . '"><img src="/images/icons/x.png" width="15" height="17" alt="' . _('Delete File') . '" /></a></li>';
                            }
                        } else {
                            echo '<li>', _('You have not uploaded any files.') . '</li>';
                        }
                        ?>
                    </ul>
                    <br />

                    <input type="text" class="tb" id="tFileName" tmpval="<?php echo _('Enter File Name'); ?>..." error="<?php echo _('You must type in a file name before uploading a file.'); ?>" style="position:relative; bottom: 11px;" />
                    <input type="file" name="fUploadFile" id="fUploadFile" />
                    <br /><br />
                    <div id="dCurrentLink" class="hidden">
                        <p><strong><?php echo _('Current Link'); ?>:</strong></p>
                        <p><input type="text" class="tb" id="tCurrentLink" value="<?php echo _('No link selected'); ?>" style="width:100%;" /></p>
                    </div>
                </div>
                <?php nonce::field( 'upload-file', '_ajax_upload_file' ); ?>
                <input type="hidden" id="hWebsiteID" value="<?php echo $user['website']['website_id']; ?>" />
            <?php
            }
        }
        ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>