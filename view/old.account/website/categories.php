<?php
/**
 * @package Grey Suit Retail
 * @page List Website Pages
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var array $categories
 */

echo $template->start( _('Website Categories') );
?>

<div class="relative">
    <select id="sParentCategoryID">
        <option value="0">-- <?php echo _('Parent Category (Top) '); ?> --</option>
        <?php
        foreach ( $categories as $category ) {
            ?>
            <option value="<?php echo $category->id; ?>"><?php echo str_repeat( '&nbsp;', $category->depth * 5 ); echo $category->name; ?></option>
        <?php } ?>
    </select>
    <table ajax="/website/list-categories/" perPage="30,50,100">
        <thead>
            <tr>
                <th width="65%"><?php echo _('Title'); ?></th>
                <th width="35%"><?php echo _('Last Updated'); ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<?php
nonce::field( 'store_session', '_store_session' );
echo $template->end();
?>