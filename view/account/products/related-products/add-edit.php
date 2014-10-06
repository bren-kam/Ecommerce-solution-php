<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit | Related Products | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var WebsiteProductGroup $group
 * @var Product[] $products
 * @var string $errs
 * @var string $js_validation
 */

nonce::field( 'autocomplete_owned', '_autocomplete_owned' );
nonce::field( 'add_product', '_add_product' );
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Add Product
            </header>

            <div class="panel-body">

                <form class="form-inline" role="form">
                    <div class="form-group">
                        <select class="form-control" id="sAutoComplete" name="s">
                            <option value="sku">SKU</option>
                            <option value="product">Product Name</option>
                            <option value="brand">Brand</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="tAutoComplete" placeholder="Search..." name="v" />
                    </div>
                    <button type="button" id="aSearch" class="btn btn-primary">Search</button>
                </form>

                <div class="adv-table">
                    <table class="display table table-bordered table-striped" id="tAddProducts" data-hide-filter="1">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Brand</th>
                                <th>SKU</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Products in Group
            </header>

            <div class="panel-body">
                <form action="<?php if ( $group->id ) echo '?wpgid=' . $group->id; ?>" method="post">

                    <div class="form-group">
                        <label for="tName">Group Name:</label>
                        <input type="text" class="form-control" name="tName" id="tName" value="<?php echo $group->name ?>" />
                    </div>

                    <div id="product-list" class="clearfix">
                        <?php foreach ( $products as $product ) :
                            $image = current( $product->get_images() );
                        ?>
                            <div class="product">
                                <h4><?php echo format::limit_chars( $product->name, 40 ); ?></h4>
                                <img src="<?php echo $product->get_image_url( $image, 'small', $product->industry, $product->id )?>" alt="<?php echo $product->name; ?>" />
                                <p>Brand: <?php echo $product->brand; ?></p>
                                <a href="javascript:;" class="remove" title="Remove"><i class="fa fa-trash-o"></i></a>
                                <input type="hidden" name="products[]" value="<?php echo $product->id; ?>" />
                            </div>
                        <?php endforeach; ?>
                        <div class="product hidden" id="product-template">
                            <h4></h4>
                            <p class="text-center"><img src="" /></p>
                            <p class="brand-name">Brand: </p>
                            <a href="javascript:;" class="remove" title="Remove"><i class="fa fa-trash-o"></i></a>
                            <input type="hidden" name="products[]" />
                        </div>
                    </div>

                    <?php nonce::field( 'add_edit' )?>
                    <p>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </p>
                </form>
            </div>
        </section>
    </div>
</div>