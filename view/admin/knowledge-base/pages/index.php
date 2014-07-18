<?php
/**
 * @package Grey Suit Retail
 * @page List Pages
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $link
 * @var string $kb_section
 */
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <?php echo ucwords( $_GET['s'] ) . ' Pages ' . $link;  ?>
            </header>

            <div class="panel-body">
                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="<?php echo url::add_query_arg( 'section', $_GET['s'], '/knowledge-base/pages/list-all/' ); ?>" perPage="30,50,100">
                        <thead>
                        <th sort="2">Page</th>
                        <th sort="1">Category</th>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>