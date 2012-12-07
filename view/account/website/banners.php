<?php
/**
 * @package Grey Suit Retail
 * @page Banners
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AccountPage $page
 * @var array $settings
 * @var string $dimensions
 * @var bool $images_alt
 */

echo $template->start( _('Banners') );
?>

<a href="#" id="aUploadBanner" class="button" title="<?php echo _('Upload'); ?>"><?php echo _('Upload'); ?></a>
<div class="hidden" id="upload-banner"></div>
<br /><br />
<p class="red"><?php echo _('(Note: The changes you make to your banners are immediately live on your website)'); ?></p>
<br />
<input type="hidden" id="hAccountPageId" value="<?php echo $page->id; ?>" />
<input type="hidden" id="hBannerWidth" value="<?php echo $settings['banner-width']; ?>" />
<?php
nonce::field( 'upload_banner', '_upload_banner' );
nonce::field( 'update_sequence', '_update_sequence' );
?>
<div id="dElementBoxes">
    <?php
    $remove_attachment_nonce = nonce::create('remove_attachment');
    $update_status_nonce = nonce::create('update_attachment_status');
    $confirm_disable = _('Are you sure you want to deactivate this banner?');
    $confirm_remove = _('Are you sure you want to remove this banner?');

    /**
     * @var AccountPageAttachment $a
     */
    foreach ( $attachments as $a ) {
        if ( '0' == $a->status ) {
            $disabled = ' disabled';
            $confirm = '';
        } else {
            $disabled = '';
            $confirm = ' confirm="' . $confirm_disable . '"';
        }

        if ( stristr( $a->value, 'http:' ) ) {
                $banner_url = $a->value;
            } else {
                $banner_url = 'http://' . $user->account->domain . $a->value;
            }

            $enable_disable_url = url::add_query_arg( array(
                '_nonce' => $update_status_nonce
                , 'apaid' => $a->id
                , 's' => ( '0' == $a->status ) ? '1' : '0'
            ), '/website/update-attachment-status/' );
        ?>
        <div class="element-box<?php echo $disabled; ?>" id="dAttachment_<?php echo $a->id; ?>">
            <h2><?php echo _('Banner'); ?></h2>
            <p><small><?php echo $dimensions; ?></small></p>
            <a href="<?php echo $enable_disable_url; ?>" id="aEnableDisable<?php echo $a->id; ?>" class="enable-disable<?php echo $disabled; ?>" title="<?php echo _('Enable/Disable'); ?>" ajax="1"<?php echo $confirm; ?>><img src="/images/trans.gif" width="26" height="28" alt="<?php echo _('Enable/Disable'); ?>" /></a>

            <div id="dBanner<?php echo $a->id; ?>" class="text-center">
                <img src="<?php echo $banner_url; ?>" alt="<?php echo _('Sidebar Image'); ?>" />
            </div>
            <br />

            <form action="/website/update-attachment-extra/" method="post" ajax="1">
                <p id="pTempSuccess<?php echo $a->id; ?>" class="success hidden"><?php echo _('Your banner has been successfully updated.'); ?></p>
                <input type="text" class="tb" name="extra" tmpval="<?php echo _('Enter Link...'); ?>" value="<?php echo ( empty( $a->extra ) ) ? 'http://' : $a->extra; ?>" />

                <?php if ( $images_alt ) { ?>
                    <input type="text" class="tb" name="meta" tmpval="<?php echo _('Enter Alt Attribute...'); ?>" value="<?php if ( !empty( $a->meta ) ) echo $a->meta; ?>" />
                <?php } ?>

                <input type="submit" class="button" value="<?php echo _('Save'); ?>" />

                <input type="hidden" name="hAccountPageAttachmentId" value="<?php echo $a->id; ?>" />
                <input type="hidden" name="target" value="pTempSuccess<?php echo $a->id; ?>" />
                <?php nonce::field( 'update_attachment_extra', '_nonce' ); ?>
            </form>
            <?php
            $remove_attachment_url = url::add_query_arg( array(
                '_nonce' => $remove_attachment_nonce
                , 'apaid' => $a->id
                , 't' => 'dAttachment_' . $a->id
                , 'si' => '1'
            ), '/website/remove_attachment/' );
            ?>
            <a href="<?php echo $remove_attachment_url; ?>" class="remove" title="<?php echo _('Remove Banner'); ?>" ajax="1" confirm="<?php echo $confirm_remove; ?>"><?php echo _('Remove'); ?></a>
            <br clear="all" />
        </div>
    <?php } ?>
</div>

<?php echo $template->end(); ?>