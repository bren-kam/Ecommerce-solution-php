<?php
/**
 * @page Tools - Bad Excel
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Define variables
$success = false;

// Reformat the excel into what we need
if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'bad-excel' ) && !empty( $_POST['taBadExcel'] ) ) {
    // Success
    $success = true;

    // Split into an array
    $rows = explode( "\n", $_POST['taBadExcel'] );

    // Define variables
    $skus = '';

    // Loop through array and find the value
    foreach ( $rows as $row ) {
        // Eliminate any extra space
        $row = trim( $row );

        // If they are the base of a SKU, or not
        if ( '-' == $row[0] ) {
            // Add it to the SKUs
            $skus .= $sku_base . $row . "\n";
        } else {
            // Base of SKUs
            $sku_pieces = explode( ' ', $row );
            $sku_base = $sku_pieces[0];
        }
    }
}

$title = _('Bad Excel') . ' | ' . _('Tools') . ' | ' . TITLE;

get_header();
?>

<div id="content">
	<h1><?php echo _('Bad Excel'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'tools/' ); ?>
	<div id="subcontent">
        <?php if ( $success ) { ?>
            <p><?php echo _('Copy and paste the SKUs below into the add bulk'); ?></p>
            <br />
            <textarea rows="20" cols="100"><?php echo $skus; ?></textarea>
        <?php } else { ?>
        <form name="fBadExcel" method="post" action="">
            <p><?php echo _('Copy and paste Column A and B into the editor below.'); ?></p>
            <br />
            <p><textarea rows="20" cols="100" name="taBadExcel" id="taBadExcel"></textarea></p>
            <br />
            <p><input type="submit" class="button" value="<?php echo _('Reformat'); ?>" /></p>
            <?php nonce::field('bad-excel'); ?>
        </form>
        <?php } ?>
	</div>
	<br /><br />
</div>

<?php get_footer(); ?>