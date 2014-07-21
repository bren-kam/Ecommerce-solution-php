<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit a user
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $form
 * @var int $user_id
 */
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                Add/Edit User
            </header>
            <div class="panel-body">

                <?php echo $form; ?>

            </div>
        </section>
    </div>
</div>

<?php if ( $user->has_permission( User::ROLE_ONLINE_SPECIALIST ) && isset( $_GET['uid'] ) ): ?>

    <div class="row-fluid">
        <div class="col-lg-12">
            <section class="panel">

                <header class="panel-heading">
                    Knowledge Base Articles
                </header>

                <div class="panel-body">

                    <div class="adv-table">
                        <table class="display table table-bordered table-striped" ajax="<?php echo url::add_query_arg( 'uid', $user_id, '/users/list-articles/' ); ?>" perPage="30,50,100">
                            <thead>
                                <tr>
                                    <th sort="1">Article</th>
                                    <th>Section</th>
                                    <th>Category</th>
                                    <th>Page</th>
                                    <th>Views</th>
                                    <th>Helpful</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>

                </div>
            </section>
        </div>
    </div>
<?php endif; ?>
