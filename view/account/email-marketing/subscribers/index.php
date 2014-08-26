<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Subscribers
            </header>

            <div class="panel-body">

                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/email-marketing/subscribers/list-all/?s=1<?php if ( isset( $_GET['elid'] ) ) echo '&elid=' . $_GET['elid']; ?>" perPage="30,50,100">
                        <thead>
                        <th sort="1">Email</th>
                        <th>Name</th>
                        <th>Signup Date</th>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>