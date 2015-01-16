<?php
/**
 * @package Grey Suit Retail
 * @page Edit Account > Actions
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Account $account
 * @var array $settings
 */

?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                <ul class="nav nav-tabs tab-bg-dark-navy-blue" role="tablist">
                    <li><a href="/accounts/edit/?aid=<?php echo $account->id ?>">Account</a></li>
                    <li><a href="/accounts/website-settings/?aid=<?php echo $account->id ?>">Website</a></li>
                    <li><a href="/accounts/other-settings/?aid=<?php echo $account->id ?>">Other</a></li>
                    <li class="active"><a href="/accounts/actions/?aid=<?php echo $account->id ?>">Actions</a></li>

                    <?php if ( $account->craigslist ): ?>
                        <div class="tab-link"><a href="/accounts/craigslist/?aid=<?php echo $account->id; ?>" title="<?php echo _('Craigslist'); ?>"><?php echo _('Craigslist'); ?></a></div>
                    <?php endif; ?>

                    <?php if ( $user->has_permission( User::ROLE_SUPER_ADMIN ) ): ?>
                        <li><a href="/accounts/dns/?aid=<?php echo $account->id ?>">DNS</a></li>
                    <?php endif; ?>

                    <li><a href="/accounts/notes/?aid=<?php echo $account->id ?>">Notes</a></li>
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
                <h3>Other Settings: <?php echo $account->title ?></h3>
            </header>

            <div class="panel-body">

                <?php if ( 0 == $account->version ) { ?>
                    <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/install-website/' ); ?>" title="<?php echo _('Install Website'); ?>"><?php echo _('Install Website'); ?></a></p>
                <?php } else { ?>
                    <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/install-package/' ); ?>" title="<?php echo _('Install Package'); ?>"><?php echo _('Install Package'); ?></a></p>
                <?php } ?>

                <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/delete-categories-and-products/' ); ?>" title="<?php echo _('Delete Categories and Products'); ?>" confirm="<?php echo _('Are you sure you want to delete all categories and products? This cannot be undone.'); ?>"><?php echo _('Delete Categories and Products'); ?></a></p>

                <?php if ( empty( $settings['sendgrid-username'] ) ) { ?>
                    <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/create-email-marketing-account/' ); ?>" title="<?php echo _('Create Email Marketing Account'); ?>" ajax="1"><?php echo _('Create Email Marketing Account'); ?></a></p>
                <?php } else { ?>
                    <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/resync-email-lists/' ); ?>" title="<?php echo _('Resync Email Lists'); ?>"><?php echo _('Resync Email Lists'); ?></a></p>
                <?php } ?>

               <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/cancel/' ); ?>" title="<?php echo _('Cancel Account'); ?>" confirm="<?php echo _('Are you sure you want to deactivate this account?'); ?>"><?php echo _('Cancel Account'); ?></a></p>

                <?php if ( !$account->status ): ?>
                    <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/reactivate/' ); ?>" title="<?php echo _('Reactivate Account'); ?>" confirm="<?php echo _('Are you sure you want to reactivate this account?'); ?>"><?php echo _('Reactivate Account'); ?></a></p>
                <?php endif; ?>

                <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/run-ashley-feed/' ); ?>" title="<?php echo _('Run Ashley Feed'); ?>"><?php echo _('Run Ashley Feed'); ?></a></p>
                <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/reorganize-categories/' ); ?>" title="<?php echo _('Reorganize Categories'); ?>"><?php echo _('Reorganize Categories'); ?></a></p>
                <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/add-email-template/' ); ?>" title="<?php echo _('Add'); ?>"><?php echo _('Add Email Template'); ?></a></p>

                <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/reset-product-prices/' ); ?>" title="<?php echo _('Set all product prices to zero.'); ?>"><?php echo _('Reset all product prices'); ?></a></p>

                <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/purge_cache/' ); ?>" title="<?php echo _('Purge Varnish Cache'); ?>"><?php echo _('Purge cache'); ?></a></p>

                <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/run-ashley-express-feed/' ); ?>" title="<?php echo _('Run Express Ashley Feed'); ?>"><?php echo _('Run Ashley Express Feed'); ?></a></p>

                <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/run-ashley-express-order-status/' ); ?>" title="<?php echo _('Check Ashley Express Order Status'); ?>"><?php echo _('Check Ashley Express Order Status'); ?></a></p>

                <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/index-products/' ); ?>" title="<?php echo _('Re-Index Products'); ?>"><?php echo _('Re-Index Products'); ?></a></p>

                <?php if ( $settings['yext-subscription-id'] ) { ?>
                    <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/cancel-yext-subscription/' ); ?>" title="<?php echo _('Cancel YEXT Subscription'); ?>"><?php echo _('Cancel YEXT Subscription'); ?></a></p>
                <?php } ?>

            </div>
        </section>
    </div>
</div>


