<?php
/**
 * @package Grey Suit Retail
 * @page Top Categories | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Category[] $categories
 * @var Category[] $top_categories
 * @var array $category_images
 */

echo $template->start( _('Top Categories') );
?>
<select id="sCategoryId">
    <option value="">-- Select Category --</option>
    <?php foreach ( $categories as $category ) { ?>
    <option value="<?php echo $category->id; ?>" data-img="<?php echo $category_images[$category->id]; ?>"><?php echo str_repeat( '&nbsp;', $category->depth * 5 ); echo $category->name; ?></option>
    <?php } ?>
</select>
<a href="#" class="button" id="add-category">Add Category</a>
<?php nonce::field( 'update_top_category_sequence', '_update_top_category_sequence' ); ?>
<hr />
<div id="top-categories">
<?php
if ( is_array( $top_categories ) ) {
    foreach ( $top_categories as $category ) {
        $image = ( !isset( $category_images[$category->id] ) || empty( $category_images[$category->id] ) ) ? 'http://placehold.it/200x200&text=' . $category->name : $category_images[$category->id];
    ?>
        <div id="dTopCategory_<?php echo $category->id; ?>" class="top-category">
            <img src="<?php echo $image; ?>" width="200" />
            <h4><?php echo $category->name; ?></h4>
            <a href="#" class="remove-category" title="<?php echo _('Remove'); ?>" data-confirm="Are you sure you want to remove this top category?">Remove</a>
        </div>
<?php
    }
}?>
</div>
<br clear="left" /><br />
<div id="category-template" class="top-category" style="display: none">
    <img src="http://placehold.it/200x200&text=Category" width="200" />
    <h4><?php echo $category->title; ?></h4>
    <a href="#" class="remove-category" title="<?php echo _('Remove'); ?>" data-confirm="Are you sure you want to remove this top category?">Remove</a>
</div>
<br clear="left" /><br />
<?php echo $template->end(); ?>