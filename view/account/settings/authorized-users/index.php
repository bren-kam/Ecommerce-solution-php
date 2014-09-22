<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Authorized Users
                <a class="pull-right btn btn-sm btn-primary" href="/settings/authorized-users/add-edit/"><i class="fa fa-plus"></i> Add New User</a>
            </header>

            <div class="panel-body">

                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/settings/authorized-users/list-all/" perPage="30,50,100">
                        <thead>
                            <th sort="1">Email</th>
                            <th>Pages</th>
                            <th>Products</th>
                            <th>Analytics</th>
                            <th>Blog</th>
                            <th>Email Marketing</th>
                            <th>Shopping Cart</th>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>