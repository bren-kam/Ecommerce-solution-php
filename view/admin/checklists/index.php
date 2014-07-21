<?php
/**
 * @package Grey Suit Retail
 * @page List Checklists
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

?>

<?php nonce::field( 'store_session', '_store_session' ); ?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Checklists
            </header>

            <div class="panel-body">

                <div class="clearfix">
                    <p class="pull-right">
                        <select class="form-control" id="sCompleted">
                            <option value="0">Show incomplete Tasks</option>
                            <option value="1">Show completed Tasks</option>
                        </select>
                    </p>
                </div>

                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/checklists/list-all/" perPage="30,50,100">
                        <thead>
                            <th sort="1">Days Left</th>
                            <th>Account</th>
                            <th>Online Specialist</th>
                            <th>Type</th>
                            <th>Date Created</th>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
