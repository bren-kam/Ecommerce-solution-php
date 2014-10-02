<?php nonce::field( 'autocomplete_owned', '_autocomplete' ); ?>

<section class="panel <?php if ( $product_count == 0 ) echo 'hidden' ?>" id="product-form">
    <header class="panel-heading">
        Products shown in this Page
    </header>

    <div class="panel-body">

        <div class="row">
            <div class="col-lg-3">
                <select id="sAutoComplete" class="form-control">
                    <option value="sku">SKU</option>
                    <option value="product">Product Name</option>
                    <option value="brand">Brand</option>
                </select>
            </div>
            <div class="col-lg-3">
                <input type="text" id="tAutoComplete" class="form-control" placeholder="Search" />
            </div>
            <div class="col-lg-1">
                <button id="bSearch" class="btn btn-default" type="button">Search</button>
            </div>
        </div>

        <div class="adv-table clearfix">
            <table id="tAddProducts" class="display table table-bordered table-striped">
                <thead>
                <tr>
                    <th sort="1">Name</th>
                    <th>Brand</th>
                    <th>SKU</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>


        <div id="product-list" class="clearfix">
            <?php
                if ( $product_count > 0 ):
                foreach ( $page->products as $product ):
                    $images = $product->get_images();
                    $product_image = $product->get_image_url( current($images), '', $product->industry, $product->id  );
            ?>
                <div class="product">
                    <img src="<?php echo $product_image ?>"/>
                    <h4><?php echo $product->name ?></h4>
                    <p class="brand"><?php echo $product->brand ?></p>
                    <a href="javascript:;" class="remove"><i class="fa fa-trash-o"></i></a>
                    <input type="hidden" name="products[]" value="<?php echo $product->id ?>"/>
                </div>
            <?php
                endforeach;
                endif;
            ?>
        </div>

        <div class="radio">
            <label>
                <input type="radio" name="rPosition" value="1" <?php if ( $page->top == '1' ) echo 'checked="checked"'; ?> />
                Products will be placed after content
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="rPosition" value="0" <?php if ( $page->top == '0' ) echo 'checked="checked"'; ?> />
                Products will be placed before content
            </label>
        </div>
    </div>

</section>

<div id="product-template" class="product hidden">
    <img />
    <h4></h4>
    <p class="brand"></p>
    <a href="javascript:;" class="remove"><i class="fa fa-trash-o"></i></a>
    <input type="hidden" name="products[]" />
</div>