<?php nonce::field( 'store_session', '_store_session' ); ?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Tickets
            </header>

            <div class="panel-body" id="ticket-container">

                <form class="form-inline col-md-offset-3" role="form">
                    <div class="form-group">
                        <select class="form-control input-sm" id="sStatus">
                            <option value="0">Open Tickets</option>
                            <option value="1">Closed Tickets</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control  input-sm" id="sAssignedTo">
                            <option value="0">All</option>
                            <option value="-1">Peers</option>
                            <?php foreach ( $assigned_to_users as $atu ): ?>
                                <option value="<?php echo $atu->id; ?>"<?php if ( $user->has_permission( User::ROLE_ADMIN ) && $atu->id == $user->id ) echo ' selected="selected"' ?>><?php echo $atu->contact_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>


                <div class="adv-table">
                    <table class="display table table-bordered table-striped" perPage="30,50,100">
                        <thead>
                            <tr>
                                <th>Summary</th>
                                <th>Name</th>
                                <th sort="3 asc">Website</th>
                                <th sort="1 desc">Priority</th>
                                <th>Assigned To</th>
                                <th sort="2 asc">Created</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Summary</th>
                                <th>Name</th>
                                <th>Website</th>
                                <th>Priority</th>
                                <th>Assigned To</th>
                                <th>Created</th>
                                <th>Updated</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
<!-- page end-->
