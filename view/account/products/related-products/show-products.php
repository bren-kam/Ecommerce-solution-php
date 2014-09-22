<?php
/**
 * @page Show Products | Related Products | Products
 * @type Dialog
 * @package Grey Suit Retail
 *
 * @var Product[] $products
 * @var WebsiteProductGroup $website_product_group
 */
 ?>

<!-- Modal -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            <h4 class="modal-title" id="modalLabel">Products in <?php echo $website_product_group->name ?></h4>
        </div>
        <div class="modal-body">

            <ul>
                <?php foreach ( $products as $product ) { ?>
                    <li><?php echo $product->name; ?></li>
                <?php } ?>
            </ul>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>