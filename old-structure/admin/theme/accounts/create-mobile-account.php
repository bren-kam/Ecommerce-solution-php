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
	$response = $m->create_trumpia_account( $_GET['wid'], $_POST['sMobilePlanID'] );

    if ( $response->success() ) {
        url::redirect("/accounts/edit/?wid=$website_id");
    } else {
        $errs = $response->message();
    }
}

css( 'form' );
$plans = $m->get_plans();

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
                    <td><label for="sMobilePlanID"><?php echo _('Mobile Marketing Plan'); ?></label></td>
                    <td>
                        <select name="sMobilePlanID" id="sMobilePlanID">
                            <?php
                            foreach ( $plans as $plan ) {
                                $selected = ( isset( $_POST['sMobilePlanID'] ) && $_POST['sMobilePlanID'] == $plan['mobile_plan_id'] || !isset( $_POST['sMobilePlanID'] ) && 2 == $plan['mobile_plan_id'] ) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?php echo $plan['mobile_plan_id']; ?>"<?php echo $selected; ?>><?php echo $plan['name']; ?></option>
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