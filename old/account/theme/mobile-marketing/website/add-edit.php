<?php
/**
 * @page Add Edit Mobile Pages
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

// Get the mobile page id if there is one
$mobile_page_id = ( isset( $_GET['mpid'] ) ) ? $_GET['mpid'] : false;

$v = new Validator();
$v->form_name = 'fAddEditPage';

$v->add_validation( 'tTitle', 'req', _('The "Title" field is required') );
$v->add_validation( 'tSlug', 'req', _('The "URL" field is required') );
$v->add_validation( 'taContent', 'req', _('Page Content is required.') );

// Add validation
add_footer( $v->js_validation() );

// Initialize variable
$success = false;

// Make sure it's a valid request
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-edit-page' ) ) {
	$errs = $v->validate();
	
	// if there are no errors
	if ( empty( $errs ) ) {
		if ( $mobile_page_id ) {
            // Get page
            $page = $m->get_mobile_page( $mobile_page_id );

            $slug = ( 'home' == $page['slug'] ) ? 'home' : $_POST['tSlug'];

			// Update subscriber
			$success = $m->update_mobile_page( $mobile_page_id, $slug, $_POST['tTitle'], $_POST['taContent'] );
		} else {
			$success = $m->create_mobile_page( $_POST['tSlug'], $_POST['tTitle'], $_POST['taContent'] );

            $page = $m->get_mobile_page( $success );
		}
	}
}

// Get the subscriber if necessary
if ( $mobile_page_id ) {
	$page = $m->get_mobile_page( $mobile_page_id );
} elseif ( !$success ) {
	// Initialize variable
	$page = array(
		'title' => ''
		, 'slug' => ''
        , 'content' => ''
        , 'meta_title' => ''
        , 'meta_description' => ''
        , 'meta_keywords' => ''
	);
}

javascript( 'mammoth', 'mobile-marketing/page' );

$selected = "mobile_marketing";
$sub_title = ( $mobile_page_id || $success ) ? _('Edit Page') : _('Add Page');
$title = "$sub_title | " . _('Mobile Marketing') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo $sub_title; ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'mobile-marketing/', 'website' ); ?>
	<div id="subcontent">
		<?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo ( $mobile_page_id ) ? _('Your page has been updated successfully!') : _('Your page has been added successfully!'); ?></p>
			<p><?php echo '<a href="/mobile-marketing/website/" title="', _('Pages'), '">', _('Click here to view your pages'), '</a>.'; ?></p>
		</div>
		<?php 
		}
		
		// Allow them to edit the entry they just created
		if ( $success && !$mobile_page_id )
			$mobile_page_id = $success;
		
		if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		?>
		<form name="fAddEditPage" action="/mobile-marketing/website/add-edit/<?php if ( $mobile_page_id ) echo "?mpid=$mobile_page_id"; ?>" method="post">
			<div id="dTitleContainer">
                <input name="tTitle" id="tTitle" class="tb" value="<?php echo ( !$success && isset( $_POST['tTitle'] ) ) ? $_POST['tTitle'] : $page['title']; ?>" tmpval="<?php echo _('Page Title...'); ?>" />
            </div>
            <?php if ( 'home' != $page['slug'] ) { ?>
            <div id="dSlug">
            	<span><strong><?php echo _('Link:'); ?></strong> http://m.<?php echo str_replace( 'www.', '', url::domain( $user['website']['domain'] ) ); ?>/<span id="sSlug"><?php $slug = ( !$success && isset( $_POST['tSlug'] ) ) ? $_POST['tSlug'] : $page['slug']; echo $slug; ?></span><input type="text" name="tSlug" id="tSlug" maxlength="50" class="tb hidden" value="<?php echo $slug; ?>" />/</span>
                &nbsp;
                <a href="javascript:;" id="aCancelSlug" title="Cancel" class="hidden"><?php echo _('Cancel'); ?></a>
                <a href="javascript:;" id="aEditSlug" title="<?php echo _('Edit Link'); ?>"><?php echo _('Edit'); ?></a>&nbsp;
                <a href="javascript:;" id="aSaveSlug" title="<?php echo _('Save Link'); ?>" class="button hidden round"><?php echo _('Save'); ?></a>
            </div>
            <?php } ?>
            <br />
            <textarea name="taContent" id="taContent" cols="50" rows="3" rte="1"><?php echo ( !$success && isset( $_POST['taContent'] ) ) ? $_POST['taContent'] : $page['content']; ?></textarea>
            <br />

            <p><input type="submit" id="bSubmit" value="<?php echo _('Save'); ?>" class="button" /></p>
            <?php nonce::field( 'add-edit-page' ); ?>
		</form>
		<br /><br />
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>