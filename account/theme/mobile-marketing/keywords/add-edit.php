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

list( $used_keywords, $total_keywords ) = $m->get_keyword_usage();

$full = $used_keywords >= $total_keywords;

if ( !$full || isset( $_POST['_nonce'] ) ) {
    
    // Get variables
    $mobile_lists = $m->get_mobile_lists();
    
    // Do Validation
    $v = new Validator();
    $v->form_name = 'fAddEditKeyword';
    
    if ( !$mobile_keyword_id ) {
        $v->add_validation( 'tKeyword', 'req' , _('The "Keyword" field is required') );
        $v->add_validation( 'hKeywordAvailable', 'val=1' , _('The "Keyword" field must contain an available keyword') );
    }
    
    $v->add_validation( 'taResponse', 'req', _('The "Response" field is required') );
    $v->add_validation( 'taResponse', 'maxlen=132', _('The "Response" field must be 132 characters or less') );

    // Add validation
    add_footer( $v->js_validation() );
    
    $v->add_validation( 'cbMobileLists', 'req', _('The "Mobile List" field is required') );

    // Initialize variable
    $success = false;
    
    // Make sure it's a valid request
    if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-keyword' ) ) {
        $errs = $v->validate();
        
        // if there are no errors
        if ( empty( $errs ) ) {
            if ( $mobile_keyword_id ) {
                // Update subscriber
                $success = $m->update_keyword( $mobile_keyword_id, stripslashes( $_POST['taResponse'] ), $_POST['sMobileLists'] );
            } else {
                // Create Subscriber
                $success = $m->create_keyword( stripslashes( $_POST['tKeyword'] ), stripslashes( $_POST['taResponse'] ), $_POST['sMobileLists'] );
            }
        }
    }
    
    // Get the subscriber if necessary
    if ( $mobile_keyword_id || $success ) {
        $keyword = ( !$mobile_keyword_id && $success ) ? $m->get_keyword( $success ) : $m->get_keyword( $mobile_keyword_id );
    } else {
        // Initialize variable
        $keyword = array(
            'keyword' => ''
            , 'response' => ''
            , 'mobile_lists' => array()
        );
    }

    css('mobile-marketing/main');
    javascript( 'mammoth', 'mobile-marketing/keywords/add-edit' );
}

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
        if ( $full ) {
            ?>
            <p><?php echo _('You have used all of your available keywords'), ": $used_keywords/$total_keywords.</p><p>", _('Please upgrade your Mobile Marketing Plan with your Online Specialist or delete a keyword and try again.'); ?></p>
        <?php
        } else {
            nonce::field( 'check-availability', '_ajax_check_availability' );

            if ( $success ) {
                ?>
                <div class="success">
                    <p><?php echo ( $mobile_keyword_id ) ? _('Your keyword has been updated successfully!') : _('Your keyword has been added successfully!'); ?></p>
                    <p><?php echo '<a href="/mobile-marketing/keywords/" title="', _('Keywords'), '">', _('Click here to view your keywords'), '</a>.'; ?></p>
                </div>
            <?php
            }

            // Allow them to edit the entry they just created
            if ( $success && !$mobile_keyword_id )
                $mobile_keyword_id = $success;

            if ( isset( $errs ) )
                echo "<p class='red'>$errs</p>";

            if ( is_array( $mobile_lists ) && count( $mobile_lists ) > 0 ) {
            ?>

            <form name="fAddEditKeyword" action="/mobile-marketing/keywords/add-edit/<?php if ( $mobile_keyword_id ) echo "?mkid=$mobile_keyword_id"; ?>" method="post">
                <?php nonce::field( 'add-edit-keyword' ); ?>
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td><label><?php echo _('Short Code'); ?>:</label></td>
                        <td>#96362</td>
                    </tr>
                    <tr>
                        <td><label><?php echo _('Available Keywords'); ?>:</label></td>
                        <td><?php echo $total_keywords - $used_keywords; ?></td>
                    </tr>
                    <tr>
                        <td><label for="tKeyword"><?php echo _('Keyword'); ?>:</label></td>
                        <td>
                            <?php
                            if ( $mobile_keyword_id ) {
                                echo $keyword['keyword'];
                            } else {
                                ?>
                                <input type="text" class="tb" name="tKeyword" id="tKeyword" maxlength="20" value="<?php echo ( !$success && isset( $_POST['tKeyword'] ) ) ? $_POST['tKeyword'] : $keyword['keyword']; ?>" />
                                <br />
                                <p><a href="javascript:;" id="aCheckKeywordAvailability" title="<?php echo _('Check Keyword Availability'); ?>"><?php echo _('Check Availability'); ?></a> <span id="sAvailable"></span></span></p>
                                <input type="hidden" name="hKeywordAvailable" id="hKeywordAvailable" value="<?php echo ( $mobile_keyword_id ) ? '1' : '0'; ?>" />
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="top"><label for="taResponse"><?php echo _('Response'); ?>:</label></td>
                        <td>
                            <textarea name="taResponse" id="taResponse" rows="5" cols="50"><?php echo ( !$success && isset( $_POST['taResponse'] ) ) ? $_POST['taResponse'] : $keyword['response']; ?></textarea>
                            <a href="javascript:var%20e=document.createElement('script');e.setAttribute('language','javascript');e.setAttribute('src','//bitly.com/bookmarklet/load.js');document.body.appendChild(e);void(0);" id="aURLShortner" title="<?php echo _('Shorten URL'); ?>"><?php echo _('Shorten URL'); ?></a>
                        </td>
                    </tr>
                    <tr>
                        <td class="top"><label><?php echo _('Lists'); ?></label>:</td>
                        <td>
                            <p>
                                <?php
                                $selected_mobile_lists = ( !$success && isset( $_POST['cbMobileLists'] ) ) ? $_POST['cbMobileLists'] : $keyword['mobile_lists'];

                                foreach ( $mobile_lists as $ml ) {
                                    $checked = ( in_array( $ml['mobile_list_id'], $selected_mobile_lists ) ) ? ' checked="checked"' : '';
                                ?>
                                <input type="checkbox" class="cb" name="cbMobileLists[]" id="cbMobileList<?php echo $ml['mobile_list_id']; ?>" value="<?php echo $ml['mobile_list_id']; ?>"<?php echo $checked; ?> /> <label for="cbMobileList<?php echo $ml['mobile_list_id']; ?>"><?php echo $ml['name']; ?></label>
                                <br />
                                <?php } ?>
                            </p>
                        </td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><input type="submit" class="button" value="<?php echo ( $mobile_keyword_id ) ? _('Update Keyword') : _('Add Keyword'); ?>" /></td>
                    </tr>
                </table>
            </form>
            <?php } else { ?>
                <p><?php echo _('You must'), ' ', '<a href="/mobile-marketing/lists/add-edit/" title="', _('Add Mobile List'), '">', _('add a Mobile List'), '</a>', ' ', _('before you can add a Keyword.'); ?></p>
            <?php
            }
        }
        ?>
        <br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>