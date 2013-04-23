<?php
/**
 * @package Grey Suit Retail
 * @page Show Knowledge Base Categories
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Categories'), '../sidebar' );

nonce::field( 'get', '_get_categories' );
?>

<div id="breadcrumb"><?php echo _('Main Categories'); ?></div>

<a href="/knowledge-base/categories/add-edit/#dAddEditCategory" class="float-right" title="<?php echo _('Add Category'); ?>" rel="dialog" cache="0"><?php echo _('Add Category'); ?></a>

<h3 id="current-category">
    <span><?php echo _('Main Categories'); ?></span>
    <span id="edit-delete-category" class="hidden small"> -
        <a href="/knowledge-base/categories/add-edit/" id="edit-category" title="<?php echo _('Edit'); ?>" rel="dialog" ajax="1" cache="0"><?php echo _('Edit'); ?></a>
        <span class="small">|</span>
        <a href="#" id="delete-category" title="<?php echo _('Delete'); ?>"  ajax="1" confirm="<?php echo _('Are you sure you want to delete this category? This cannot be undone.'); ?>"><?php echo _('Delete'); ?></a>
    </span>
</h3>

<br clear="right" />
<br />
<hr />
<br />

<p align="center" class="hidden" id="no-sub-categories">
    <?php echo _('No sub categories have been created for this category.'); ?>
    <a href="#" title="<?php echo _('Add'); ?>"><?php echo _('Add a category'); ?></a> <?php echo _('now'); ?>.
</p>

<div id="categories-list"></div>

<br clear="all" /><br />

<?php echo $template->end(); ?>