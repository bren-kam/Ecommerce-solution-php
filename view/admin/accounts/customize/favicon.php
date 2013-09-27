<?php
/**
 * @package Grey Suit Retail
 * @page Favicon | Customize | Account
 *
 
 */

echo $template->start( _('Favicon'),false );
?>
<form name="fTop" action="/accounts/customize/favicon/" method="post">
    <table class="width-auto">
        
        <tr>
            <td class="top" width="20%"><label for="fFavicon"><?php echo _('Favicon'); ?></label></td>
            <td>
                <div id="dLogoContent">
                     
                    <?php if ( !empty( $favicon ) ) { ?>
                    <img src="<?php echo $favicon; ?>" alt="<?php echo _('Favicon'); ?>" style="padding-bottom: 10px;" />
                    <br />
                    <?php } ?>
                </div>
                <a href="#" id="aUploadFavicon" class="button" title="<?php echo _('Upload'); ?>"><?php echo _('Upload'); ?></a>
                <div class="hidden-fix position-absolute" id="upload-favicon"></div>
                <input type="hidden" value="<?php echo $_GET["aid"] ?>" name="aid" id="aid" />
                <?php nonce::field( 'upload_logo', '_upload_favicon' ); ?>
            </td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>
            <td>&nbsp;</td>
            
        </tr>
    </table>
    <?php nonce::field( 'favicon' ); ?>
</form>
<?php echo $template->end(); ?>