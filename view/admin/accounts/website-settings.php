<?php
/**
 * @package Grey Suit Retail
 * @page Edit Account > Website Settings
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Account $account
 */

?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                <ul class="nav nav-tabs tab-bg-dark-navy-blue" role="tablist">
                    <li><a href="/accounts/edit/?aid=<?php echo $account->id ?>">Account</a></li>
                    <li class="active"><a href="/accounts/website-settings/?aid=<?php echo $account->id ?>">Website</a></li>
                    <li><a href="/accounts/other-settings/?aid=<?php echo $account->id ?>">Other</a></li>
                    <li><a href="/accounts/actions/?aid=<?php echo $account->id ?>">Actions</a></li>

                    <?php if ( $account->craigslist ): ?>
                        <div class="tab-link"><a href="/accounts/craigslist/?aid=<?php echo $account->id; ?>" title="<?php echo _('Craigslist'); ?>"><?php echo _('Craigslist'); ?></a></div>
                    <?php endif; ?>

                    <?php if ( $user->has_permission( User::ROLE_SUPER_ADMIN ) ): ?>
                        <li><a href="/accounts/dns/?aid=<?php echo $account->id ?>">DNS</a></li>
                    <?php endif; ?>
                </ul>
                <h3>Website Settings: <?php echo $account->title ?></h3>
            </header>

            <div class="panel-body">
                <?php echo $form ?>
            </div>
        </section>
    </div>
</div>