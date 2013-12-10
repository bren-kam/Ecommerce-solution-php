<?php
/**
 * @package Grey Suit Retail
 * @page Navigation
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AccountPage[] $pages
 */

echo $template->start( _('Navigation') );
?>

<a href="#dAddEditNavigation" title="<?php echo _('Add Menu Item'); ?>" rel="dialog"><?php echo _('Add Menu Item'); ?></a>

<br />
<hr />
<br />

<div id="navigation-menu-list"></div>
<br clear="all"><br>

<div id="dAddEditNavigation" class="hidden">
    <p><input type="text" class="tb" id="menu-item-name" placeholder="<?php echo _('Menu Item Name'); ?>"></p>
    <p>
        <input type="radio" name="menu-link" value="menu-url" checked="checked">
        <input type="text" class="tb" id="menu-url" placeholder='<?php echo _('Menu link, i.e. "contact"'); ?>'>
        <br><br>
        <input type="radio" name="menu-link" id="menu-link-2" value="menu-page">
        <select id="menu-page">
            <?php foreach ( $pages as $page ) { ?>
            <option value="<?php echo $page->slug; ?>"><?php echo $page->title; ?></option>
            <?php } ?>
        </select>
        <br><br>
    </p>
    <p><a href="#" class="button" id="add-menu-item" title="<?php echo _('Add Menu Item'); ?>"><?php echo _('Add'); ?></a></p>
</div>

<div class="hidden">
<div id="dMenuItem" class="menu-item">
    <h4 class="name"></h4>
    <p class="menu-item-actions">
        <a href="#" class="edit-item" rel="dialog"><?php echo _('Edit'); ?></a>
        | <a href="#" class="delete-item" title="<?php echo _('Delete'); ?>" confirm="<?php echo _('Are you sure you want to delete this menu item? This cannot be undone.'); ?>"><?php echo _('Delete'); ?></a>
    </p>

    <a href="#" class="url" target="_blank" ></a>
</div>
</div>

<?php echo $template->end(); ?>