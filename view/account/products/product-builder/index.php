<?php
nonce::field( 'store_session', '_store_session' );
?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                My Custom Products
            </header>

            <div class="panel-body">

                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/products/product-builder/list-products/" perPage="30,50,100">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Brand</th>
                                <th>SKU</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>