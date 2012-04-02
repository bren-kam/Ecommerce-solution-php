<?php
/**
 * @page Craigslist Accounts Link Market
 * @package Grey Suit Retail
 */

// Get current user
global $user;

// If user is not logged in
if ( !$user )
	login();

// Must have a websit e_ID
if ( !isset( $_GET['wid'] ) )
    url::redirect('/craigslist/accounts/');

// Instantiate classes
$c = new Craigslist;
$w = new Websites;

$website_id = (int) $_GET['wid'];
$account = $c->get_account( $website_id );
$market_links = $c->get_market_links( $website_id );
$markets = $c->get_markets();
$addresses = @unserialize( $w->get_pagemeta_by_key( $website_id, 'addresses' ) );

if ( !$account['craigslist_customer_id'] )
    url::redirect('/craigslist/accounts/');

// Add validation
$v = new Validator();
$v->form_name = 'fLinkMarket';

$v->add_validation( 'sMarketID', 'req', _('The "Market" field is required') );

add_footer( $v->js_validation() );

// Initialize variable
$success = false;

if ( isset( $_POST['_nonce'] ) && nonce::verify( $_POST['_nonce'], 'link-market' ) ) {
	// Server side validation
	$errs = $v->validate();

    if ( empty( $errs ) ) {
        // Get the craigslist market
        $craigslist_market = $c->get_market( $_POST['sMarketID'] );
		$website = $w->get_website( $website_id );

        // Load the library
        library( 'craigslist-api' );

        // Create API object
        $craigslist_api = new Craigslist_API( config::key('craigslist-gsr-id'), config::key('craigslist-gsr-key') );
		
		// Create as many locations as we can, up to 10;
		if ( is_array( $addresses ) )
		foreach ( $addresses as $addr ) {
			$locations[] = $addr['city'] . ', ' . $addr['state'];
		}
		
		$locations[] = $craigslist_market['market'];
		
		if ( !empty( $craigslist_market['area'] ) )
			$locations[] = $craigslist_market['city'] . ', ' . $craigslist_market['state'];
		
		$locations[] = $craigslist_market['city'];
		$locations[] = $craigslist_market['city'] . ' Area';
		
		// Finalize the locations to 10 at the max
		$locations = array_slice( array_unique( $locations ), 0, 10 );

        $locations =

		// Add the store link
		if ( 1 == $website['pages'] && !empty( $website['domain'] ) ) {
			$url = 'http://' . ( ( $user['website']['subdomain'] != '' ) ? $user['website']['subdomain'] . '.' : '' ) . $website['domain'] . '/';
			$store['storelink'] = $url;
		}
		
		// See if they have a remote logo
		$remote_logo = stristr( $website['logo'], 'http' );
		
		// Add a store logo if they have one
		if ( !empty( $website['logo'] ) && ( $remote_logo || isset( $url ) ) ) {
			$logo = ( $remote_logo ) ? $website['logo'] :  "{$url}custom/uploads/images/" . $website['logo'];
			$store['storelogo'] = $logo;
		}
		
		// Set the store name -- everyone should have one of these
		$store['storename'] = $website['title'];
		
		// Set the phone if they have one
		if ( !empty( $website['phone'] ) )
			$store['storephone'] = $website['phone'];
		
        // Get the market id
        $market_id = $craigslist_api->add_market( $account['craigslist_customer_id'], $craigslist_market['market'], $locations, $store );
		
        // Link it in our database
        if ( $market_id )
            $success = $c->link_market( $account['website_id'], $_POST['sMarketID'], $market_id );
    }
}

javascript( 'validator' );

$selected = 'craigslist';
$title = _('Link Market') . ' | ' . _('Accounts') . ' | ' . _('Craigslist') . ' | ' . TITLE;
get_header();
?>

<div id="content">
	<h1><?php echo _('Link Market'); ?></h1>
	<br clear="all" /><br />
	<?php get_sidebar( 'craigslist/', 'accounts' ); ?>
    
	<div id="subcontent">
        <?php if ( $success ) { ?>
		<div class="success">
			<p><?php echo _('Your market has been linked successfully!'); ?></p>
			<br />
            <p><?php echo _('Click here to'), ' <a href="/craigslist/accounts/" title="', _('Accounts'), '">', _('view your accounts'), '</a> or <a href="/craigslist/accounts/link-market/" title="', _('Link Market'), '">', _('link another market'), '</a>.'; ?></p>
			<br />
		</div>
		<?php 
		}
		
		if ( isset( $errs ) )
            echo "<p class='red'>$errs</p><br />";
        ?>
        <p><?php echo _('At this point in time you cannot modify or edit a market once added. Please add carefully. If you have trouble, please submit a ticket.'); ?></p>
		<br />
		<form name="fLinkMarket" id="fLinkMarket" action="/craigslist/accounts/link-market/<?php echo "?wid=$website_id"; ?>" method="post">
		    <table cellpadding="0" cellspacing="0">
                <tr>
                    <td><label><?php echo _('Account'); ?>:</label></td>
                    <td><?php echo $account['title']; ?></td>
                </tr>
				<tr>
                    <td><label><?php echo _('Linked Markets'); ?>:</label></td>
                    <td>
						<?php 
						if ( is_array( $market_links ) ) {
							foreach ( $market_links as $ml ) {
								echo "<p>$ml</p>";
							}
						} else {
							echo '<p>', _('You have not linked any markets yet.'), '</p>';
						}
						?>
					</td>
                </tr>
                <tr>
                    <td><label for="sMarketID"><?php echo _('Market'); ?>:</label></td>
                    <td>
                        <select name="sMarketID" id="sMarketID">
                            <option value="">--<?php echo _('Select a Market'); ?>--</option>
                            <?php
                            $plan = ( !$success && isset( $_POST['sPlan'] ) ) ? $_POST['sPlan'] : $plan;

                            if ( is_array( $markets ) )
                            foreach ( $markets as $m ) {
                                if ( array_key_exists( $m['craigslist_market_id'], $market_links ) )
                                    continue;
                            ?>
                                <option value="<?php echo $m['craigslist_market_id']; ?>"><?php echo $m['market']; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" class="button" value="<?php echo _('Link Market'); ?>" /></td>
                </tr>
		    </table>
            <?php nonce::field('link-market'); ?>
		</form>
		<br clear="all" />
	</div>
</div>

<?php get_footer(); ?>