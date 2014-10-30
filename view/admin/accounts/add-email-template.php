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
                <h3>Add Email Template: <?php echo $account->title ?></h3>
            </header>

            <div class="panel-body">
                <?php echo $form ?>
            </div>
        </section>
    </div>
</div>