<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Import Products for <?php echo $location->name ?>
            </header>

            <div class="panel-body">

                <?php if ( $current_product_list ): ?>
                    <div class="alert alert-warning">
                        You already have a product list on this Location. Download the CSV file <a href="<?php echo $current_product_list ?>">here</a><br>
                        Only one list of products can be created per location.  To update your current list of product, upload a new spreadsheet.
                    </div>
                <?php endif; ?>

                <form method="post" role="form" enctype="multipart/form-data">

                    <?php if ( isset( $success ) ): ?>
                        <?php if ( $success ): ?>
                            <div class="alert <?php echo $skipped ? 'alert-warning' : 'alert-success' ?>">
                                Product List Imported.
                                <?php if ( $skipped ) echo count( $skipped ) . ' products were skipped as "ProductName" is required.' ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                There was an error uploading your product list, please try again.
                                <?php echo $response->errors[0]->message ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="csv">Product File - CSV Format - Download an import template <a href="http://websites.retailcatalog.us/1352/mm/product-import-template.csv">here</a>:</label>
                        <input type="file" name="csv" />
                    </div>

                    <p>
                        <?php nonce::field( 'import_products' ) ?>
                        <button type="submit" class="btn btn-primary">Import Products</button>
                    </p>
                </form>

            </div>
        </section>
    </div>
</div>