<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit a Company
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 */
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                <?php echo $template->v('title') ?>
            </header>
            <div class="panel-body">

                <?php echo $form ?>

            </div>
        </section>
    </div>
</div>