<?php
/**
 * @page Contact Us
 * @package Grey Suit Retail
 */
get_header('facebook/');
?>

    <div id="header">
        <div id="logo">
            <a href="http://www.greysuitapps.com/" title="Grey Suit Apps"><img src="https://www.greysuitapps.com/fb/images/trans.gif" alt="Grey Suit Apps" /></a>
        </div><!-- #logo -->
        
        
	<?php if( $success && $website ) { ?>
	<div class="success">
		<p><?php echo _('Your information has been successfully updated!'); ?></p>
	</div>
	<?php 
	}
	
	if( isset( $errs ) )
			echo "<p class='error'>$errs</p>";
	
	if( ! $page_id ) {
	?>
	
	<div id="nav">
            <ul>
                <li id="nav-apps"><a id="aTabApps" class="fb-tab" href="#" title="Apps"><img src="https://www.greysuitapps.com/fb/images/trans.gif" alt="Apps" /></a></li>
                <li id="nav-pricing"><a id="aTabPricing" class="fb-tab" href="#" title="Pricing"><img src="https://www.greysuitapps.com/fb/images/trans.gif" alt="Pricing" /></a></li>
                <!-- <li id="nav-faqs"><a id="aTabFaqs" class="fb-tab" href="#" title="FAQs"><img src="https://www.greysuitapps.com/fb/images/trans.gif" alt="FAQs" /></a></li> -->
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
	    	
	            <div id="apps-content" class="clear">
	            	<div id="apps-icon">
	                    <img src="https://www.greysuitapps.com/fb/images/icons/about.png" alt="<?php echo _('Online Platform - About Us'); ?>" />
	                </div>
	                <div id="apps-desc">
	                    <h1>About Us</h1>
	                    <h3>Tell your fans what makes you unique, how long you've been in business and what makes you cool!</h3>
	                    <ul>
	                        <li>Keep fans on your Facebook site and give them all the information they need to buy from you</li>
	                        <li>Give the search engines one more way to find you with this SEO friendly app</li>
	                        <li>Log into your dashboard, add any information and turn yourself into a rockstar</li>
	                    </ul>
	                    <p><a href="#" onclick="top.location.href='http://www.greysuitapps.com/pricing/'" title="Purchase this App"><img src="https://www.greysuitapps.com/fb/images/buttons/purchase-app.png" alt="Purchase this App" /></a></p>
	                    <p><a href="#" onclick="top.location.href='http://www.facebook.com/add.php?api_key=233746136649331&pages=1'"title="Install this App" class="install-app"><img src="https://www.greysuitapps.com/fb/images/trans.gif" alt="Install this App" /></a></p>
	                    <p class="sml-text">gives you acces to ALL apps</p>
	                </div>
	            </div>
	        </div><!-- #apps-container .clear -->
	    </div><!-- #install -->
        
        <?php theme_inc( 'facebook/price-tab', true ); ?>
        <?php theme_inc( 'facebook/faq-tab', true ); ?>

	<?php } else { ?>
	</div>
	<div id="content">
        <form name="fConnect" id="fConnect" method="post" action="/facebook/about-us/">
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td width="220" class="align-right"><strong><?php echo _('Website'); ?>:</strong></td>
                    <td><?php echo ( $website ) ? $website['title'] : 'N/A'; ?></td>
                </tr>
                <tr>
                    <td class="align-right"><label for="tFBConnectionKey"><?php echo _('Facebook Connection Key'); ?>:</label></td>
                    <td><input type="text" class="tb" name="tFBConnectionKey" id="tFBConnectionKey" value="<?php echo ( $website ) ? $website['key'] : ''; ?>" /> <strong><?php echo ( $website ) ? '<span class="success">(' . _('Connected') . ')</span>' : '<span class="error">(' . _('Not Connected') . ')</span>'; ?></strong></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" class="button" value="<?php echo _('Connect'); ?>" /></td>
                </tr>
            </table>
            <input type="hidden" name="app_data" value="<?php echo $_REQUEST['app_data']; ?>" />
            <?php nonce::field('connect-to-field'); ?>
        </form>
	<?php } ?>
</div>  <!-- #content -->

<?php get_footer('facebook/'); ?>