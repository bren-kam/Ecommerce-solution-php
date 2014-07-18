<?php
/**
 * @package Grey Suit Retail
 * @page Show Categories
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

nonce::field( 'get', '_get_categories' );
nonce::field( 'update_sequence', '_update_sequence' );
nonce::field( 'delete', '_delete' );
nonce::field( 'add_edit', '_add_edit' );

?>

<input type="hidden" id="hSection" value="<?php echo $_GET['s'] ?>" />

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Categories for <?php echo $uc_section ?>: <?php echo $link ?>
            </header>

            <div class="panel-body">

                <ul class="breadcrumb">
                    <li><a href="javascript:;" data-category-id="0" class="get-category"><i class="fa fa-home"></i>Main Category</a></li>
                </ul>

                <p class="clearfix" id="current-category">
                    <span>Main Category</span> <small><a href="javascript:;" class="edit-category" data-modal>Edit</a> | <a href="javascript:;" class="delete-category">Remove</a></small>
                    <a href="/knowledge-base/categories/add-edit/?s=<?php echo $_GET['s'] ?>" class="btn btn-primary pull-right" data-modal>Add Category</a>
                </p>

            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <div class="panel-body" id="categories-list"></div>

        </section>
    </div>
</div>

<div class="hidden category" id="category-template">
    <h4>
        <a href="javascript:;" class="get-category"></a>
        <small class="text-muted"></small>
    </h4>

    <p class="category-actions">
        <small>
            <a href="javascript:;" class="edit" title="Edit Category">Edit</a>
            | <a href="javascript:;" class="delete-category" title="Delete">Delete</a>
        </small>
    </p>

    <p class="text-muted"><small class="url-preview"></small></p>
</div>

