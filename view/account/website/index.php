<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Website Pages
                <a href="/website/add" class="btn btn-primary btn-sm pull-right"><i class="fa fa-plus"></i> New Page</a>
            </header>

            <div class="panel-body">

                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/website/list-pages/" perPage="30,50,100">
                        <thead>
                        <th sort="1">Title</th>
                        <th>Updated</th>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>