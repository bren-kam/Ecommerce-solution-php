<?php
/**
 * @package Grey Suit Retail
 * @page Notes for an account
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Account $account
 * @var array $notes
 * @var BootstrapValidator $v
 *
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

                    <li class="active"><a href="/accounts/notes/?aid=<?php echo $account->id ?>">Notes</a></li>
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
                <h3>Notes: <?php echo $account->title ?></h3>
            </header>

            <div class="panel-body">

                <form name="fAddNote" id="fAddNote" method="post" action="/accounts/notes/?aid=<?php echo $_GET['aid']; ?>" role="form">
                    <div class="form-group">
                        <label for="taNote">Note:</label>
                        <textarea name="taNote" id="taNote" cols="50" rows="3" class="form-control"></textarea>
                    </div>

                    <p class="clearfix">
                        <button type="submit" class="btn btn-primary pull-right">Add Note</button>
                    </p>

                    <?php nonce::field( 'notes' ); ?>
                </form>
                <?php echo $v->js_validation(); ?>

            </div>
        </section>

        <?php
            $delete_note_url = url::add_query_arg( '_nonce', nonce::create('delete_note'), '/accounts/delete-note/' );

            if ( is_array( $notes ) )
            foreach ( $notes as $note ):

            $date = new DateTime( $note->date_created );
            $delete_note_url = url::add_query_arg( 'anid', $note->id, $delete_note_url );
        ?>

        <section class="panel">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-3">
                        <strong><?php echo $note->contact_name; ?></strong>
                        <br /><?php echo $date->format( 'F j, Y' ); ?>
                        <?php
                        if ( $note->user_id == $user->user_id )
                            echo '<br /><a href="', $delete_note_url, '" title="' . _('Delete') . '" class="delete-note">' . _('Delete') . '</a>';
                        ?>
                    </div>
                    <div class="col-lg-9">
                        <?php echo $note->message; ?>
                    </div>
                </div>
            </div>
        </section>

        <?php endforeach; ?>
    </div>
</div>


