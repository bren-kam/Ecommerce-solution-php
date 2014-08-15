<?php
nonce::field( 'store_session', '_store_session' );
?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Reaches
            </header>

            <div class="panel-body">

                <div class="row">
                    <div class="col-lg-4">
                        <select id="status" class="form-control">
                            <option value="0">Open</option>
                            <option value="1">Closed</option>
                        </select>
                    </div>
                </div>

                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/products/reaches/list-reaches/" perPage="30,50,100">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Assigned To</th>
                                <th>Waiting</th>
                                <th>Priority</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>