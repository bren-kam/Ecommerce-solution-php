<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Website Pages
                <?php if ( $user->has_permission( User::ROLE_ONLINE_SPECIALIST ) ): ?>
                    <a href="/website/add/" class="btn btn-primary btn-sm pull-right" data-modal><i class="fa fa-plus"></i> <span class="hidden-xs">New Page</span></a>
                <?php endif; ?>
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