<?php
/**
 * @package Grey Suit Retail
 * @page Browser | Knowledge Base
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $domain
 */
?>

<div id="content">
    <div id="kb-search">
        <form name="fKBSearch" action="/kb/search/">
            <img src="/images/kb/search.png" width="48" height="35">
            <input type="text" id="kbs" name="kbs" placeholder="<?php echo _('Enter a question or keyword to search'); ?>">
            <input type="submit" id="kbs-button" value="<?php echo _('Search'); ?>">
        </form>
    </div>
    <div id="subcontent-wrapper">
        <div id="breadcrumb">
            <a href="/kb/" title="<?php echo _('Home'); ?>"><img src="/images/kb/icons/home.png" width="14" height="12" alt="<?php echo _('Home'); ?>"></a> >
            <span class="last"><?php echo _('Browser Support'); ?></span>
        </div>
        <div id="subcontent" class="browser-support">
            <img src="/images/kb/browser-background.png" id="browser-background">
            <h1><?php echo _('Browser Trouble?'); ?><br/><?php echo _('We can help.'); ?></h1>
            <p class="title"><?php echo _("If you're not seeing what you expect in one of our applications, these quick fixes should get you back on track."); ?></p>
            <br class="clr">
            <hr>
            <br><br>

            <section id="browser-cache">
                <h2><?php echo _("Empty your browser's cache"); ?></h2>
                <div class="col-3 float-left">
                    <p><?php echo _("Clearing your browser's cache can force recently changed web pages to show up properly. Each browser handles caching differently, so be sure to follow the correct steps for your particular browser."); ?></p>
                    <p><strong><?php echo _('Note'); ?>:</strong> <?php echo _('If your browser version is not listed here, Google has more comprehensive instructions covering older versions of these browsers.'); ?></p>
                </div>
                <div class="float-left" id="bc-tabs">
                    <div class="tabs">
                        <a href="#cache-firefox" class="tab selected"><?php echo _('Firefox'); ?></a> |
                        <a href="#cache-chrome" class="tab"><?php echo _('Chrome'); ?></a> |
                        <a href="#cache-safari" class="tab"><?php echo _('Safari'); ?></a> |
                        <a href="#cache-ie" class="tab"><?php echo _('Internet Explorer'); ?></a>
                    </div>

                    <ol id="cache-firefox">
                        <li><?php echo _("In your browser's menu bar, go to Tools &rarr; Clear Recent History."); ?></li>
                        <li><?php echo _('Under "Time Range to Clear" select "Everything."'); ?></li>
                        <li><?php echo _('Expand the "Details" section and check "Cache."'); ?></li>
                        <li><?php echo _('Click the "Clear Now" button.'); ?></li>
                    </ol>
                    
                    <ol id="cache-chrome" class="hidden">
                        <li><?php echo _("In your browser's toolbar go to Tools &rarr; Options &rarr; Under the Hood."); ?></li>
                        <li><?php echo _('Click the button labeled "Clear browsing data."'); ?></li>
                        <li><?php echo _('Select the checkboxes for the types of information that you want to remove.'); ?></li>
                        <li><?php echo _('Click "Clear browsing data."'); ?></li>
                    </ol>
                    
                    <ol id="cache-safari" class="hidden">
                        <li><?php echo _('In the Mac OS X menu bar, go to Safari &rarr; Empty Cache.'); ?></li>
                        <li><?php echo _('There is no Step 2.'); ?></li>
                    </ol>

                    <ol id="cache-ie" class="hidden">
                        <li><?php echo _("In your browser's menu bar go to Tools &rarr; Internet Options &rarr; General &rarr; Browsing History &rarr; Delete..."); ?></li>
                        <li><?php echo _('From this window delete the Temporary Internet Files.'); ?></li>
                        <li><?php echo _('Close this window, then select OK before exiting the browser.'); ?></li>
                    </ol>
                </div>
                <br class="clr">
            </section>
            <br><br>
            <hr>
            <br />
            <section id="browser-cookies">
                <h2><?php echo _("Remove stale cookies"); ?></h2>
                <div class="col-3 float-left">
                    <p><?php echo _("A cookie is a piece of text stored by your browser to help it remember your login information, site preferences, and more. If you are having problems with one of our sites, deleting your cookies will reset your preferences to their default values."); ?></p>
                    <p><strong><?php echo _('Note'); ?>:</strong> <?php echo _('On Firefox, Chrome, and Safari, it is possible to search for and delete only your 37signals cookies. On Internet Explorer you will have to delete all cookies.'); ?></p>
                </div>
                <div class="float-left" id="cookies-tabs">
                    <div class="tabs">
                        <a href="#cookie-firefox" class="tab selected"><?php echo _('Firefox'); ?></a> |
                        <a href="#cookie-chrome" class="tab"><?php echo _('Chrome'); ?></a> |
                        <a href="#cookie-safari" class="tab"><?php echo _('Safari'); ?></a> |
                        <a href="#cookie-ie" class="tab"><?php echo _('Internet Explorer'); ?></a>
                    </div>

                    <ol id="cookie-firefox">
                        <li><?php echo _('In your browser\'s menu bar, click on "Tools."'); ?></li>
                        <li><?php echo _('Click "Options..."'); ?></li>
                        <li><?php echo _('Click on the "Privacy" tab.'); ?></li>
                        <li><?php echo _('Click on "Show Cookies..."'); ?></li>
                        <li><?php echo _('Depending on the products you use, look for cookies ending in') . ' ' . $domain . '.'; ?></li>
                        <li><?php echo _('Select them and click "Remove Cookie."'); ?></li>
                        <li><?php echo _('Click "Close" to exit.'); ?></li>
                    </ol>

                    <ol id="cookie-chrome" class="hidden">
                        <li><?php echo _("In your browser's toolbar go to Tools &rarr; Options &rarr; Under the Hood."); ?></li>
                        <li><?php echo _('Content settings in the "Privacy" section.'); ?></li>
                        <li><?php echo _('On the "Cookies" tab, click Show cookies and other site data.'); ?></li>
                        <li><?php echo _("Click Close for the Cookies and Other Data dialog when you're done."); ?></li>
                    </ol>

                    <ol id="cookie-safari" class="hidden">
                        <li><?php echo _('In the Mac OS X menu bar, select "Safari."'); ?></li>
                        <li><?php echo _('Click "Preferences..."'); ?></li>
                        <li><?php echo _('On the "Security" tab, click "Show Cookies."'); ?></li>
                        <li><?php echo _('Click on "Show Cookies..."'); ?></li>
                        <li><?php echo _('Depending on the products you use, look for cookies ending in') . ' ' . $domain . '.'; ?></li>
                        <li><?php echo _('Select them and click "Remove."'); ?></li>
                        <li><?php echo _('Click "Done."'); ?></li>
                        <li><?php echo _('Close the Preferences box.'); ?></li>
                    </ol>

                    <ol id="cookie-ie" class="hidden">
                        <li><?php echo _('In your browser\'s menu bar, click on "Tools."'); ?></li>
                        <li><?php echo _('Click "Internet Options..."'); ?></li>
                        <li><?php echo _('Under "Temporary Internet Files" on the General Tab, click "Delete Cookies."'); ?></li>
                        <li><?php echo _('Click "Ok" on the dialog box that says, "Delete all cookies in the Temporary Internet Files Folder?"'); ?></li>
                        <li><?php echo _('Click "OK" to exit.'); ?></li>
                    </ol>

                </div>
                <br class="clr">
            </section>
            <br><br>
            <hr>
            <br />
            <section id="browsers">
                <h2><?php echo _("Make sure you're using a required web browser."); ?></h2>
                <br class="clr"><br>
                <div id="firefox" class="browser">
                    <a href="http://www.mozilla.org/en-US/firefox/new/" class="img" title="<?php echo _('Download Mozilla Firefox'); ?>" target="_blank"><img src="/images/kb/icons/firefox.png" width="64" height="62" alt="<?php echo _('Mozilla Firefox'); ?>"></a>
                    <br />
                    <a href="http://www.mozilla.org/en-US/firefox/new/" title="<?php echo _('Download Mozilla Firefox'); ?>" target="_blank"><?php echo _('Mozilla Firefox'); ?></a>
                    <p class="version"><?php echo _('Version 4'); ?> +<br /><small>(<?php echo _('Recommended'); ?>)</small></p>
                </div>
                <div id="chrome" class="browser">
                    <a href="https://www.google.com/intl/en/chrome/browser/" class="img" title="<?php echo _('Download Google Chrome'); ?>" target="_blank"><img src="/images/kb/icons/chrome.png" width="62" height="60" alt="<?php echo _('Google Chrome'); ?>"></a>
                    <br />
                    <a href="https://www.google.com/intl/en/chrome/browser/" title="<?php echo _('Download Google Chrome'); ?>" target="_blank"><?php echo _('Google Chrome'); ?></a>
                    <p class="version"><?php echo _('Version 7'); ?> +</p>
                </div>
                <div id="safari" class="browser">
                    <a href="http://support.apple.com/downloads/#safari" class="img" title="<?php echo _('Download Apple Safari'); ?>" target="_blank"><img src="/images/kb/icons/safari.png" width="58" height="64" alt="<?php echo _('Apple Safari'); ?>"></a>
                    <br />
                    <a href="http://support.apple.com/downloads/#safari" title="<?php echo _('Download Apple Safari'); ?>" target="_blank"><?php echo _('Apple Safari'); ?></a>
                    <p class="version"><?php echo _('Version 5'); ?> +</p>
                </div>
                <div id="ie" class="browser">
                    <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie" class="img" title="<?php echo _('Download Internet Explorer'); ?>" target="_blank"><img src="/images/kb/icons/ie.png" width="60" height="60" alt="<?php echo _('Internet Explorer'); ?>"></a>
                    <br />
                    <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie" title="<?php echo _('Download Internet Explorer'); ?>" target="_blank"><?php echo _('Internet Explorer'); ?></a>
                    <p class="version"><?php echo _('Version 9'); ?> +</p>
                </div>
                <br class="clr">
                <p class="note"><strong><?php echo _('Please note'); ?>:</strong> <?php echo _('We do not support Internet Explorer 8 or Opera. Please download one of the browsers above to continue using our products without hiccups.'); ?></p>
            </section>
            <br><br>
            <hr>
            <br />
            <section id="internet-connection">
                <h2><?php echo _("Check your internet connection"); ?></h2>
                <br class="clr"><br>
                <div class="col-4 float-left">
                    <h3><?php echo _('Can you access other major web sites?'); ?></h3>
                    <p><?php echo _("If you're having trouble accessing one of our sites, try visiting") . ' <a href="http://google.com" title="Google">Google</a>, <a href="http://yahoo.com" title="Yahoo">Yahoo</a>, ' . _('or') . ' <a href="http://apple.com" title="Apple">Apple</a> ' . _('before submitting a support request. If these sites fail, you may have a more serious connection issue &mdash; try our other connection tips to the right.'); ?></p>
                    <p><?php echo _("Assuming those major sites show up fine, it's time to verify whether other people can access our products. To look into that, be sure to visit") . ' <a href="http://downforeveryoneorjustme.com/" title="' . _('Down For Everyone Or Just Me') . '">' . _('Down For Everyone Or Just Me') . '</a>.'; ?></p>
                    <p><?php echo _('Just you? The best explanation could be a DNS issue. DNS servers figure out how internet domain names (like') . " $domain) " . _('map to server IP addresses. Try switching to') . ' <a href="http://code.google.com/speed/public-dns/" title="' . _('Google Public DNS') . '">' . _('Google Public DNS') . '</a> ' . _('or') . ' <a href="http://opendns.com" title="' . _('Open DNS') . '">' . _('Open DNS') . '</a> ' . _('to see if that resolves the issue.'); ?></p>
                </div>
                <div class="col-4 float-left">
                    <h3><?php echo _('Try another browser'); ?></h3>
                    <p><?php echo _('Extensions or other software may occasionally corrupt your browser, leading to unexpected behavior. Before making changes to your primary browser, try accessing the internet with another browser like Chrome or Firefox.'); ?></p>
                    <br />
                    <h3><?php echo _('Check your anti-virus and firewall settings'); ?></h3>
                    <p><?php echo _("Still having issues no matter which browser you're using? Try disabling your anti-virus software &mdash; corrupt or partially-uninstalled software can break your access to the internet. Also try disabling your firewall temporarily to see if that helps."); ?></p>
                    <p><?php echo _("If you continue experiencing issues even with anti-virus and firewall disabled, it's time to check your connection to the network."); ?></p>
                </div>
                <div class="col-4 float-left">
                    <h3><?php echo _("On Wi-Fi? Make sure you're connected"); ?></h3>
                    <p><?php echo _("Wireless hotspots are great, but they're not always reliable. If you're using wi-fi but having connection issues, start by verifying your connection to the base station. Are you connected to the base station you expect or did your computer pick up someone else's signal? Did you enter the correct password?"); ?></p>
                    <p><?php echo _("If you're on the correct base station and the password checks out, try looking into your signal strength. If it's relatively weak, try moving closer to the hotspot and try accessing the web again."); ?></p>
                    <p><?php echo _("Good signal but still having issues? It may be time to restart the base station and try again in a few minutes."); ?></p>
                </div>
                <div class="col-4 float-left">
                    <h3><?php echo _('Restart your modem and your router'); ?></h3>
                    <p><?php echo _("When all else fails, it's time to check the hardware that connects you to the internet in the first place. If you can access the modem and the router, you'll need to disconnect them from their power source (the power button often just puts them in standby)."); ?></p>
                    <p><?php echo _("Once you've powered down the modem and router, wait 10-15 seconds, then plug the modem back in, followed by the router. Allow time for both to boot up and connect by watching the activity lights, then try connecting to the internet again on your computer."); ?></p>
                </div>
                <br class="clr">
            </section>
            <br><br>
            <hr>
            <br />
            <p id="help"><?php echo _('Still have questions?'); ?> <a href="#" class="support-ticket" title="<?php echo _('Support Request'); ?>"><?php echo _('Submit a support request'); ?></a>. <?php echo _("We'll get back to you as soon as possible."); ?></p>
            <br class="clr" />
        </div>
    </div>
</div>

<?php echo $template->end(0); ?>