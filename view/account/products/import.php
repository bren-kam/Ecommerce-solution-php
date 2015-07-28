<?php
/**
 * @package Grey Suit Retail
 * @page Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var Brand[] $brands
 */
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Import Products
            </header>

            <div class="panel-body">

                <div id="dForm">

                    <p>On this page you can import a list of products.</p>

                    <p>
                        <button type="button" id="aUpload" class="btn btn-lg btn-primary">Select File</button>
                        <div class="progress progress-sm hidden" id="upload-loader">
                            <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </p>

                    <!-- Where uploader lives -->
                    <div class="hidden" id="import-products"></div>
                    <?php nonce::field( 'prepare_import', '_prepare_import' ); ?>
                </div>

                <div id="dConfirm" class="hidden">

                    <p>Please verify the following information is correct and confirm.</p>

                    <table id="tUploadOverview" class="table table-bordered table-stripped table-non-fluid">
                    </table>

                    <div id="dSkippedRows" class="hidden">

                        <div class="alert alert-warning">The following products WILL NOT BE IMPORTED due to validation errors:</div>

                        <table class="table table-bordered table-stripped">
                        </table>
                    </div>

                    <form action="/products/confirm-import/" method="post" >
                        <?php nonce::field( 'confirm_import' ); ?>
                        <input type="submit" class="btn btn-lg btn-primary" value="Confirm Import" />
                    </form>

                </div>


            </div>
        </section>
    </div>
</div>

