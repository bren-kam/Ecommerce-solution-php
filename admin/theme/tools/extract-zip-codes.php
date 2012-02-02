<?php
/**
 * @page Tools - Extract Zip Codes
 * @package Imagine Retailer
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Define variables
$success = false;

// Reformat the excel into what we need
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'extract-zip-codes' ) && !empty( $_POST['taText'] ) ) {
    // Success
    $success = true;

    $zip_codes_text = preg_replace( '/\s*([0-9]{5}).*/', '$1' . "\n", $_POST['taText'] );
    $zip_codes_array = array_unique( explode( "\n", $zip_codes_text ) );
    $zip_codes = implode( "\n", $zip_codes_array );
}

$title = _('Extract Zip Codes') . ' | ' . _('Tools') . ' | ' . TITLE;

get_header();
?>

<div id="content">
	<h1><?php echo _('Extract Zip Codes'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'tools/' ); ?>
	<div id="subcontent">
        <?php if ( $success ) { ?>
            <p><?php echo _('Copy and paste the zip codes below.'); ?></p>
            <br />
            <textarea rows="20" cols="100"><?php echo $zip_codes; ?></textarea>
        <?php } else { ?>
        <form name="fExtractZipCodes" method="post" action="">
            <p><?php echo _('Copy and paste zip codes into the editor below.'); ?></p>
            <br />
            <p><textarea rows="20" cols="100" name="taText" id="taText"></textarea></p>
            <br />
            <p><input type="submit" class="button" value="<?php echo _('Extract'); ?>" /></p>
            <?php nonce::field('extract-zip-codes'); ?>
        </form>
        <?php } ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>