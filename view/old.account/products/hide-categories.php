<?php
/**
 * @package Grey Suit Retail
 * @page Hide Categories | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 * @var Category[] $hidden_categories
 */

echo $template->start( _('Hide Categories') );
echo $form;
?>
<br /><br />
<?php if ( !empty( $hidden_categories ) ) { ?>
    <h2><?php echo _('Hidden Categories'); ?></h2>
    <br />
    <form action="/products/unhide-categories/" method="post" name="fUnblockCategories">
        <table class="width-auto">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th class="text-left"><strong><?php echo _('Name'); ?></strong></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ( $hidden_categories as $category ) {
                    $parent_categories = $category->get_all_parents( $category->id );
                    $name = $category->name;

                    foreach ( $parent_categories as $pc ) {
                        $name = $pc->name . ' &gt; ' . $name;
                    }
                ?>
                <tr>
                    <td><input type="checkbox" class="cb" name="unhide-categories[]" value="<?php echo $category->id; ?>" id="cbUnhideCategory<?php echo $category->id; ?>" /></td>
                    <td><label for="cbUnhideCategory<?php echo $category->id; ?>"><?php echo $name; ?></label></td>
                </tr>
                <?php } ?>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" class="button" value="<?php echo _('Unhide Categories'); ?>" /></td>
                </tr>
            </tbody>
        </table>
        <?php nonce::field('unhide_categories'); ?>
    </form>
<?php
}

echo $template->end();
?>