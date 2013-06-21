<?php
/**
 * @package Grey Suit Retail
 * @page Browser | Knowledge Base
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

?>

<div id="content">
    <div id="kb-search">
        <form name="fKBSearch" action="/kb/search/">
            <img src="/images/kb/search.png" width="48" height="35">
            <input type="text" id="kbs" name="kbs" tmpval="<?php echo _('Enter a question or keyword to search'); ?>">
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
            <h1><?php echo _('Browser Trouble? We can help.'); ?></h1>
            <p><?php echo _("If you're not seeing what you expect in one of our applications, these quick fixes should get you back on track."); ?></p>
            <br /><br />
            <br /><br />
            <p><?php echo _('This platform requires any of these web browsers:'); ?></p>
            <p><small><?php echo _('Click to download any web browser.'); ?></small></p>
            <div class="browsers">
                <br class="clr"><br>
                <div id="ie" class="browser">
                    <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie" class="img" title="<?php echo _('Download Internet Explorer'); ?>" target="_blank"><img src="/images/kb/icons/ie.png" width="60" height="60" alt="<?php echo _('Internet Explorer'); ?>"></a>
                    <br />
                    <a href="http://windows.microsoft.com/en-us/internet-explorer/download-ie" title="<?php echo _('Download Internet Explorer'); ?>" target="_blank"><?php echo _('Internet Explorer'); ?></a>
                    <p class="version"><?php echo _('Version 9'); ?> +</p>
                </div>
                <div id="firefox" class="browser">
                    <a href="http://www.mozilla.org/en-US/firefox/new/" class="img" title="<?php echo _('Download Mozilla Firefox'); ?>" target="_blank"><img src="/images/kb/icons/firefox.png" width="64" height="62" alt="<?php echo _('Mozilla Firefox'); ?>"></a>
                    <br />
                    <a href="http://www.mozilla.org/en-US/firefox/new/" title="<?php echo _('Download Mozilla Firefox'); ?>" target="_blank"><?php echo _('Mozilla Firefox'); ?></a>
                    <p class="version"><?php echo _('Version 3'); ?> +</p>
                </div>
                <div id="chrome" class="browser">
                    <a href="https://www.google.com/intl/en/chrome/browser/" class="img" title="<?php echo _('Download Google Chrome'); ?>" target="_blank"><img src="/images/kb/icons/chrome.png" width="62" height="60" alt="<?php echo _('Google Chrome'); ?>"></a>
                    <br />
                    <a href="https://www.google.com/intl/en/chrome/browser/" title="<?php echo _('Download Google Chrome'); ?>" target="_blank"><?php echo _('Google Chrome'); ?></a>
                    <p class="version"><?php echo _('Version 5'); ?> +</p>
                </div>
                <div id="safari" class="browser">
                    <a href="http://support.apple.com/downloads/#safari" class="img" title="<?php echo _('Download Apple Safari'); ?>" target="_blank"><img src="/images/kb/icons/safari.png" width="58" height="64" alt="<?php echo _('Apple Safari'); ?>"></a>
                    <br />
                    <a href="http://support.apple.com/downloads/#safari" title="<?php echo _('Download Apple Safari'); ?>" target="_blank"><?php echo _('Apple Safari'); ?></a>
                    <p class="version"><?php echo _('Version 3'); ?> +</p>
                </div>
                <br class="clr">
            </div>
            <br><br>
            <hr>
            <br class="clr" />
        </div>
    </div>
</div>

<?php echo $template->end(0); ?>