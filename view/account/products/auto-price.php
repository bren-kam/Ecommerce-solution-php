<?php
/**
 * @package Grey Suit Retail
 * @page Auto Price | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $auto_price_settings
 * @var string $auto_price
 * @var array $auto_price_candidates
 */

echo $template->start( _('Auto Price') );
?>

<p><?php echo _('On this page you set all of your prices based on the whole sale price.'); ?></p>
<p><?php echo _('Please enter in the multipliers in the fields below before uploading. A "0" will be ignored.'); ?></p>
<br><br>
<?php if ( empty( $auto_price_candidates ) ) { ?>
    <p>This would affect none of your current products.</p>
<?php } else { ?>
    <p>This would affect the following products:</p>
    <ul>
        <?php foreach ( $auto_price_candidates as $candidate ) { ?>
        <li> * <?php echo $candidate['brand'] . ' - ' . $candidate['count']; ?> product(s)</li>
        <?php } ?>
    </ul>
<?php } ?>
<br>

<?php if ( !empty( $auto_price_settings ) ) { ?>
<h2><?php echo _('Auto Price Settings'); ?></h2>
<br>
<?php echo $auto_price_settings; ?>
<br><br>
<?php } ?>

<h2><?php echo _('Auto Price - Manual'); ?></h2>
<br>
<?php echo $auto_price; ?>

<br /><br />
<br /><br />

<?php echo $template->end(); ?>