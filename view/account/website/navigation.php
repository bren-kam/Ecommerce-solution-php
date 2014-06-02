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
 * @var array $navigation
 */

echo $template->start( _('Header Menu Navigation') );
?>

<a href="#dAddEditNavigation" title="<?php echo _('Add Menu Item'); ?>" rel="dialog"><?php echo _('Add Menu Item'); ?></a>

<br />
<hr />
<br />

<form action="" name="fNavigation" id="fNavigation" method="post">
    <ul id="navigation-menu-list">
        <?php if ( $navigation ) { ?>
            <?php foreach ( $navigation as $k => $page ) { ?>
                <li id="l<?php echo $k ?>">
                    <div class="menu-item">
                        <h4 class="name"><?php echo $page->name; ?></h4>
                        <p class="menu-item-actions">
                            <a href="#" class="delete-item" title="<?php echo _('Delete'); ?>" data-confirm="<?php echo _('Are you sure you want to delete this menu item? This cannot be undone.'); ?>"><?php echo _('Delete'); ?></a>
                        </p>

                        <a href="#" class="url" target="_blank" ><?php echo $page->url; ?></a>
                        <input type="hidden" name="navigation[l<?php echo $k ?>]" value="<?php echo $page->url . '|' . $page->name; ?>">
                    </div>
                    <?php if ( isset( $page->children ) ) { ?>
                        <ul>
                            <?php foreach ( $page->children as $child_k => $child_page ) { ?>
                                <li id="l<?php echo $k ?>l<?php echo $child_k ?>">
                                    <div class="menu-item">
                                        <h4 class="name"><?php echo $child_page->name; ?></h4>
                                        <p class="menu-item-actions">
                                            <a href="#" class="delete-item" title="<?php echo _('Delete'); ?>" data-confirm="<?php echo _('Are you sure you want to delete this menu item? This cannot be undone.'); ?>"><?php echo _('Delete'); ?></a>
                                        </p>

                                        <a href="#" class="url" target="_blank" ><?php echo $child_page->url; ?></a>
                                        <input type="hidden" name="navigation[l<?php echo $k ?>l<?php echo $child_k ?>]" value="<?php echo $child_page->url . '|' . $child_page->name; ?>">
                                    </div>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </li>
            <?php } ?>
        <?php } else { ?>
        <li id="l1">
            <div class="menu-item">
                <h4 class="name">Home</h4>
                <p class="menu-item-actions">
                    <a href="#" class="delete-item" title="<?php echo _('Delete'); ?>" data-confirm="<?php echo _('Are you sure you want to delete this menu item? This cannot be undone.'); ?>"><?php echo _('Delete'); ?></a>
                </p>

                <a href="#" class="url" target="_blank" >/</a>
                <input type="hidden" name="navigation[l1]" value="<?php echo '|Home'; ?>">
            </div>
        </li>
        <li id="l2">
            <div class="menu-item">
                <h4 class="name">About</h4>
                <p class="menu-item-actions">
                    <a href="#" class="delete-item" title="<?php echo _('Delete'); ?>" data-confirm="<?php echo _('Are you sure you want to delete this menu item? This cannot be undone.'); ?>"><?php echo _('Delete'); ?></a>
                </p>

                <a href="#" class="url" target="_blank" >/about-us/</a>
                <input type="hidden" name="navigation[l2]" value="<?php echo 'about-us|About Us'; ?>">
            </div>
        </li>
        <li id="l3">
            <div class="menu-item">
                <h4 class="name">Current Offer</h4>
                <p class="menu-item-actions">
                    <a href="#" class="delete-item" title="<?php echo _('Delete'); ?>" data-confirm="<?php echo _('Are you sure you want to delete this menu item? This cannot be undone.'); ?>"><?php echo _('Delete'); ?></a>
                </p>

                <a href="#" class="url" target="_blank" >/current-offer/</a>
                <input type="hidden" name="navigation[l3]" value="<?php echo 'current-offer|Current Offer'; ?>">
            </div>
        </li>
        <li id="l4">
            <div class="menu-item">
                <h4 class="name">Financing</h4>
                <p class="menu-item-actions">
                    <a href="#" class="delete-item" title="<?php echo _('Delete'); ?>" data-confirm="<?php echo _('Are you sure you want to delete this menu item? This cannot be undone.'); ?>"><?php echo _('Delete'); ?></a>
                </p>

                <a href="#" class="url" target="_blank" >/financing/</a>
                <input type="hidden" name="navigation[l4]" value="<?php echo 'financing|Financing'; ?>">
            </div>
        </li>
        <li id="l5">
            <div class="menu-item">
                <h4 class="name">Contact</h4>
                <p class="menu-item-actions">
                    <a href="#" class="delete-item" title="<?php echo _('Delete'); ?>" data-confirm="<?php echo _('Are you sure you want to delete this menu item? This cannot be undone.'); ?>"><?php echo _('Delete'); ?></a>
                </p>

                <a href="#" class="url" target="_blank" >/contact-us/</a>
                <input type="hidden" name="navigation[l5]" value="<?php echo 'contact-us|Contact'; ?>">
            </div>
        </li>
        <?php } ?>
    </ul>

    <input type="submit" class="button" value="<?php echo _('Save'); ?>">
    <?php nonce::field('navigation'); ?>
</form>
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
            <option value="<?php echo $page->slug; ?>"><?php echo ( empty( $page->title ) ) ? format::slug_to_name( $page->slug ) . ' (' . _('No Name') . ')' : $page->title; ?></option>
            <?php } ?>
        </select>
        <br><br>
    </p>
    <p><a href="#" class="button" id="add-menu-item" title="<?php echo _('Add Menu Item'); ?>"><?php echo _('Add'); ?></a></p>
</div>

<div class="hidden">
    <li id="dMenuItem">
        <div class="menu-item">
            <h4 class="name"></h4>
            <p class="menu-item-actions">
                <a href="#" class="delete-item" title="<?php echo _('Delete'); ?>" data-confirm="<?php echo _('Are you sure you want to delete this menu item? This cannot be undone.'); ?>"><?php echo _('Delete'); ?></a>
            </p>

            <a href="#" class="url" target="_blank" >/contact-us/</a>
        </div>
    </li>
</div>

<?php echo $template->end(); ?>