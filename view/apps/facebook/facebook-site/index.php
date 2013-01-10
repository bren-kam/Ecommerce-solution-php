<?php
/**
 * @page Facebook Site
 * @package Grey Suit Retail
 *
 * @var int $app_id
 * @var bool $success
 * @var array $website
 * @var string $form
 * @var int|bool $page_id
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
                    <img src="https://www.greysuitapps.com/fb/images/icons/fb-home.png" alt="<?php echo _('Facebook Site'); ?>" />
                </div>
                <div id="apps-desc">
                    <h1>Facebook Site</h1>
                    <p><a href="#" onclick="top.location.href='http://www.greysuitapps.com/pricing/'" title="Purchase this App"><img src="https://www.greysuitapps.com/fb/images/buttons/purchase-app.png" alt="Purchase this App" /></a></p>
                    <p><a href="#" onclick="top.location.href='http://www.facebook.com/add.php?api_key=<?php echo $app_id; ?>&pages=1'" title="Install this App" class="install-app"><img src="https://www.greysuitapps.com/fb/images/trans.gif" alt="Install this App" /></a></p>
                    <p class="sml-text">gives you access to ALL apps</p>
                </div>
            </div>
        </div><!-- #apps-container .clear -->
    </div><!-- #install -->

    <?php require VIEW_PATH . 'facebook/price-tab.php'; ?>
    <?php require VIEW_PATH . 'facebook/faq-tab.php'; ?>
    <?php } else { ?>
    </div> <!-- end #header -->
    <div id="content">
        <?php
        echo $form;
    }
    ?>
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