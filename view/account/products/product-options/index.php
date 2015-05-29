<?php
/**
 * @package Grey Suit Retail
 * @page List Product options
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Product Options
            </header>

            <div class="panel-body">
                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/products/product-options/list-all/" perPage="30,50,100">
                        <thead>
                            <th>#</th>
                            <th sort="1">SKU</th>
                            <th>Name</th>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
