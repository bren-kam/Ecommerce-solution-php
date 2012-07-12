<?php
/**
 * @page Posting
 * @package Grey Suit Retail
 */

global $user;

// Instantiate Classes
$fb = new FB( '268649406514419', '6ca6df4c7e9d909a58d95ce7360adbf3', 'op-posting', false, array( 'scope' => 'manage_pages,offline_access,publish_stream' ) );
$p = new Posting;
$v = new Validator;

// Get User
$user = $fb->user;

// Set Validation
$v->add_validation( 'tFBConnectionKey', 'req', _('The "Facebook Connection Key" field is required') );

// Make sure it's a valid request
if ( nonce::verify( $_POST['_nonce'], 'connect-to-field' ) ) {
	$errs = $v->validate();

	// if there are no errors
	if( empty( $errs ) )
		$success = $p->connect( $user, $_POST['sFBPageID'], $_POST['tFBConnectionKey'], $fb->getAccessToken() );
}

// See if we're connected
$connected = $p->connected( $user );

// Connected pages
$pages = ( $connected ) ? $p->get_connected_pages( $user ) : array();
	

// Get the accounts of the user
$accounts = $fb->api( "/$user/accounts" );

add_footer('<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({appId: "268649406514419", status: true, cookie: true,
             xfbml: true});
	FB.setSize({ width: 720, height: 500 });
  };
  (function() {
    var e = document.createElement("script"); e.async = true;
    e.src = document.location.protocol +
      "//connect.facebook.net/en_US/all.js";
    document.getElementById("fb-root").appendChild(e);
  }());
</script>');
$selected = 'posting';

$title = _('Posting') . ' | ' . _('Online Platform');
get_header('facebook/');
?>

<div id="header">
    <div id="logo">
        <a href="http://www.greysuitapps.com/" title="Grey Suit Apps"><img src="http://www.greysuitapps.com/fb/images/trans.gif" alt="Grey Suit Apps" /></a>
    </div><!-- #logo -->
        
	<?php if( $success && $website ) { ?>
	<div class="success">
		<p><?php echo _('Your information has been successfully updated!'); ?></p>
	</div>
	<?php 
	}
	
	if( isset( $errs ) )
		echo "<p class='error'>$errs</p>"; ?>
	<div id="nav">
            <ul>
                <li id="nav-apps"><a id="aTabApps" class="fb-tab" href="#" title="Apps"><img src="http://www.greysuitapps.com/fb/images/trans.gif" alt="Apps" /></a></li>
                <li id="nav-pricing"><a id="aTabPricing" class="fb-tab" href="#" title="Pricing"><img src="http://www.greysuitapps.com/fb/images/trans.gif" alt="Pricing" /></a></li>
                <!-- <li id="nav-faqs"><a id="aTabFaqs" class="fb-tab" href="#" title="FAQs"><img src="http://www.greysuitapps.com/fb/images/trans.gif" alt="FAQs" /></a></li> -->
            </ul>
        </div><!-- #nav -->
    </div><!-- #header -->
    <div id="content">
    	<div id="apps" class="fb-tab-wrapper">
	        <div id="apps-header">
	            <div id="apps-header-1">
	                <h2>1. Purchase the Apps</h2>
	                <p>You'll be redirected to GreySuitApps.com<br />
	                to choose your monthly plan.</p>
	            </div>
	            
	            <div id="apps-header-2">
	                <h2>2. Return to Facebook</h2>
	                <p>After you make your purchase you'll<br />
	                head back here to begin.</p>
	            </div>
	            
	            <div id="apps-header-3">
	                <h2>3. Get Started!</h2>
	                <p>Start using your new apps now to<br />
	                get your message out there!</p>
	            </div>
	        </div><!-- #apps-header -->
	        <div id="apps-container" class="clear">
	        	
	            <?php theme_inc( 'facebook/app-sidebar', true); ?>
	            
	            <div id="apps-icon">
                    <img src="http://www.greysuitapps.com/fb/images/icons/posting.png" alt="Posting" />
                </div>
                <div id="apps-desc">
                    <h1>Posting</h1>
                    <h3>Stop logging into your Facebook page to add a post every single day with our Posting Service app</h3>
                    <ul>
                        <li>Log into your dashboard and pick a time, date, then add your post and presto, your post will update like clockwork</li>
                        <li>Schedule your daily posts up to six months in advance</li>
                        <li>View your scheduled posts and even look at previous posts</li>
                        <li>Take the time out of daily posting to do what you do best, making money</li>
                    </ul>
                    <p><a href="#" onclick="top.location.href='http://www.greysuitapps.com/pricing/'" title="Purchase this App"><img src="http://www.greysuitapps.com/fb/images/buttons/purchase-app.png" alt="Purchase this App" /></a></p>
	
                <?php if( $connected ) { ?>
                    <p class="success"><?php echo _('You are connected!'); ?></p>
                    <p><?php echo _('You can now sign into your dashboard to control the posting to your pages.'); ?></p>

                    <br /><br />
                    <p><?php echo _('You can connect another account with a different Facebook Connection Key.'); ?></p>
                <?php } ?>
                <form name="fConnect" method="post" action="/facebook/posting/">
                <table cellpadding="0" cellspacing="0">
                    <tr>
                        <td><label for="tFBConnectionKey"><?php echo _('Facebook Connection Key'); ?>:</label></td>
                        <td><input type="text" class="tb" name="tFBConnectionKey" id="tFBConnectionKey" value="" /></td>
                    </tr>
                    <tr>
                        <td><label for="sFBPageID"><?php echo _('Facebook Page'); ?>:</label></td>
                        <td>
                            <select name="sFBPageID" id="sFBPageID">
                                <?php
                                if ( is_array( $accounts['data'] ) )
                                foreach ( $accounts['data'] as $page ) {
                                    if ( 'Application' == $page['category'] || in_array( $page['id'], $pages ) )
                                        continue;
                                    ?>
                                    <option value="<?php echo $page['id']; ?>"><?php echo $page['name']; ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td><input type="submit" class="button" value="<?php echo _('Connect'); ?>" /></td>
                    </tr>
                </table>
                <?php nonce::field('connect-to-field'); ?>
                </form>
            </div>
        </div>
    </div><!-- #apps-container .clear -->
</div> <!-- #apps -->

<?php
theme_inc( 'facebook/price-tab', true );
theme_inc( 'facebook/faq-tab', true );
get_footer('facebook/');
?>