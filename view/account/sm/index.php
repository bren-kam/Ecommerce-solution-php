<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Accounts | Social Media

                <div class="pull-right">
                    <div class="dropdown">
                        <button class="btn btn-default dropdown-toggle" type="button" id="ddAddSMAccount" data-toggle="dropdown" aria-expanded="true">
                            <i class="fa fa-plus"></i>
                            Add Social Media Account
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="ddAddSMAccount">
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="/sm/facebook-connect/?website-id=<?php echo $user->account->id ?>&amp;user-id=<?php echo $user->id ?>"><i class="fa fa-facebook"></i> Facebook</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="/sm/twitter-connect/?website-id=<?php echo $user->account->id ?>&amp;user-id=<?php echo $user->id ?>"><i class="fa fa-twitter"></i> Twitter</a></li>
                        </ul>
                    </div>
                </div>

            </header>

            <div class="panel-body">

                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/sm/list-all/" perPage="30,50,100">
                        <thead>
                        <th>Title</th>
                        <th>Social Media</th>
                        <th>Created</th>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
