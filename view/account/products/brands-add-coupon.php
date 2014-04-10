<?php
/**
 * @package Grey Suit Retail
 * @page Brand Add Coupon | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Brand[] $brands
 * @var Coupon[] $coupons
 */

echo $template->start( _('Add Coupon to Brand') );
?>

<p>This action will add the selected coupon to all brand products. This won't affect future brand products.</p>
<br>

<?php
echo $form;
echo $template->end();
