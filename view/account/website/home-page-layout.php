<?php
/**
 * @package Grey Suit Retail
 * @page Home Page Layout
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AccountPage $page
 */

echo $template->start( _('Home Page layout') );
?>

<br /><br /><br />
<p class="alert">(<?php echo _('Note: The changes you make to your sidebar are immediately live on your website'); ?>)</p>

<div id="dElementBoxes">
<?php
$h2 = $content_id = $placerholder = $buttons = $value = '';
$update_attachment_status_nonce = nonce::create( 'update_attachment_status' );
$confirm_disable = _('Are you sure you want to deactivate this sidebar element? This will remove it from the sidebar on your website.');
$confirm_remove = _('Are you sure you want to remove this sidebar element?');

/**
 * @var AccountPageAttachment $a
 */
foreach ( $attachments as $a ) {
?>
    <div class="element-box<?php echo $disabled; ?>" id="dAttachment_<?php echo $a->id; ?>">
        <h2><?php echo $h2; ?></h2>

        <?php
        echo $enable_disable_link;

        if ( !empty( $content_id ) ) { ?>
        <div id="<?php echo $content_id; ?>" class="center-content">
            <?php echo ( empty( $a->value ) && isset( $placeholder ) ) ? $placeholder : $value; ?>
        </div>
        <?php } ?>
        <br />

        <?php
        if ( !empty( $buttons ) ) {
            $remove_attachment_url = url::add_query_arg( array(
                    '_nonce' => $remove_attachment_nonce
                    , 'apaid' => $a->id
                    , 't' => $content_id
                )
                , '/website/remove-attachment/'
            );
            ?>
            <div align="center" class="buttons">
                <?php if ( !empty( $a->value ) && $remove ) { ?>
                    <a href="<?php echo $remove_attachment_url; ?>" id="aRemove<?php echo $a->id; ?>" title="<?php echo _('Remove'); ?>" confirm="<?php echo $confirm_remove; ?>"><?php echo _('Remove'); ?></a>
                    <br /><br />
                <?php
                }

                echo $buttons;
                ?>
                <br clear="left" />
            </div>
        <?php } ?>
    </div>
<?php } ?>
</div>

<?php echo $template->end(); ?>