<?php
/**
 * @package Grey Suit Retail
 * @page Edit Account
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Account $account
 * @var User $owner
 * @var array $address
 * @var array $checkboxes
 * @var string $errs
 * @var array $users
 * @var array $os_users
 * @var array $checkboxes
 */
?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                <ul class="nav nav-tabs tab-bg-dark-navy-blue" role="tablist">
                    <li class="active"><a href="/accounts/edit/?aid=<?php echo $account->id ?>">Account</a></li>
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
                    <li><a href="/accounts/passwords/?aid=<?php echo $account->id ?>">Passwords</a></li>
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
                <h3>Account Information: <?php echo $account->title ?></h3>
            </header>
            <div class="panel-body">



                <?php if ( $errs ): ?>
                    <div class="alert alert-danger">
                        <?php echo $errs; ?>
                    </div>
                <?php endif; ?>

                <form id="fEditAccount" role="form" method="post">

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="tTitle">Title</label>
                                <input type="text" id="tTitle" name="tTitle" class="form-control" placeholder="Enter title" value="<?php echo $account->title ?>" data-bv-notempty data-bv-notempty-message="Title is required" />
                            </div>
                            <div class="form-group">
                                <label for="tPhone">Phone</label>
                                <input type="text" id="tPhone" name="tPhone" class="form-control" placeholder="Enter phone" value="<?php echo $account->phone ?>" />
                            </div>
                            <div class="form-group">
                                <label for="tProducts">Products</label>
                                <input type="number" id="tProducts" name="tProducts" class="form-control" placeholder="Enter # of products" value="<?php echo $account->products ?>" min="0"  data-bv-notempty data-bv-notempty-message="# of products is required" data-bv-integer data-bv-integer-message="# of products must be a number" />
                            </div>
                            <div class="form-group">
                                <label for="tPlan">Plan</label>
                                <input type="text" id="tPlan" name="tPlan" class="form-control" placeholder="Enter plan" value="<?php echo $account->plan_name ?>" />
                            </div>
                            <div class="form-group">
                                <label for="tAddress">Address</label>
                                <input type="text" id="tAddress" name="tAddress" class="form-control" placeholder="Address" value="<?php echo $address['address'] ?>" />
                            </div>
                            <div class="form-group form-group-address clearfix">
                                <label for="tCity">City</label>
                                <input type="text" id="tCity" name="tCity" class="form-control" placeholder="City" value="<?php echo $address['city'] ?>" />
                                <select id="sState" name="sState" class="form-control">
                                    <?php foreach ($states as $k => $v): ?>
                                        <option value="<?php echo $k ?>" <?php if ($k==$address['state']) echo 'selected' ?>><?php echo $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="number" id="tZip" name="tZip" class="form-control" placeholder="Zip Code" value="<?php echo $address['zip'] ?>" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="sUserID">Owner</label>
                                <select class="form-control" id="sUserID" name="sUserID">
                                    <?php foreach ($users as $k => $v): ?>
                                        <option value="<?php echo $k ?>" <?php if ($k==$account->user_id) echo 'selected' ?>><?php echo $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input class="form-control" disabled value="<?php echo $owner->email ?>" />
                            </div>
                            <div class="form-group">
                                <label for="sOSUserID">Online Specialist</label>
                                <select class="form-control" id="sOSUserID" name="sOSUserID">
                                    <?php foreach ($os_users as $k => $v): ?>
                                        <option value="<?php echo $k ?>" <?php if ($k==$account->os_user_id) echo 'selected' ?>><?php echo $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="taPlanDescription">Plan Description</label>
                                <textarea id="taPlanDescription" name="taPlanDescription" class="form-control" rows="3"><?php echo $account->plan_description?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <?php foreach ( $checkboxes as $feature ): ?>
                            <div class="col-lg-6">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="<?php echo $feature['form_name']?>" name="<?php echo $feature['form_name']?>" data-toggle="switch" value="1" <?php if ($feature['selected']) echo 'checked' ?>/>
                                        <?php echo $feature['name'] ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-lg btn-primary pull-right">Save</button>
                        </div>
                    </div>

                    <?php nonce::field('edit') ?>
                </form>
            </div>
        </section>
    </div>
</div>