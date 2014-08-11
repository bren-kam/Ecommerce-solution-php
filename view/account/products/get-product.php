<?php
/**
 * @page Get Product Dialog
 * @type Dialog
 * @package Grey Suit Retail
 *
 * @var Product $product
 * @var Category $category
 */
?>

    <!-- Modal -->
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="modalLabel"><?php echo $product->name ?></h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-lg-6 text-right">
                        <img src="http://<?php echo $product->industry; ?>.retailcatalog.us/products/<?php echo $product->id, '/small/', reset( $product->images ); ?>" />
                    </div>
                    <div class="col-lg-6">
                        <h3><?php echo $product->name ?></h3>
                        <ul>
                            <li>SKU: <?php echo $product->sku ?></li>
                            <li>Brand: <?php echo $product->brand ?></li>
                            <li>Category: <?php echo $category->name ?></li>
                        </ul>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>

</form>
