<?php
/**
 * @package Grey Suit Retail
 * @page Mobile Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var bool $logged_in
 */

$link = ' - <a href="/mobile-marketing/website/" title="' . _('Mobile Marketing - Website') . '" class="small">(' .  _('Mobile Marketing Website') . ')</a>';
echo $template->start( _('Mobile Marketing'). $link, false );

if ( $logged_in ) {
    ?>
    <p align="center" id="loading"><?php echo _('Loading...'); ?></p>
    <iframe src="/mobile-marketing/trumpia-form/" width="100%" height="600" id="iframe" class="hidden"></iframe>
    <script type="text/javascript">
        setTimeout( function() {
            $('#iframe').attr( 'src', 'http://greysuitmobile.com/main.php' );

            setTimeout( function() {
                $('#loading').remove();
                $('#iframe').show();
             }, 2000 );
        }, 2000 );
    </script>
<?php } else { ?>
    <p><?php echo _('Mobile Marketing setup has not been completed. Please contact your online specialist for assistance'); ?></p>
<?php }

echo $template->end();
?>