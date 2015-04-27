<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Remarketing - Carts
                <div class="pull-right">
                    <a class="btn btn-primary" href="/shopping-cart/orders/download/">Download as CSV</a>
                </div>
            </header>

            <div class="panel-body">

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