<?php
/**
 * @page Add Edit Keyword
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Secure the section
if ( !$user['website']['mobile_marketing'] )
    url::redirect('/');

$m = new Mobile_Marketing;
$w = new Websites;

// Get the mobile subscriber id if there is one
$mobile_keyword_id = ( isset( $_GET['mkid'] ) ) ? $_GET['mkid'] : false;

// Get variables
$timezone = $w->get_setting( 'timezone' );

// Figure out what the time is
$now = new DateTime;
$now->setTimestamp( time() - $now->getOffset() + 3600 * $timezone );

$v = new Validator();
$v->form_name = 'fAddEditKeyword';
$v->add_validation( 'tName', 'req' , _('The "Name" field is required') );

$v->add_validation( 'tKeyword', 'req' , _('The "Keyword" field is require') );
$v->add_validation( 'hKeywordAvailable', 'val=1' , _('The "Keyword" field must contain an available keyword') );

$v->add_validation( 'taResponse', 'req', _('The "Response" field is required') );
$v->add_validation( 'taResponse', 'maxlen=140', _('The "Response" field must be 140 characters or less') );

$v->add_validation( 'tDateStarted', 'req', _('The "DateStarted" field is required') );
$v->add_validation( 'sTimezone', 'req', _('The "Timezone" field is required') );

// Add validation
add_footer( $v->js_validation() );

// Initialize variable
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-keyword' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) ) {
		if ( $mobile_keyword_id ) {
			// Update subscriber
			$success = $m->create_keyword( $_POST['tName'], $_POST['tKeyword'], $_POST['taResponse'], $_POST['tDateStarted'], $_POST['sTimezone'] );
		} else {
    		$success = $m->update_keyword( $mobile_keyword_id, $_POST['tName'], $_POST['tKeyword'], $_POST['taResponse'], $_POST['tDateStarted'], $_POST['sTimezone'] );
		}
	}
}

// Get the subscriber if necessary
if ( $mobile_keyword_id ) {
	$keyword = $m->get_keyword( $mobile_keyword_id );
} else {
	// Initialize variable
	$keyword = array(
		'name' => ''
		, 'keyword' => ''
        , 'response' => ''
        , 'date_started' => ''
        , 'timezone' => ''
	);
}

css('mobile-marketing/main');
javascript( 'mammoth', 'mobile-marketing/keywords/add-edit' );

// Load the jQuery UI CSS
add_head( '<link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/ui-lightness/jquery-ui.css" />' );

$selected = "mobile_marketing";
$sub_title = ( $mobile_keyword_id ) ? _('Edit Keyword') : _('Add Keyword');
$title = "$sub_title | " . _('Mobile Marketing') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'mobile-marketing/', 'keywords' ); ?>
	<div id="subcontent">
        <?php
        nonce::field( 'check-availability', '_ajax_check_availability' );

        if ( $success ) {
            ?>
            <div class="success">
                <p><?php echo ( $mobile_keyword_id ) ? _('Your keyword has been updated successfully!') : _('Your keyword has been added successfully!'); ?></p>
                <p><?php echo _('Click here to'), ' <a href="/mobile-marketing/keywords/" title="', _('Keywords'), '">', _('view your keywords'), '</a>.'; ?></p>
            </div>
		<?php 
		}
		
		// Allow them to edit the entry they just created
		if ( $success && !$mobile_keyword_id )
			$mobile_keyword_id = $success;
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		?>
		<form name="fAddEditKeyword" action="/mobile-marketing/keywords/add-edit/<?php if ( $mobile_keyword_id ) echo "?mkid=$mobile_keyword_id"; ?>" method="post">
			<?php nonce::field( 'add-edit-keyword' ); ?>
			<table cellpadding="0" cellspacing="0">
				<tr>
					<td><label for="tName"><?php echo _('Name of Keyword Campaign'); ?>:</label></td>
					<td><input type="text" class="tb" name="tName" id="tName" maxlength="20" value="<?php echo ( !$success && isset( $_POST['tName'] ) ) ? $_POST['tName'] : $keyword['name']; ?>" /></td>
				</tr>
				<tr>
					<td><label for="tKeyword"><?php echo _('Keyword'); ?>:</label></td>
					<td>
                        <input type="text" class="tb" name="tKeyword" id="tKeyword" maxlength="20" value="<?php echo ( !$success && isset( $_POST['tKeyword'] ) ) ? $_POST['tKeyword'] : $keyword['keyword']; ?>" />
                        <br />
                        <p><a href="javascript:;" id="aCheckKeywordAvailability" title="<?php echo _('Check Keyword Availability'); ?>"><?php echo _('Check Availability'); ?></a> <span id="sAvailable"></span></span></p>
                        <input type="hidden" name="hKeywordAvailable" id="hKeywordAvailable" value="0" />
                    </td>
				</tr>
				<tr>
					<td class="top"><label for="taResponse"><?php echo _('Response'); ?>:</label></td>
					<td><textarea name="taResponse" id="taResponse" rows="5" cols="50"><?php echo ( !$success && isset( $_POST['taResponse'] ) ) ? $_POST['taResponse'] : $keyword['response']; ?></textarea></td>
				</tr>
				<tr>
                    <td><label for="tDateStarted"><?php echo _('Campaign Start Date'); ?>:</label></td>
                    <td><input type="text" class="tb" name="tDateStarted" id="tDateStarted" value="<?php echo ( !$success && isset( $_POST['tDateStarted'] ) ) ? $now->format('m/d/Y') : $_POST['tDateStarted']; ?>" maxlength="10" /></td>
                </tr>
                <tr>
                    <td><label for="sTimeZone"><?php echo _('Timezone'); ?></label>:</td>
                    <td>
                        <select name="sTimezone" id="sTimeZone">
                            <?php
                            $timezones = array(
                                'E' =>  _('Eastern')
                                , 'C' => _('Central')
                                , 'M' => _('Mountain')
                                , 'P' => _('Pacific')
                            );

                            $timezone = ( !$success && isset( $_POST['tDateStarted'] ) ) ? $keyword['timezone'] : $_POST['sTimezone'];

                            foreach ( $timezones as $abbr => $zone ) {
                                $selected = ( $abbr == $timezone ) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?php echo $abbr; ?>"<?php echo $selected; ?>><?php echo $zone; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="submit" class="button" value="<?php echo ( $mobile_keyword_id ) ? _('Update Keyword') : _('Add Keyword'); ?>" /></td>
				</tr>
			</table>
		</form>
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>