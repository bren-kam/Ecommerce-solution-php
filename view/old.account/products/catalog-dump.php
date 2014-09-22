<?php
/**
 * @package Grey Suit Retail
 * @page Catalog Dump | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $errs
 * @var string $js_validation
 */

echo $template->start( _('Catalog Dump') );

if ( !empty( $errs ) )
    echo "<p class='red'>$errs</p>";
?>
<p><?php echo _('NOTE: This will add <em>every</em> item in a selected brand into your product catalog.'); ?></p>
<p><input type="text" class="tb" name="tAutoComplete" id="tAutoComplete" placeholder="<?php echo _('Enter Brand'); ?>..." /></p>
<form action="/products/catalog-dump/" method="post" name="fCatalogDump">
    <p><input type="submit" class="button" value="<?php echo _('Dump Brand'); ?>" /></p>
    <input type="hidden" id="hBrandID" name="hBrandID" />
    <?php nonce::field('catalog_dump'); ?>
</form>
<?php
echo $js_validation;
nonce::field( 'autocomplete', '_autocomplete' );

echo $template->end();
?>