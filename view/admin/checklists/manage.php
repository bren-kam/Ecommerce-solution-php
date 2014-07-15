<?php
    nonce::field( 'add_section', '_add_section' );
    nonce::field( 'add_item', '_add_item' );
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Manage Checklists
            </header>

            <div class="panel-body">

                <form method="post" role="form">

                    <div id="checklist-sections">
                        <?php
                            if ( is_array( $sections ) )
                            foreach ( $sections as $section ):
                        ?>

                            <div class="section" data-section-id="<?php echo $section->checklist_section_id ?>">

                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <a href="javascript:;" class="handle btn btn-default"><i class="fa fa-arrows"></i></a>
                                    </span>
                                    <input type="text" class="form-control section-title" name="sections[<?php echo $section->checklist_section_id; ?>]" value="<?php echo $section->name; ?>" placeholder="Section name"/>
                                    <span class="input-group-btn">
                                        <a href="javascript:;" class="remove-section btn btn-danger"><i class="fa fa-trash-o"></i></a>
                                    </span>
                                </div>

                                <div class="section-items">

                                    <?php
                                        if ( is_array( $items[$section->checklist_section_id] ) )
                                        foreach ( $items[$section->checklist_section_id] as $item ):
                                    ?>

                                            <div class="item input-group">
                                                <span class="input-group-btn">
                                                    <a href="javascript:;" class="handle btn btn-default"><i class="fa fa-arrows"></i></a>
                                                </span>
                                                <input type="text" class="form-control item-name" name="items[<?php echo $section->checklist_section_id; ?>][<?php echo $item->checklist_item_id; ?>][name]" value="<?php echo $item->name; ?>" />
                                                <input type="text" class="form-control item-assigned-to" name="items[<?php echo $section->checklist_section_id; ?>][<?php echo $item->checklist_item_id; ?>][assigned_to]" value="<?php echo $item->assigned_to; ?>" />
                                                <span class="input-group-btn">
                                                    <a href="javascript:;" class="remove-item btn btn-danger"><i class="fa fa-trash-o"></i></a>
                                                </span>
                                            </div>

                                    <?php endforeach; ?>

                                </div>

                                <p class="clearfix">
                                    <a href="javascript:;" class="btn btn-sm btn-success pull-right add-item" data-section-id="<?php echo $section->checklist_section_id ?>"><i class="fa fa-plus"></i> Add Item</a>
                                </p>

                            </div>

                        <?php endforeach; ?>

                    </div>

                    <?php nonce::field('manage'); ?>

                    <p>
                        <button type="button" id="add-section" class="btn btn-success">Add Section</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </p>


                </form>

            </div>

        </section>
    </div>
</div>

<div class="section hidden" id="section-template">

    <div class="input-group">
        <span class="input-group-btn">
            <a href="javascript:;" class="handle btn btn-default"><i class="fa fa-arrows"></i></a>
        </span>
        <input type="text" class="form-control section-title" placeholder="Section name" />
        <span class="input-group-btn">
            <a href="javascript:;" class="remove-section btn btn-danger"><i class="fa fa-trash-o"></i></a>
        </span>
    </div>

    <div class="section-items">
        <div class="item input-group">
        </div>
    </div>

    <p class="clearfix">
        <a class="btn btn-sm btn-success pull-right add-item"><i class="fa fa-plus"></i> Add Item</a>
    </p>
</div>

<div class="item input-group hidden" id="item-template">
    <span class="input-group-btn">
        <a href="javascript:;" class="handle btn btn-default"><i class="fa fa-arrows"></i></a>
    </span>
    <input type="text" class="form-control item-name" />
    <input type="text" class="form-control item-assigned-to" />
    <span class="input-group-btn">
        <a href="javascript:;" class="remove-item btn btn-danger"><i class="fa fa-trash-o"></i></a>
    </span>
</div>
