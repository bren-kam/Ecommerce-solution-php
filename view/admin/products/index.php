<?php
/**
 * @package Grey Suit Retail
 * @page List Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var User[] $product_users
 * @var array $categories
 */
?>

<?php nonce::field( 'autocomplete', '_autocomplete' ); ?>
<?php nonce::field( 'store_session', '_store_session' ); ?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Narrow your search
            </header>

            <div class="panel-body">

                <form class="form-inline" id="product-search" role="form">
                    <fieldset>
                        <div class="form-group">
                            <select class="form-control" id="user-option">
                                <option value="all">-- Select Option --</option>
                                <option value="created">Created By</option>
                                <option value="modified">Modified By</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control"id="user">
                                <option value="all">All Users</option>
                                <?php if ( is_array( $product_users ) )
                                        foreach ( $product_users as $product_user ):  ?>
                                    <option value="<?php echo $product_user->id ?>"><?php echo $product_user->contact_name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select class="form-control" id="visibility">
                                <option value="">All Products</option>
                                <option value="public">Public Products</option>
                                <option value="private">Private Products</option>
                                <option value="deleted">Deleted Products</option>
                            </select>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group">
                            <select class="form-control" id="sAutoComplete">
                                <option value="sku"><?php echo _('SKU'); ?></option>
                                <option value="products"><?php echo _('Product Name'); ?></option>
                                <option value="brands"><?php echo _('Brands'); ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="tAutoComplete" placeholder="Enter Search..." />
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="form-group">
                            <select class="form-control" id="cid">
                                <option value="0">-- <?php echo _('Select a Category'); ?> --</option>
                                <?php foreach ( $categories as $category ): ?>
                                    <option value="<?php echo $category->id; ?>"><?php echo str_repeat( '&nbsp;', $category->depth * 5 ), $category->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" id="aSearch">Search</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Products
            </header>

            <div class="panel-body">

                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/products/list-all/" perPage="30,50,100">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Brand</th>
                                <th>SKU</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Name</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Brand</th>
                                <th>SKU</th>
                                <th>Category</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
<!-- page end-->
