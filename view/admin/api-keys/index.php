<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                List API Keys
            </header>

            <div class="panel-body">
                <p class="text-right">
                    <a class="btn btn-primary" href="/api-keys/manage/"><i class="fa fa-plus"></i> API Key</a>
                </p>
                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/api-keys/list-all/" perPage="30,50,100">
                        <thead>
                        <th>#</th>
                        <th>Company</th>
                        <th>Domain</th>
                        <th>Status</th>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>