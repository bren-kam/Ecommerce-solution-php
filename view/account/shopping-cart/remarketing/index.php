<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Remarketing - Carts
            </header>

            <div class="panel-body">

                <?php if ( $overview['total_count'] ): ?>
                    <h4>Last Month Overview</h4>
                    <div class="progress">
                        <div class="progress-bar" style="width: <?php echo $overview['converted_count'] / $overview['total_count'] * 100 ?>%">
                            <?php echo $overview['converted_count'] ?> out of <?php echo $overview['total_count'] ?> Carts Converted
                        </div>
                    </div>
                    <div class="progress">
                        <div class="progress-bar progress-bar-success" style="width: <?php echo $overview['converted_amount'] / $overview['total_amount'] * 100 ?>%">
                            $<?php echo number_format($overview['converted_amount'], 2) ?> out of $<?php echo number_format($overview['total_amount'], 2) ?> in carts converted
                        </div>
                    </div>
                <?php endif; ?>

                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/shopping-cart/remarketing/list-carts/" perPage="30,50,100">
                        <thead>
                            <th sort="1 desc">Cart #</th>
                            <th>Name</th>
                            <th>Product</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Last Update</th>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>