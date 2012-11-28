<?php
/**
 * @page Posting
 * @package Grey Suit Retail
 *
 * @var int $app_id
 * @var bool $success
 * @var array $website
 * @var string $form
 */

?>

<div id="header">
    <div id="logo">
        <a href="http://www.greysuitapps.com/" title="Grey Suit Apps"><img src="https://www.greysuitapps.com/fb/images/trans.gif" alt="Grey Suit Apps" /></a>
    </div><!-- #logo -->


    <?php if ( $success && $website ) { ?>
    <div class="success">
        <p><?php echo _('Your information has been successfully updated!'); ?></p>
    </div>
    <?php
    }

    if ( isset( $errs ) )
        echo "<p class='error'>$errs</p>";

    if ( !$page_id ) {
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

            <?php require VIEW_PATH . 'facebook/app-sidebar.php'; ?>

            <div id="apps-content" class="clear">
                <div id="apps-icon">
                    <img src="https://www.greysuitapps.com/fb/images/icons/posting.png" alt="Posting" />
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
                    <p><a href="#" onclick="top.location.href='http://www.greysuitapps.com/pricing/'" title="Purchase this App"><img src="https://www.greysuitapps.com/fb/images/buttons/purchase-app.png" alt="Purchase this App" /></a></p>
                </div>
            </div>
        </div><!-- #apps-container .clear -->
    </div><!-- #install -->

    <?php require VIEW_PATH . 'facebook/price-tab.php'; ?>
    <?php require VIEW_PATH . 'facebook/faq-tab.php'; ?>
    <?php } else { ?>
    </div> <!-- end #header -->
    <div id="content">
        <?php if( $connected ) { ?>
            <p class="success">You are connected!</p>
            <p>You can now sign into your dashboard to control the posting to your pages.</p>

            <br /><br />
            <p>You can connect another account with a different Facebook Connection Key.</p>
        <?php }
        echo $form;
    } ?>
</div>

<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({appId: "<?php echo $app_id; ?>", status: true, cookie: true,
             xfbml: true});
	FB.setSize({ width: 720, height: 500 });
  };
  (function() {
    var e = document.createElement("script"); e.async = true;
    e.src = document.location.protocol +
      "//connect.facebook.net/en_US/all.js";
    document.getElementById("fb-root").appendChild(e);
  }());
</script>