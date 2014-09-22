<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit Attribute
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Attribute $attribute
 * @var array $attribute_items
 * @var bool|string $errs
 * @var string $validation
 */
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <?php echo $attribute->id ? 'Edit' : 'Add' ?> Attribute
            </header>

            <div class="panel-body">

                <form name="fAddEditAttribute" id="fAddEditAttribute" method="post" <?php if ( $attribute->id ) echo 'action="?aid='. $attribute->id .'"' ?>>

                    <?php nonce::field( 'add_edit' ); ?>

                    <div class="form-group">
                        <label for="tTitle">Title</label>
                        <input type="text" class="form-control" name="tTitle" id="tTitle" value="<?php echo ( isset( $_POST['tTitle'] ) || !$attribute->id ) ? $template->v('tTitle') : $attribute->title; ?>" placeholder="Title" />
                    </div>

                    <div class="form-group">
                        <label for="tName">Name</label>
                        <input type="text" class="form-control" name="tName" id="tName" value="<?php echo ( isset( $_POST['tName'] ) || !$attribute->id ) ? $template->v('tName') : $attribute->name; ?>" placeholder="Name" />
                    </div>

                    <p><strong>Items:</strong></p>

                    <div id="attribute-items-list">
                        <div class="input-group">
                            <input type="text" class="form-control" id="tItem" placeholder="Add new item" />
                            <span class="input-group-btn">
                                <button type="button" id="add-item" class="btn btn-success"><i class="fa fa-plus"></i></button>
                            </span>
                        </div>

                        <?php
                            if ( is_array( $attribute_items ) )
                                foreach ( $attribute_items as $attribute_item ):
                        ?>
                            <div class="input-group attribute-item">
                                <input type="text" class="form-control" name="list-items[ai<?php echo $attribute_item->id; ?>]" value="<?php echo $attribute_item->name; ?>" />
                                <span class="input-group-btn">
                                    <a href="javascript:;" id="add-item" class="btn btn-default move-attribute-item move"><i class="fa fa-arrows"></i></a>
                                </span>
                                <span class="input-group-btn">
                                    <a href="javascript:;" class="btn btn-danger delete-attribute-item"><i class="fa fa-trash-o"></i></a>
                                </span>
                            </div>
                        <?php endforeach; ?>

                    </div>

                    <p>
                        <br />
                        <button type="submit" class="btn btn-lg btn-primary">Save</button>
                    </p>

                </form>

                <?php echo $validation ?>

            </div>
        </section>
    </div>
</div>

<div class="input-group attribute-item hidden" id="attribute-item-template">
    <input type="text" class="form-control" name="list-items[]" value="<?php echo $attribute_item->name; ?>" />
    <span class="input-group-btn">
        <a href="javascript:;" class="btn btn-default move-attribute-item move"><i class="fa fa-arrows"></i></a>
    </span>
    <span class="input-group-btn">
        <a href="javascript:;" class="btn btn-danger delete-attribute-item"><i class="fa fa-trash-o"></i></a>
    </span>
</div>
