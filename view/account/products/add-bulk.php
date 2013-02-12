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
 */

echo $template->start( _('Add Bulk') );
?>
<p><?php echo _("Separate SKU's by putting one on each line."); ?></p>
<?php
echo $form;
echo $template->end();
?>