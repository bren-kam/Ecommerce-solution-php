<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit Product Option
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var ProductOption $product_option
 * @var array $product_option_list_items
 * @var array $forms
 * @var string $validation
 * @var string $errs
 */
?>


<div class="row-fluid state-overview">
    <div class="col-lg-4 col-sm-6">
        <section class="panel">
            <div class="symbol terques">
                <a href="#drop-down-list" class="switch-form">
                    <i class="fa fa-th-list"></i>
                </a>
            </div>
            <div class="value">
                <p>On this option users would select one of multiple options, like Colors and Sizes</p>
            </div>
        </section>
    </div>
    <div class="col-lg-4 col-sm-6">
        <section class="panel">
            <div class="symbol terques">
                <a href="#checkbox" class="switch-form">
                    <i class="fa fa-check-square-o"></i>
                </a>
            </div>
            <div class="value">
                <p>On this option the user would response a yes/no type question, like Insurance.</p>
            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <?php echo $product_option->id ? 'Edit' : 'Add' ?> Product Option <span id="form-type"></span>
            </header>

            <div class="panel-body">

                <!-- Dropdown List Form -->
                <div id="drop-down-list" class="switchable-form <?php if ( empty( $product_option->id ) ||$product_option->type != 'select' ) echo ' hidden' ?>">

                    <form id="fAddEditDropDownList" method="post" role="form">

                        <div class="form-group">
                            <label for="tDropDownListTitle">Title:*</label>
                            <input type="text" class="form-control" name="tDropDownListTitle" id="tDropDownListTitle" value="<?php echo ( isset( $_POST['tDropDownListTitle'] ) || !$product_option->id ) ? $template->v('tDropDownListTitle') : $product_option->title; ?>" maxlength="50" />
                        </div>

                        <div class="form-group">
                            <label for="tDropDownListName">Name:*</label>
                            <input type="text" class="form-control" name="tDropDownListName" id="tDropDownListName" value="<?php echo ( isset( $_POST['tDropDownListName'] ) || !$product_option->id ) ? $template->v('tDropDownListName') : $product_option->name; ?>" maxlength="200" /></td>
                        </div>

                        <div class="form-group" id="product-option-item-list">
                            <div class="input-group">
                                <label for="tItem">Items</label>
                                <input type="text" class="form-control" id="tItem" placeholder="Add new item" />
                            <span class="input-group-btn">
                                <button type="button" id="add-item" class="btn btn-success"><i class="fa fa-plus"></i></button>
                            </span>
                            </div>

                            <?php
                            if ( is_array( $product_option_list_items ) )
                                foreach ( $product_option_list_items as $product_option_list_item ):
                            ?>
                                    <div class="input-group product-option-item">
                                        <input type="text" class="form-control" name="list-items[poli<?php echo $product_option_list_item->id; ?>]" value="<?php echo $product_option_list_item->value; ?>" />
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-danger delete-product-option-item"><i class="fa fa-trash-o"></i></button>
                                </span>
                                    </div>
                          <?php endforeach; ?>

                        </div>

                        <input type="hidden" name="hType" value="drop-down-list" />
                        <?php nonce::field('add_edit'); ?>

                        <button type="submit" class="btn btn-primary">Save</button>

                    </form>
                    <?php echo $validation; ?>

                </div>

                <!-- Checkbox Form -->
                <div id="checkbox" class="switchable-form <?php if ( empty( $product_option->id ) || $product_option->type != 'checkbox' ) echo ' hidden' ?>">
                    <?php echo $forms['checkbox']; ?>
                </div>


            </div>
        </section>
    </div>
</div>

<div class="input-group product-option-item hidden" id="product-option-item-template">
    <input type="text" class="form-control" name="list-items[]" />
    <span class="input-group-btn">
        <button type="button" id="add-item" class="btn btn-danger delete-product-option-item"><i class="fa fa-trash-o"></i></button>
    </span>
</div>
