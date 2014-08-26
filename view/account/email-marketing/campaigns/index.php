<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Campaigns
                <a href="/email-marketing/campaigns/create/" class="btn btn-sm btn-success pull-right "><i class="fa fa-plus"></i> Create a new Campaign</a>
            </header>

            <div class="panel-body">

                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/email-marketing/campaigns/list-all/" perPage="30,50,100">
                        <thead>
                            <th sort="1">Subject</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Scheduled To</th>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>