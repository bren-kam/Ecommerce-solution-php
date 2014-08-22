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

<div class="row-fluid">
    <div class="col-lg-12">
        <ul class="breadcrumb">
            <li><a href="/kb/"><i class="fa fa-home"></i></a></li>

            <li class="active">Browser Support</li>
        </ul>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body">

                <form class="form-inline" action="/kb/search" role="form">
                    <div class="form-group">
                        <input type="text" class="form-control" name="kbs" placeholder="Enter question or search..." />
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Search</button>
                    </div>
                </form>

            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <h1><?php echo _('Browser Trouble?'); ?><br/><?php echo _('We can help.'); ?></h1>
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <h3><?php echo _("If you're not seeing what you expect in one of our applications, these quick fixes should get you back on track."); ?></h3>
                    </div>
                    <div class="col-lg-6">
                        <img src="/images/kb/browser-background.png" class="pull-right"/>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <h3><i class="fa fa-trash-o"></i> <?php echo _("Empty your browser's cache"); ?></h3>
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <p><?php echo _("Clearing your browser's cache can force recently changed web pages to show up properly. Each browser handles caching differently, so be sure to follow the correct steps for your particular browser."); ?></p>
                        <p><strong><?php echo _('Note'); ?>:</strong> <?php echo _('If your browser version is not listed here, Google has more comprehensive instructions covering older versions of these browsers.'); ?></p>
                    </div>

                    <div class="col-lg-6">
                        <ul class="nav nav-pills">
                            <li class="active"><a href="#cache-firefox" data-toggle="tab">Firefox</a></li>
                            <li><a href="#cache-chrome" data-toggle="tab">Chrome</a></li>
                            <li><a href="#cache-safari" data-toggle="tab">Safari</a></li>
                            <li><a href="#cache-ie" data-toggle="tab">Internet Explorer</a></li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="cache-firefox">
                                <ol>
                                    <li><?php echo _("In your browser's menu bar, go to Tools &rarr; Clear Recent History."); ?></li>
                                    <li><?php echo _('Under "Time Range to Clear" select "Everything."'); ?></li>
                                    <li><?php echo _('Expand the "Details" section and check "Cache."'); ?></li>
                                    <li><?php echo _('Click the "Clear Now" button.'); ?></li>
                                </ol>
                            </div>
                            <div class="tab-pane" id="cache-chrome">
                                <ol>
                                    <li><?php echo _("In your browser's toolbar go to Tools &rarr; Options &rarr; Under the Hood."); ?></li>
                                    <li><?php echo _('Click the button labeled "Clear browsing data."'); ?></li>
                                    <li><?php echo _('Select the checkboxes for the types of information that you want to remove.'); ?></li>
                                    <li><?php echo _('Click "Clear browsing data."'); ?></li>
                                </ol>
                            </div>
                            <div class="tab-pane" id="cache-safari">
                                <ol>
                                    <li><?php echo _('In the Mac OS X menu bar, go to Safari &rarr; Empty Cache.'); ?></li>
                                    <li><?php echo _('There is no Step 2.'); ?></li>
                                </ol>
                            </div>
                            <div class="tab-pane" id="cache-ie">
                                <ol>
                                    <li><?php echo _("In your browser's menu bar go to Tools &rarr; Internet Options &rarr; General &rarr; Browsing History &rarr; Delete..."); ?></li>
                                    <li><?php echo _('From this window delete the Temporary Internet Files.'); ?></li>
                                    <li><?php echo _('Close this window, then select OK before exiting the browser.'); ?></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>


<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <h3><i class="fa fa-database"></i> <?php echo _("Remove stale cookies"); ?></h3>
            </header>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-6">
                        <p><?php echo _("A cookie is a piece of text stored by your browser to help it remember your login information, site preferences, and more. If you are having problems with one of our sites, deleting your cookies will reset your preferences to their default values."); ?></p>
                        <p><strong><?php echo _('Note'); ?>:</strong> <?php echo _('On Firefox, Chrome, and Safari, it is possible to search for and delete only your 37signals cookies. On Internet Explorer you will have to delete all cookies.'); ?></p>
                    </div>

                    <div class="col-lg-6">
                        <ul class="nav nav-pills">
                            <li class="active"><a href="#cookie-firefox" data-toggle="tab">Firefox</a></li>
                            <li><a href="#cookie-chrome" data-toggle="tab">Chrome</a></li>
                            <li><a href="#cookie-safari" data-toggle="tab">Safari</a></li>
                            <li><a href="#cookie-ie" data-toggle="tab">Internet Explorer</a></li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="cookie-firefox">
                                <ol>
                                    <li><?php echo _('In your browser\'s menu bar, click on "Tools."'); ?></li>
                                    <li><?php echo _('Click "Options..."'); ?></li>
                                    <li><?php echo _('Click on the "Privacy" tab.'); ?></li>
                                    <li><?php echo _('Click on "Show Cookies..."'); ?></li>
                                    <li><?php echo _('Depending on the products you use, look for cookies ending in') . ' ' . $domain . '.'; ?></li>
                                    <li><?php echo _('Select them and click "Remove Cookie."'); ?></li>
                                    <li><?php echo _('Click "Close" to exit.'); ?></li>
                                </ol>                            </div>
                            <div class="tab-pane" id="cookie-chrome">
                                <ol>
                                    <li><?php echo _("In your browser's toolbar go to Tools &rarr; Options &rarr; Under the Hood."); ?></li>
                                    <li><?php echo _('Content settings in the "Privacy" section.'); ?></li>
                                    <li><?php echo _('On the "Cookies" tab, click Show cookies and other site data.'); ?></li>
                                    <li><?php echo _("Click Close for the Cookies and Other Data dialog when you're done."); ?></li>
                                </ol>
                            </div>
                            <div class="tab-pane" id="cookie-safari">
                                <ol>
                                    <li><?php echo _('In the Mac OS X menu bar, select "Safari."'); ?></li>
                                    <li><?php echo _('Click "Preferences..."'); ?></li>
                                    <li><?php echo _('On the "Security" tab, click "Show Cookies."'); ?></li>
                                    <li><?php echo _('Click on "Show Cookies..."'); ?></li>
                                    <li><?php echo _('Depending on the products you use, look for cookies ending in') . ' ' . $domain . '.'; ?></li>
                                    <li><?php echo _('Select them and click "Remove."'); ?></li>
                                    <li><?php echo _('Click "Done."'); ?></li>
                                    <li><?php echo _('Close the Preferences box.'); ?></li>
                                </ol>
                            </div>
                            <div class="tab-pane" id="cookie-ie">
                                <ol>
                                    <li><?php echo _('In your browser\'s menu bar, click on "Tools."'); ?></li>
                                    <li><?php echo _('Click "Internet Options..."'); ?></li>
                                    <li><?php echo _('Under "Temporary Internet Files" on the General Tab, click "Delete Cookies."'); ?></li>
                                    <li><?php echo _('Click "Ok" on the dialog box that says, "Delete all cookies in the Temporary Internet Files Folder?"'); ?></li>
                                    <li><?php echo _('Click "OK" to exit.'); ?></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <h3><i class="fa fa-download"></i> <?php echo _("Make sure you're using a required web browser."); ?></h3>
            </header>
            <div class="panel-body">

                <div class="row" id="download-browser">
                    <div class="col-lg-3">
                        <a href="http://www.mozilla.org/en-US/firefox/new/" class="img" title="<?php echo _('Download Mozilla Firefox'); ?>" target="_blank"><img src="/images/kb/icons/firefox.png" width="64" height="62" alt="<?php echo _('Mozilla Firefox'); ?>"></a>
                        <br />
                        <a href="http://www.mozilla.org/en-US/firefox/new/" title="<?php echo _('Download Mozilla Firefox'); ?>" target="_blank"><?php echo _('Mozilla Firefox'); ?></a>
                        <p><?php echo _('Version 4'); ?> +<br /><small>(<?php echo _('Recommended'); ?>)</small></p>
                    </div>
                    <div class="col-lg-3">
                        <a href="https://www.google.com/intl/en/chrome/browser/" class="img" title="<?php echo _('Download Google Chrome'); ?>" target="_blank"><img src="/images/kb/icons/chrome.png" width="62" height="60" alt="<?php echo _('Google Chrome'); ?>"></a>
                        <br />
                        <a href="https://www.google.com/intl/en/chrome/browser/" title="<?php echo _('Download Google Chrome'); ?>" target="_blank"><?php echo _('Google Chrome'); ?></a>
                        <p class="version"><?php echo _('Version 7'); ?> +</p>
                    </div>
                    <div class="col-lg-3">
                        <a href="http://support.apple.com/downloads/#safari" class="img" title="<?php echo _('Download Apple Safari'); ?>" target="_blank"><img src="/images/kb/icons/safari.png" width="58" height="64" alt="<?php echo _('Apple Safari'); ?>"></a>
                        <br />
                        <a href="http://support.apple.com/downloads/#safari" title="<?php echo _('Download Apple Safari'); ?>" target="_blank"><?php echo _('Apple Safari'); ?></a>
                        <p class="version"><?php echo _('Version 5'); ?> +</p>
                    </div>
                    <div class="col-lg-3">
                        <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie" class="img" title="<?php echo _('Download Internet Explorer'); ?>" target="_blank"><img src="/images/kb/icons/ie.png" width="60" height="60" alt="<?php echo _('Internet Explorer'); ?>"></a>
                        <br />
                        <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie" title="<?php echo _('Download Internet Explorer'); ?>" target="_blank"><?php echo _('Internet Explorer'); ?></a>
                        <p class="version"><?php echo _('Version 9'); ?> +</p>
                    </div>
                </div>

                <div class="alert alert-info">
                    <strong><?php echo _('Please note'); ?>:</strong> <?php echo _('We do not support Internet Explorer 8 or Opera. Please download one of the browsers above to continue using our products without hiccups.'); ?>
                </div>

            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <h3><i class="fa fa-globe"></i> <?php echo _("Check your internet connection"); ?></h3>
            </header>
            <div class="panel-body">

                <div class="row">
                    <div class="col-lg-3">
                        <h3><?php echo _('Can you access other major web sites?'); ?></h3>
                        <p><?php echo _("If you're having trouble accessing one of our sites, try visiting") . ' <a href="http://google.com" title="Google">Google</a>, <a href="http://yahoo.com" title="Yahoo">Yahoo</a>, ' . _('or') . ' <a href="http://apple.com" title="Apple">Apple</a> ' . _('before submitting a support request. If these sites fail, you may have a more serious connection issue &mdash; try our other connection tips to the right.'); ?></p>
                        <p><?php echo _("Assuming those major sites show up fine, it's time to verify whether other people can access our products. To look into that, be sure to visit") . ' <a href="http://downforeveryoneorjustme.com/" title="' . _('Down For Everyone Or Just Me') . '">' . _('Down For Everyone Or Just Me') . '</a>.'; ?></p>
                        <p><?php echo _('Just you? The best explanation could be a DNS issue. DNS servers figure out how internet domain names (like') . " $domain) " . _('map to server IP addresses. Try switching to') . ' <a href="http://code.google.com/speed/public-dns/" title="' . _('Google Public DNS') . '">' . _('Google Public DNS') . '</a> ' . _('or') . ' <a href="http://opendns.com" title="' . _('Open DNS') . '">' . _('Open DNS') . '</a> ' . _('to see if that resolves the issue.'); ?></p>
                    </div>
                    <div class="col-lg-3">
                        <h3><?php echo _('Try another browser'); ?></h3>
                        <p><?php echo _('Extensions or other software may occasionally corrupt your browser, leading to unexpected behavior. Before making changes to your primary browser, try accessing the internet with another browser like Chrome or Firefox.'); ?></p>
                        <br />
                        <h3><?php echo _('Check your anti-virus and firewall settings'); ?></h3>
                        <p><?php echo _("Still having issues no matter which browser you're using? Try disabling your anti-virus software &mdash; corrupt or partially-uninstalled software can break your access to the internet. Also try disabling your firewall temporarily to see if that helps."); ?></p>
                        <p><?php echo _("If you continue experiencing issues even with anti-virus and firewall disabled, it's time to check your connection to the network."); ?></p>
                    </div>
                    <div class="col-lg-3">
                        <h3><?php echo _("On Wi-Fi? Make sure you're connected"); ?></h3>
                        <p><?php echo _("Wireless hotspots are great, but they're not always reliable. If you're using wi-fi but having connection issues, start by verifying your connection to the base station. Are you connected to the base station you expect or did your computer pick up someone else's signal? Did you enter the correct password?"); ?></p>
                        <p><?php echo _("If you're on the correct base station and the password checks out, try looking into your signal strength. If it's relatively weak, try moving closer to the hotspot and try accessing the web again."); ?></p>
                        <p><?php echo _("Good signal but still having issues? It may be time to restart the base station and try again in a few minutes."); ?></p>
                    </div>
                    <div class="col-lg-3">
                        <h3><?php echo _('Restart your modem and your router'); ?></h3>
                        <p><?php echo _("When all else fails, it's time to check the hardware that connects you to the internet in the first place. If you can access the modem and the router, you'll need to disconnect them from their power source (the power button often just puts them in standby)."); ?></p>
                        <p><?php echo _("Once you've powered down the modem and router, wait 10-15 seconds, then plug the modem back in, followed by the router. Allow time for both to boot up and connect by watching the activity lights, then try connecting to the internet again on your computer."); ?></p>
                    </div>
                </div>

            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body">
                <p>Still have questions? <a href="#" data-toggle="modal" data-target="#support-modal">Submit a support request</a>. We'll get back to you as soon as possible.</p>
            </div>
        </section>
    </div>
</div>
</div>