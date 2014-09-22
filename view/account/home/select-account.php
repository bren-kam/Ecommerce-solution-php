<?php
/**
 * @package Grey Suit Retail
 * @page Select Account
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

$return_url = $_SERVER['REQUEST_URI'];

if ( !empty( $_SERVER['QUERY_STRING'] ) )
    $return_url .= '?' . $_SERVER['QUERY_STRING'];

?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                Select Account
            </header>
            <div class="panel-body">

                <ul>
                    <?php foreach ( $user->accounts as $account ): ?>
                        <li><a href="/home/change-account/?aid=<?php echo $account->id; ?>" title="<?php echo _('Change Account'); ?>"><strong><?php echo $account->title; ?></strong> - <?php echo $account->domain; ?></a></li>
                    <?php endforeach; ?>
                </ul>

            </div>
        </section>
    </div>
</div>