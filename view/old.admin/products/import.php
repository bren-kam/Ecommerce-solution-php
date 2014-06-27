<?php
/**
 * @package Grey Suit Retail
 * @page Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var Brand[] $brands
 */

echo $template->start( _('Import products') );
?>

    <div id="dConfirm" class="hidden">
        <p><?php echo _('Please verify the following information is correct and confirm:'); ?></p>
        <br />
        <table id="tUploadOverview" class="overview-report">
        </table>

        <br />
        <div id="dSkippedProducts" class="hidden">
            <p>The following products <span class="error">WILL NOT BE IMPORTED</span> due to validation errors:</p>
            <br/ >
            <table id="tSkippedProducts" class="error-table">
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
        <br/ >

        <label for="brand">Brand:</label>
        <select id="brand">
            <?php foreach ( $brands as $brand ): ?>
                <option value="<?php echo $brand->brand_id ?>"><?php echo $brand->name ?></option>
            <?php endforeach; ?>
        </select>

        <br /><br />

        <a href="#" id="aImportProducts" class="button" title="<?php echo _('Import'); ?>"><?php echo _('Select file...'); ?></a>
        <div class="hidden" id="import-products"></div>
        <?php nonce::field( 'prepare_import', '_prepare_import' ); ?>
        <br /><br />
        <br /><br />
    </div>

<?php echo $template->end(); ?>