<?php
/**
 * @page Create Trumpia Account
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

$website_id = (int) $_GET['wid'];

// Initiate Classes
$w = new Websites;
$m = new Mobile_Marketing();

$website = $w->get_website( $website_id );

if ( !$website )
    url::redirect('/accounts/');

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'create-trumpia-account' ) ) {
	$success = $m->create_trumpia_account( $_GET['wid'], $_POST['sLevel'] );

    if ( $success ) {
        url::redirect("/accounts/edit/?wid=$website_id");
    } else {
        $errs = _('An error occurred while trying to create the Mobile Marketing Account. Please try again.');
    }
}

css( 'form' );

$selected = 'accounts';
$title = _('Create Mobile Account') . ' | ' . _('Accounts') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Create Mobile Account'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'accounts/' ); ?>
	<div id="subcontent">
        <?php
        if ( !empty( $errs ) )
            echo "<p class='red'>$errs</p><br />";
        ?>
		<form action="/accounts/create-mobile-account/?wid=<?php echo $website_id; ?>" name="fCreateTrumpiaAcocunt" id="fCreateTrumpiaAcocunt" method="post">
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td><label for="sLevel"><?php echo _('Mobile Marketing Level'); ?></label></td>
                    <td>
                        <select name="sLevel" id="sLevel">
                            <?php
                            $levels = array( 1, 2, 3, 4, 5 );

                            foreach ( $levels as $level ) {
                                $value = "level-$level";
                                $name = _('Level') . ' ' . $level;

                                $selected = ( isset( $_POST['sLevel'] ) && $_POST['sLevel'] == $value || !isset( $_POST['sLevel'] ) && 2 == $level ) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?php echo $value; ?>"<?php echo $selected; ?>><?php echo $name; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" class="button" value="<?php echo _('Create Mobile Account'); ?>" /></td>
                </tr>
            </table>
		<?php nonce::field ( 'create-trumpia-account', '_nonce' ); ?>
		</form>
		<br clear="all" />
	</div>
</div>

<?php get_footer(); ?>