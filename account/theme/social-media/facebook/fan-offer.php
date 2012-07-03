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
            if ( 0 == $fan_offer['fb_page_id'] ) { 
            ?>
                <h2 class="title"><?php echo _('Step 1: Go to the Fan Offer application.'); ?></h2>
                <p><?php echo _('Go to the'); ?> <a href="http://apps.facebook.com/op-fan-offer/" title="<?php echo _('Online Platform - Fan Offer'); ?>" target="_blank"><?php echo _('Fan Offer'); ?></a> <?php echo _('application page'); ?>.</p>
                <br /><br />

                <h2 class="title"><?php echo _('Step 2: Install on your Fan Page'); ?></h2>
                <p><?php echo _('Click'); ?> <strong><?php echo _('Add to my Page (bottom left of your page).'); ?></strong></p>
                <p><strong><?php echo _('NOTE'); ?>:</strong> <?php echo _("If you do not see this link, it means you either don't have administrative access to any fan pages, or you already have this application installed. (If it is already installed, please ahead skip to Step 4.)"); ?></p>
                <br />
                <p><img src="http://account.imagineretailer.com/images/social-media/facebook/fan-offer/step2.jpg" class="image-border" width="750" height="534" alt="<?php echo _('Step 2'); ?>" /></p>
                <br /><br />

                <h2 class="title"><?php echo _('Step 3: Click on the Add to Page Button.'); ?></h2>
                <p><?php echo _('Choose the Facebook Fan Page you want to add your app to by clicking on the'); ?> <strong><?php echo _('Add to Page'); ?></strong> <?php echo _('button to the right of the Fan Page name.'); ?></p>
                <br />
                <p><img src="http://account.imagineretailer.com/images/social-media/facebook/fan-offer/step3.jpg" class="image-border" width="750" height="236" alt="<?php echo _('Step 3'); ?>" /></p>
                <br /><br />

                <h2 class="title"><?php echo _('Step 4: Click on the App.'); ?></h2>
                <p><?php echo _('Go to your Fan Page and click on the App you are installing from the list on the left.'); ?></p>
                <br />
                <p><img src="http://account.imagineretailer.com/images/social-media/facebook/fan-offer/step4.jpg" class="image-border" width="750" height="650" alt="<?php echo _('Step 4 - 1'); ?>" /></p>
                <br />
                <p><?php echo _('Click on'); ?> <strong><?php echo _('Update Settings'); ?></strong> <?php echo _('right under the app name. Note: This is only visible to you because you are the admin for this page.'); ?></p>
                <br />
                <p><img src="http://account.imagineretailer.com/images/social-media/facebook/fan-offer/step4-1.jpg" class="image-border" width="750" height="178" alt="<?php echo _('Step 4 - 2'); ?>" /></p>
                <br /><br />

                <h2 class="title"><?php echo _('Step 5: Connect the application with your dashboard account'); ?></h2>
                <p><?php echo _('Copy the connection key listed below and paste into the Facebook app.'); ?></p>
                <p><?php echo _('Facebook Connection Key'); ?>: <?php echo $fan_offer['key']; ?></p>
                <p><strong><?php echo _('NOTE'); ?></strong>: <?php echo _('You may see a request for permissions. If this is the case, you first need to Allow Permissions to the application before you will be able to move on.'); ?></p>
                <br />
                <p><img src="http://account.imagineretailer.com/images/social-media/facebook/fan-offer/step5.jpg" class="image-border" width="750" height="150" alt="<?php echo _('Step 5'); ?>" /></p>
                <br />
                <p><?php echo _('When you click Connect, you will see'); ?> <span class="error"><?php echo _('(Not Connected)'); ?></span> <?php echo _('in red change to'); ?> <span class="success"><?php echo _('(Connected)'); ?></span> <?php echo _('in green.'); ?></p>
                <br /><br />

                <h2 class="title"><?php echo _('Step 6: Final App Activation.'); ?></h2>
                <p><?php echo _('Click the activate link to complete the installation process. You will then be able to control all the content for the app from this dashboard.'); ?></p>
                <p><a href="/social-media/facebook/fan-offer/" title="<?php echo _('Activate'); ?>"><?php echo _('Activate'); ?></a></p>
                <br />
                <p><img src="http://account.imagineretailer.com/images/social-media/facebook/fan-offer/step6.jpg" class="image-border" width="489" height="190" alt="<?php echo _('Step 6'); ?>" /></p>
                <br /><br />
            <?php } else { ?>
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