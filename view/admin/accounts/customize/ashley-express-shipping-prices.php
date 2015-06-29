
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <ul class="nav nav-tabs tab-bg-dark-navy-blue" role="tablist">
                    <li><a href="/accounts/edit/?aid=<?php echo $account->id ?>">Account</a></li>
                    <li><a href="/accounts/website-settings/?aid=<?php echo $account->id ?>">Website</a></li>
                    <li><a href="/accounts/other-settings/?aid=<?php echo $account->id ?>">Other</a></li>
                    <li><a href="/accounts/actions/?aid=<?php echo $account->id ?>">Actions</a></li>

                    <?php if ( $user->has_permission( User::ROLE_SUPER_ADMIN ) ): ?>
                        <li><a href="/accounts/dns/?aid=<?php echo $account->id ?>">DNS</a></li>
                    <?php endif; ?>

                    <li><a href="/accounts/notes/?aid=<?php echo $account->id ?>">Notes</a></li>
                    <li class="dropdown active">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">Customize <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="/accounts/customize/settings/?aid=<?php echo $account->id ?>">Settings</a></li>
                            <li><a href="/accounts/customize/stylesheet/?aid=<?php echo $account->id ?>">LESS/CSS</a></li>
                            <li><a href="/accounts/customize/favicon/?aid=<?php echo $account->id ?>">Favicon</a></li>
                            <li><a href="/accounts/customize/ashley-express-shipping-prices/?aid=<?php echo $account->id ?>">Ashley Express - Shipping Prices</a></li>
                        </ul>
                    </li>
                </ul>

                <h3>
                    Import - Ashley Express shipping prices <small><?php echo $account->title ?></small>
                </h3>
            </header>

            <div class="panel-body">

                <p>The Excel/CSV must contain <strong>*only*</strong> the following columns:</p>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Ashley Item</th>
                            <th>Estimated Express Freight Per Carton</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>&lt;SKU&gt;</td>
                            <td>&lt;AE Shipping Price&gt;</td>
                        </tr>
                    </tbody>
                </table>

                <form method="post" enctype="multipart/form-data" role="form">
                    <div class="form-group">
                        <label for="file">XLS/CSV File:</label>
                        <input type="file" name="file" />
                    </div>

                    <p>
                        <?php nonce::field( 'ashley_express_shipping_prices' ); ?>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </p>
                </form>

            </div>
        </section>
    </div>
</div>