<?php
/**
 * @package Grey Suit Retail
 * @page Logo and Phone | Settings | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Logo and Phone') );
?>
<form name="fTop" action="/settings/logo-and-phone/" method="post">
    <table class="width-auto">
        <tr>
            <td><label for="tPhone"><?php echo _('Phone Number'); ?></label></td>
            <td><input type="text" id="tPhone" name="tPhone" class="tb" value="<?php echo $user->account->phone; ?>" maxlength="20" /></td>
        </tr>
        <tr>
            <td class="top"><label for="fLogo"><?php echo _('Logo'); ?></label></td>
            <td>
                <div id="dLogoContent">
                    <?php if ( !empty( $user->account->logo ) ) { ?>
                    <img src="<?php echo $user->account->logo; ?>" alt="<?php echo _('Logo'); ?>" style="padding-bottom: 10px;" />
                    <br />
                    <?php } ?>
                </div>
                <a href="#" id="aUploadLogo" class="button" title="<?php echo _('Upload'); ?>"><?php echo _('Upload'); ?></a>
                <div class="hidden" id="upload-logo"></div>
                <?php nonce::field( 'upload_logo', '_upload_logo' ); ?>
            </td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>
            <td>&nbsp;</td>
            <td><input type="submit" value="<?php echo _('Save'); ?>" class="button" /></td>
        </tr>
    </table>
    <?php nonce::field( 'logo_and_phone' ); ?>
</form>
<?php echo $template->end(); ?>