<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit Category -- Dialog
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Category $category
 * @var array $categories
 * @var array $attributes
 * @var array $category_attribute_ids
 * @var string $google_taxonomy
 */

$add_edit_url = '/products/categories/add-edit/';

if ( $category->id )
    $add_edit_url = url::add_query_arg( array( 'cid' => $category->id, 'pcid' => $_GET['pcid'] ) );

?>

<form name="fAddEditCategory" id="fAddEditCategory" action="<?php echo $add_edit_url; ?>" method="post" role="form">

    <!-- Modal -->
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="modalLabel"><?php echo $category->id ? 'Edit' : 'Add'?> Category</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label for="tName">Name:</label>
                    <input type="text" class="form-control" name="tName" id="tName" value="<?php echo $name ?>" placeholder="Category Name" />
                </div>

                <div class="form-group">
                    <label for="sParentCategoryID">Parent Category</label>
                    <select class="form-control" name="sParentCategoryID" id="sParentCategoryID">
                        <option value="">Select Category</option>
                        <?php foreach ( $categories as $c ): ?>
                            <option value="<?php echo $c->id; ?>" <?php if ( $parent_category_id == $c->id ) echo ' selected="selected"' ?>><?php echo str_repeat( '&nbsp;', $c->depth * 5 ), $c->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tSlug">Slug:</label>
                    <input type="text" class="form-control" name="tSlug" id="tSlug" value="<?php echo $slug ?>" placeholder="Slug" />
                </div>

                <div class="form-group">
                    <label for="tGoogleTaxonomy">Google Taxonomy (<a href="http://www.google.com/basepages/producttype/taxonomy.en-US.txt" target="_blank">read more</a>)</label>
                    <input type="text" class="form-control" name="tGoogleTaxonomy" id="tGoogleTaxonomy" value="<?php echo $google_taxonomy ?>" placeholder="Google Taxonomy" />
                </div>

                <div class="form-group">
                    <label for="sAttributes">Attributes</label>
                    <select class="form-control" name="sAttributes" id="sAttributes" multiple="multiple">
                        <?php foreach ( $attributes as $a ): ?>
                            <option value="<?php echo $a->id; ?>"<?php if ( in_array( $a->id, $category_attribute_ids ) ) echo ' disabled="disabled"'; ?>><?php echo $a->title; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="attributes-list">
                    <?php foreach ( $category_attribute_ids as $caid ): ?>
                        <p class="attribute">
                            <?php echo $attributes[$caid]->title; ?>
                            <a href="javascript:;" class="delete-attribute pull-right" title="Delete">
                                <i class="fa fa-trash-o"></i>
                            </a>
                            <input type="hidden" name="hAttributes[]" value="<?php echo $caid ?>" />
                        </p>
                    <?php endforeach; ?>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>

</form>
