<?php
/**
 * @package Grey Suit Retail
 * @page Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 */

echo $template->start( _('Import products') );
?>

    <div id="dConfirm" class="hidden">
        <p><?php echo _('Please verify the following information is correct and confirm:'); ?></p>
        <table cellpadding="0" cellspacing="1" id="tUploadOverview" class="generic">
        </table>

        <br />
        <div id="dSkippedProducts" class="hidden">
            <p>The following products WILL NOT BE IMPORTED due to validation errors:</p>
            <table cellpadding="0" cellspacing="1" id="tSkippedProducts" class="generic">
            </table>
        </div>

        <br /><br />
        <form action="/products/confirm-import/" method="post" >
            <?php nonce::field( 'confirm_import' ); ?>
            <input type="submit" class="button" value="<?php echo _('Confirm import'); ?>" />
        </form>
    </div>

    <div id="dDefault">
        <p><?php echo _('On this page you can import a list of products.'); ?></p>

        <a href="#" id="aImportProducts" class="button" title="<?php echo _('Import'); ?>"><?php echo _('Import'); ?></a>
        <div class="hidden" id="import-products"></div>
        <?php nonce::field( 'prepare_import', '_prepare_import' ); ?>
        <br /><br />
        <br /><br />
    </div>

<?php echo $template->end(); ?>