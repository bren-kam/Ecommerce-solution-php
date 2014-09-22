<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Facebook Posts
                <div class="pull-right">
                    <a class="btn btn-success btn-sm" href="/social-media/facebook/posting/post/?smfbpid=<?php echo $page->id ?>">Add Post</a>
                </div>
            </header>

            <div class="panel-body">

                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/social-media/facebook/posting/list-posts/" perPage="30,50">
                        <thead>
                            <th>Summary</th>
                            <th>Status</th>
                            <th sort="1 desc">Date Created</th>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>