<?php
/**
 * @package Grey Suit Retail
 * @page Brands | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Brand[] $top_brands
 */

echo $template->start( _('Brands') );
?>
<table class="form">
    <tr>
        <td><label for="tAutoComplete"><?php echo _('Add Brand'); ?></label></td>
        <td><input type="text" class="tb" tmpval="<?php echo _('Enter Brand'); ?>..." id="tAutoComplete" /></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td><input type="checkbox" class="cb" id="cbLinkBrands"<?php echo ( $user->account->link_brands ) ? ' checked="checked"' : ''; ?> /> <label for="cbLinkBrands"><?php echo _('Link to Brand Websites'); ?></label></td>
    </tr>
</table>
<?php
nonce::field( 'autocomplete', '_autocomplete' );
nonce::field( 'add_brand', '_add_brand' );
nonce::field( 'set_brand_link', '_set_brand_link' );
nonce::field( 'update_brand_sequence', '_update_brand_sequence' );
?>
<hr />
<div id="brands">
<?php
if ( is_array( $top_brands ) ) {
    $remove_brand_nonce = nonce::create('remove_brand');
    $confirm = _('Are you sure you want to remove this brand?');

    foreach ( $top_brands as $brand ) {
    ?>
        <div id="dBrand_<?php echo $brand->id; ?>" class="brand">
            <img src="<?php echo $brand->image; ?>" title="<?php echo $brand->name; ?>" />
            <h4><?php echo $brand->name; ?></h4>
            <p class="brand-url"><a href="<?php echo $brand->link; ?>" title="<?php echo $brand->name; ?>" target="_blank" ><?php echo $brand->link; ?></a></p>
            <a href="<?php echo url::add_query_arg( array( '_nonce' => $remove_brand_nonce, 'bid' => $brand->id ), '/products/remove-brand/' ); ?>" title="<?php echo _('Remove'); ?>" ajax="1" confirm="<?php echo $confirm; ?>"><?php echo _('Remove'); ?></a>
        </div>
<?php
    }
}?>
</div>
<br clear="left" /><br />
<?php echo $template->end(); ?>