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
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Header Navigation
                <a href="javascript:;" class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#add-menu-item"><i class="fa fa-plus"></i> Add Menu Item</a>
            </header>

            <div class="panel-body">

                <form method="post" role="form">
                    <div class="dd" id="navigation">
                        <ol class="dd-list">
                            <?php foreach ( $navigation as $k => $page ): ?>
                                <li class="dd-item dd3-item" data-id="<?php echo $k ?>">
                                    <div class="dd-handle dd3-handle"></div>
                                    <div class="dd3-content">
                                        <?php echo $page->name ?>
                                        <span class="page-url"><?php echo $page->url ? $page->url : '/' ?></span>
                                        <select name="submenu-columns[<?php echo $k ?>]">
                                            <?php for($cols=1; $cols<=4; $cols++): ?>
                                                <option value="<?php echo $cols ?>" <?php if (isset($page->submenu_columns) && $page->submenu_columns == $cols) echo "selected"?>>Place sub-items in <?php echo $cols ?> column(s)</option>
                                            <?php endfor; ?>
                                        </select>
                                        <a href="javascript:;" class="delete"><i class="fa fa-trash-o"></i></a>
                                        <input type="hidden" name="navigation[<?php echo $k ?>]" value="<?php echo $page->url . '|' . $page->name; ?>">
                                    </div>
                                    <?php if ( isset( $page->children ) ): ?>
                                        <ol class="dd-list">
                                            <?php foreach ( $page->children as $child_k => $child_page ): ?>
                                                <li class="dd-item dd3-item" data-id="<?php echo $k . "_" . $child_k ?>">
                                                    <div class="dd-handle dd3-handle"></div>
                                                    <div class="dd3-content">
                                                        <?php echo $child_page->name ?>
                                                        <span class="page-url"><?php echo $child_page->url ? $child_page->url : '/' ?></span>
                                                        <a href="javascript:;" class="delete"><i class="fa fa-trash-o"></i></a>
                                                        <input type="hidden" name="navigation[<?php echo $k . '_' . $child_k ?>]" value="<?php echo $child_page->url . '|' . $child_page->name; ?>">
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ol>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                           </ol>
                    </div>

                    <p>
                        <input type="hidden" name="tree" id="tree" value="" />
                        <?php nonce::field( 'navigation' ) ?>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </p>
                </form>


            </div>
        </section>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="add-menu-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Add new Menu Item</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name">
                </div>
                <div class="form-group">
                    <label for="link-select">Select a Link:</label>
                    <select id="link-select" class="form-control">
                        <option value=""></option>
                        <?php foreach ( $pages as $page ) { ?>
                            <option value="<?php echo $page->slug; ?>"><?php echo ( empty( $page->title ) ) ? format::slug_to_name( $page->slug ) . ' (' . _('No Name') . ')' : $page->title; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="link">Custom Link:</label>
                    <input type="text" class="form-control" id="link">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save-menu-item">Add</button>
            </div>
        </div>
    </div>
</div>

<ul class="hidden">
    <li class="dd-item dd3-item" id="item-template">
        <div class="dd-handle dd3-handle"></div>
        <div class="dd3-content">
            <span class="page-url"></span>
            <a href="javascript:;" class="delete"><i class="fa fa-trash-o"></i></a>
            <input type="hidden">
        </div>
    </li>
</ul>
