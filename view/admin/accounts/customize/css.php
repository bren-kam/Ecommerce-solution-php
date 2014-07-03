<?php
/**
 * @package Grey Suit Retail
 * @page CSS | Customize
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $less
 * @var Account $account
 * @var string|bool $unlocked_less
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
                    <li><a href="/accounts/actions/?aid=<?php echo $account->id ?>">Actions</a></li>

                    <?php if ( $account->craigslist ): ?>
                        <div class="tab-link"><a href="/accounts/craigslist/?aid=<?php echo $account->id; ?>" title="<?php echo _('Craigslist'); ?>"><?php echo _('Craigslist'); ?></a></div>
                    <?php endif; ?>

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
                        </ul>
                    </li>
                </ul>
                <h3>CSS/LESS: <?php echo $account->title ?></h3>
            </header>

            <div class="panel-body">

                <?php
                nonce::field('save_less');
                if ( $unlocked_less ): ?>

                    <h3><?php echo _('Core LESS'); ?></h3>
                    <div id="core-container">
                        <div id="core"><?php echo $unlocked_less; ?></div>
                    </div>

                    <h3><?php echo _('LESS'); ?></h3>

                <?php endif; ?>

                <div id="editor-container">
                    <div id="editor"><?php echo $less; ?></div>
                </div>

                <p><a href="<?php echo url::add_query_arg( 'aid', $account->id, '/accounts/customize/save-less/' ); ?>" class="btn btn-primary btn-lg" id="save-less" title="<?php echo _('Save'); ?>"><?php echo _('Save'); ?></a></p>

            </div>
        </section>
    </div>
</div>
