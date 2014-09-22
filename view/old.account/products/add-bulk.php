<?php
/**
 * @package Grey Suit Retail
 * @page Add Bulk | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 * @var int $already_existed
 * @var array $not_added_skus
 * @var bool $success
 */

echo $template->start( _('Add Bulk') );

if ( $success ) {
    if ( $already_existed > 0 ) {
        ?>
        <p><?php echo number_format( $already_existed ), ' ', _('SKU(s) were already on the website.'); ?></p>
    <?php
    }
    if ( count( $not_added_skus ) > 0 ) {
        ?>
        <p><?php echo _('The following SKU(s) were not added for one of the following reasons:'); ?></p>
        <br />
        <ol>
            <li><?php echo _('The SKU is not a valid SKU or does not match the SKU in our master catalog.'); ?></li>
            <li><?php echo _('The SKUs are for industries not associated with this account.'); ?></li>
            <li><?php echo _('There is no image associated with the SKU.'); ?></li>
        </ol>
        <br />
        <blockquote style="border-left: 1px solid #929292; margin-left: 20px; padding-left: 20px">
            <?php echo implode( '<br />', $not_added_skus ); ?>
        </blockquote>
        <br />
        <hr />
        <br /><br />
    <?php
    }
}
?>
<p><?php echo _("Separate SKU's by putting one on each line."); ?></p>
<?php
echo $form;
echo $template->end();
?>