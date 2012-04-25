<?php
/**
 * @page Email Templates
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$w = new Websites;

$v = new Validator();

$v->form_name = 'fAddTemplate';
$v->add_validation( 'sWebsiteID', 'req', 'The "Website" field is required' );

$v->add_validation( 'tViewProductButton', 'req', 'The "View Product Button" field is required' );
$v->add_validation( 'tViewProductButton', 'url', 'The "View Product Button" field must contain a valid URL' );

$v->add_validation( 'tProductColor', 'req', 'The "Product Color" field is required' );
$v->add_validation( 'tProductColor', 'custom=[0-9a-fA-F]{3,6}', 'The "Product Color" must contain a valid hex number' );

$v->add_validation( 'tPriceColor', 'req', 'The "Price Color" field is required' );
$v->add_validation( 'tPriceColor', 'custom=[0-9a-fA-F]{3,6}', 'The "Price Color" must contain a valid hex number' );

$v->add_validation( 'taDefaultTemplate', 'req', 'The "Default Template" field is required' );
$v->add_validation( 'taProductTemplate', 'req', 'The "Product Template" field is required' );

$v->add_validation( 'tTemplateImage', 'req', 'The "Template Image" field is required' );
$v->add_validation( 'tTemplateImage', 'url', 'The "Template Image" field must contain a valid URL' );

$v->add_validation( 'tTemplateImageThumbnail', 'req', 'The "Template Image Thumbnail" field is required' );
$v->add_validation( 'tTemplateImageThumbnail', 'url', 'The "Template Image Thumbnail" field must contain a valid URL' );

// Set to false
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'add-template' ) ) {
	$errs = $v->validate();
	
	// echo stripslashes( $_POST['taProductTemplate'] ); exit;
	if ( empty( $errs ) ) {
		$em = new Emails();
		$success = $em->add_template( $_POST['sWebsiteID'], $_POST['tViewProductButton'], $_POST['tProductColor'], $_POST['tPriceColor'], $_POST['taDefaultTemplate'], $_POST['taProductTemplate'], $_POST['tTemplateImage'], $_POST['tTemplateImageThumbnail'] );
	}
}

$websites = $w->list_websites( " AND a.`status` = 1", "a.`title`", 300 );
$form_validation = $v->js_validation();

css( 'form', 'emails/add_template' );
javascript( 'validator', 'jquery' );

$selected = 'add';
$title = _('Add Email Template') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Add Email Template'); ?></h1>
	<?php nonce::field( 'add-template', '_ajax_add_template' ); ?>
	<br clear="all" /><br />
	<?php get_sidebar( 'emails/' ); ?>
	<div id="subcontent">
		<?php 
		if ( !$success ) {
			$main_form_class = '';
			$success_class = ' class="hidden"';
			
			if ( isset( $errs ) )
				echo "<p class='red'>$errs</p>";
		} else {
			$success_class = '';
			$main_form_class = ' class="hidden"';
		}
		?>
		<div id="dMainForm"<?php echo $main_form_class; ?>>
			<?php
			if ( isset( $errs ) && !empty( $errs ) ) {
				$error_message = '';
				
				foreach ( $errs as $e ) {
					$error_message .= ( !empty( $error_message ) ) ? "<br />$e" : $e;
				}
				
				echo "<p class='red'>$error_message</p>";
			}
			?>
			<form action="/emails/" name="fAddTemplate" id="fAddTemplate" method="post">
			<table cellpadding="0" cellspacing="0">
                <tr>
                    <td><label for="sWebsiteID">Website <span class="red">*</span></label></td>
                    <td>
                        <select name="sWebsiteID" id="sWebsiteID">
                            <option value="">-- Select a Website --</option>
                            <?php 
                            foreach ( $websites as $website ) { 
                                $selected = ( !$success && isset( $_POST['sWebsiteID'] ) && $_POST['sWebsiteID'] == $website['website_id'] ) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?php echo $website['website_id']; ?>"<?php echo $selected; ?>><?php echo $website['title']; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="tViewProductButton">View Product Button <span class="red">*</span></label></td>
                    <td><input type="text" name="tViewProductButton" id="tViewProductButton" maxlength="200" value="<?php if ( !$success && isset( $_POST['tViewProductButton'] ) ) echo $_POST['tViewProductButton']; ?>" /></td>
                </tr>
                <tr>
                    <td><label for="tProductColor">Product Color: <span class="red">*</span></label></td>
                    <td><input type="text" name="tProductColor" id="tProductColor" maxlength="6" value="<?php if ( !$success && isset( $_POST['tProductColor'] ) ) echo $_POST['tProductColor']; ?>" /></td>
                </tr>
                <tr>
                    <td><label for="tPriceColor">Price Color: <span class="red">*</span></label></td>
                    <td><input type="text" name="tPriceColor" id="tPriceColor" maxlength="6" value="<?php if ( !$success && isset( $_POST['tPriceColor'] ) ) echo $_POST['tPriceColor']; ?>" /></td>
                </tr>
                <tr>
                    <td valign="top"><label for="taDefaultTemplate">Default Template: <span class="red">*</span></label></td>
                    <td><textarea name="taDefaultTemplate" id="taDefaultTemplate" cols="50" rows="3"><?php if ( !$success && isset( $_POST['taDefaultTemplate'] ) ) echo $_POST['taDefaultTemplate']; ?></textarea></td>
                </tr>
                <tr>
                    <td valign="top"><label for="taProductTemplate">Product Template: <span class="red">*</span></label></td>
                    <td><textarea name="taProductTemplate" id="taProductTemplate" cols="50" rows="3"><?php if ( !$success && isset( $_POST['taProductTemplate'] ) ) echo $_POST['taProductTemplate']; ?></textarea></td>
                </tr>
                <tr>
                    <td><label for="tTemplateImage">Template Image <span class="red">*</span></label></td>
                    <td><input type="text" name="tTemplateImage" id="tTemplateImage" maxlength="200" value="<?php if ( !$success && isset( $_POST['tTemplateImage'] ) ) echo $_POST['tTemplateImage']; ?>" /></td>
                </tr>
                <tr>
                    <td><label for="tTemplateImageThumbnail">Template Image Thumbnail <span class="red">*</span></label></td>
                    <td><input type="text" name="tTemplateImageThumbnail" id="tTemplateImageThumbnail" maxlength="200" value="<?php if ( !$success && isset( $_POST['tTemplateImageThumbnail'] ) ) echo $_POST['tTemplateImageThumbnail']; ?>" /></td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" class="button" value="Add Template" /></td>
                </tr>
                <?php nonce::field( 'add-template' ); ?>
            </table>				
			</form>
			<?php add_footer( $v->js_validation() ); ?>
		</div>
        <div id="dSuccess"<?php echo $success_class; ?>>
            <p><?php echo _('Email template has been successfully added!'); ?></p>
            <p><?php echo _('Click here to <a href="/emails/" id="aCreateAnother" title="Add a Template">add another template</a>.'); ?></p>
        </div>
	</div>
	<br /><br />
</div>
<br clear="all" />
<?php // get_footer(); ?>