<?php nonce::field( 'autocomplete', '_autocomplete' ); ?>
<?php nonce::field( 'store_session', '_store_session' ); ?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Narrow your search
            </header>

            <div class="panel-body">

                <form class="form-inline" role="form">
                    <div class="form-group">
                        <select class="form-control" id="state">
                            <option value="all"><?php echo _('All Accounts'); ?></option>
                            <option value="live"><?php echo _('Live'); ?></option>
                            <option value="staging"><?php echo _('Staging'); ?></option>
                            <option value="inactive"><?php echo _('Inactive'); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control"id="sAutoComplete">
                            <option value="title"><?php echo _('Account Name'); ?></option>
                            <option value="domain"><?php echo _('Account Domain'); ?></option>
                            <option value="store_name"><?php echo _('Store Name'); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="tAutoComplete" placeholder="<?php echo _('Enter Name...'); ?>" />
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" id="aSearch">Search</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Accounts
            </header>

            <div class="panel-body">

                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/accounts/list-all/" perPage="30,50,100">
                        <thead>
                            <tr>
                                <th></th>
                                <th sort="1">Account</th>
                                <th>User Name</th>
                                <th>Online Specialist</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Account</th>
                                <th>User Name</th>
                                <th>Online Specialist</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
<!-- page end-->
