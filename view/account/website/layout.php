<?php
/**
 * @package Grey Suit Retail
 * @page Layout
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AccountPage[] $pages
 * @var array $layout
 */

echo $template->start( _('Layout') );
?>

<form action="" method="post">
    <div id="dElementBoxes">
        <?php
        foreach ( $layout as $element ) {
            $name = ucwords( $element->name );
        ?>
        <div class="element-box<?php if ( $element->disabled ) echo ' disabled'; ?>">
            <h2><?php echo $name; ?></h2>

            <a href="#" class="enable-disable<?php if ( $element->disabled ) echo ' disabled'; ?>" title="<?php echo _('Enable/Disable'); ?>"><img src="/images/trans.gif" width="76" height="25" alt="<?php echo _('Enable/Disable'); ?>" /></a>
            <br>
            <img src="http://placehold.it/350x150&text=<?php echo urlencode( $name ); ?>" alt="<?php echo $name; ?>">
            <input name="layout[]" type="hidden" value="<?php echo $element->name . '|' . $element->disabled; ?>">
        </div>
        <?php } ?>
    </div>
    <input type="submit" class="button" value="<?php echo _('Save'); ?>">
    <?php nonce::field('layout'); ?>
</form>

<?php echo $template->end(); ?>