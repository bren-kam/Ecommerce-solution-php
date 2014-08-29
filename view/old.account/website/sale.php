<?php
/**
 * @package Grey Suit Retail
 * @page Sale
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 */

echo $template->start( _('Sale') );
echo $form;
?>
<p><a href="<?php echo url::add_query_arg( '_nonce', nonce::create('remove_sale_items'), '/website/remove-sale-items/' ); ?>" title="<?php echo _('Remove All Sale Items'); ?>" ajax="1" confirm="<?php echo _('Are you sure you want to remove all sale items?'); ?>"><?php echo _('Remove All Sale Items'); ?></a></p>
<?php echo $template->end(); ?>