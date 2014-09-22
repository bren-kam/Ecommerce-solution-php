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
 * @var string $section
 */

$add_edit_url = '/knowledge-base/categories/add-edit/?s=' . $section;

if ( $category->id )
    $add_edit_url = url::add_query_arg( array( 'kbcid' => $category->id ) );

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
                    <label for="sParentID">Parent Category</label>
                    <select class="form-control" name="sParentID" id="sParentID">
                        <option value="">Select Category</option>
                        <?php foreach ( $categories as $c ): ?>
                            <option value="<?php echo $c->id; ?>" <?php if ( $parent_id == $c->id ) echo ' selected="selected"' ?>><?php echo str_repeat( '&nbsp;', $c->depth * 5 ), $c->name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>

</form>
