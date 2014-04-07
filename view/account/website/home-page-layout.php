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

<form id="fHomePageLayout" action="" method="post">
    <div id="dElementBoxes">
        <?php
        foreach ( $layout as $element ) {
            // Trending Items is only for New Template sites
            if ( $element->name == 'trending-items' && !$user->account->is_new_template() ) {
                continue;
            }

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
</form>
<?php
nonce::field('save_layout', '_save_layout');
echo $template->end();
?>