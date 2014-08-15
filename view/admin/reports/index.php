<?php /**
 * @package Grey Suit Retail
 * @page Search Reports
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

nonce::field( 'autocomplete', '_autocomplete' );
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Narrow your search
            </header>

            <div class="panel-body">

                <form id="report-form" class="form-inline" role="form">
                    <fieldset>
                        <div class="form-group">
                            <div class="form-group">
                                <select class="form-control" id="services">
                                    <option value="">-- Add Service --</option>
                                    <?php foreach ( $services as $k => $v ): ?>
                                        <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <select class="form-control" id="type">
                                <option value="brand">Brand</option>
                                <option value="online_specialist">Online Specialist</option>
                                <option value="marketing_specialist">Marketing Specialist</option>
                                <?php if ( $user->has_permission( User::ROLE_ADMIN ) ): ?>
                                    <option value="company">Company</option>
                                <?php endif; ?>
                                <option value="billing_state">State</option>
                                <option value="package">Package</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="tAutoComplete" placeholder="Search..."  />
                        </div>
                        <div class="form-group">
                            <button type="submit" id="search" class="btn btn-primary">Run Report</button>
                        </div>
                    </fieldset>
                    <ul class="list-inline" id="criteria-list">
                        <li>Current Criteria:</li>
                    </ul>
                    <?php nonce::field( 'search' ); ?>
                </form>
            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <div class="panel-body">
                <table id="report" class="display table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Company</th>
                            <th>Products</th>
                            <th>Signed Up</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
