<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit an Article
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 * @var array $files
 */

?>

    <div class="row-fluid">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    <?php echo isset( $_GET['kbaid'] ) ? 'Edit Article' : 'Add Article'  ?>
                </header>

                <div class="panel-body">
                    <?php echo $form ?>
                </div>
            </section>
        </div>
    </div>


<?php
nonce::field( 'get_categories', '_get_categories' );
nonce::field( 'get_pages', '_get_pages' );
?>