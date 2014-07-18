<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit a Page
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
                <?php echo isset( $_GET['kbpid'] ) ? 'Edit Page' : 'Add Page';  ?>
            </header>

            <div class="panel-body">
                <?php
                    echo $form;
                    nonce::field( 'get_categories', '_get_categories' );
                ?>
            </div>
        </section>
    </div>
</div>