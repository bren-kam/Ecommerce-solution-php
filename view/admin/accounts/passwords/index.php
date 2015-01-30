<?php
/**
 * @package Grey Suit Retail
 * @page Edit Account > Passwords
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Account $account
 * @var string $errs
 * @var array $passwords
 */

nonce::field( 'get', '_get' );
nonce::field( 'delete', '_delete' );
nonce::field( 'add_edit', '_add_edit' );
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                <ul class="nav nav-tabs tab-bg-dark-navy-blue" role="tablist">
                    <li><a href="/accounts/edit/?aid=<?php echo $account->id ?>">Account</a></li>
                    <li><a href="/accounts/website-settings/?aid=<?php echo $account->id ?>">Website</a></li>
                    <li><a href="/accounts/other-settings/?aid=<?php echo $account->id ?>">Other</a></li>
                    <li><a href="/accounts/actions/?aid=<?php echo $account->id ?>">Actions</a></li>

                    <?php if ( $account->craigslist ): ?>
                        <div class="tab-link"><a href="/accounts/craigslist/?aid=<?php echo $account->id; ?>" title="<?php echo _('Craigslist'); ?>"><?php echo _('Craigslist'); ?></a></div>
                    <?php endif; ?>

                    <?php if ( $user->has_permission( User::ROLE_SUPER_ADMIN ) ): ?>
                        <li><a href="/accounts/dns/?aid=<?php echo $account->id ?>">DNS</a></li>
                    <?php endif; ?>

                    <li><a href="/accounts/notes/?aid=<?php echo $account->id ?>">Notes</a></li>
                    <li class="active"><a href="/accounts/passwords/?aid=<?php echo $account->id ?>">Passwords</a></li>
                    <li class="dropdown">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">Customize <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="/accounts/customize/settings/?aid=<?php echo $account->id ?>">Settings</a></li>
                            <li><a href="/accounts/customize/stylesheet/?aid=<?php echo $account->id ?>">LESS/CSS</a></li>
                            <li><a href="/accounts/customize/favicon/?aid=<?php echo $account->id ?>">Favicon</a></li>
<!--                            <li><a href="/accounts/customize/ashley-express-shipping-prices/?aid=--><?php //echo $account->id ?><!--">Ashley Express - Shipping Prices</a></li>-->
                        </ul>
                    </li>
                </ul>
                <h3>Passwords: <?php echo $account->title ?></h3>
            </header>

            <div class="panel-body">

            <?php if ( $errs ): ?>
                <div class="alert alert-danger"><?php echo $errs ?></div>
            <?php endif; ?>

                <form name="fEditPassword" id="fEditPassword" action="" method="post" role="form">

                    <div class="adv-table">
                        <table class="display table table-bordered table-striped" ajax="/accounts/passwords/list-passwords/?aid=<?php echo $account->id ?>" perPage="30,50,100" id="datatable-passwords">
                            <thead>
                                <tr>
                                    <th sort="1">Title</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>URL</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Title</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>URL</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <p><a id="create-password" href="/accounts/passwords/add-edit/?aid=<?php echo $account->id ?>" class="btn btn-primary" title="<?php echo _('Create Password'); ?>" data-modal><?php echo _('Create Password'); ?></a></p>
                </form>

            </div>
        </section>
    </div>
</div>


