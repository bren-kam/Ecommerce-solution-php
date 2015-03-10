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

$upload_url = '/users/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/users/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/users/delete-file/?_nonce=' . nonce::create( 'delete_file' );

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

<?php if ( $user_id ): ?>
    <div class="row-fluid">
        <div class="col-lg-12">
            <section class="panel">

                <header class="panel-heading">
                    User Photo
                </header>
                <div class="panel-body">

                    <div class="row">
                        <div class="col-lg-2">
                            <img id="profile-picture" class="img-responsive" src="<?php echo $user_photo ? $user_photo : '//placehold.it/200x200&text=No+Photo' ?>"
                        </div>
                        <div class="col-lg-10"></div>
                    </div>

                    <p>
                        <button type="button"
                                class="btn btn-xs btn-default"
                                title="Open Media Manager"
                                data-media-manager=""
                                data-upload-url="<?php echo $upload_url ?>"
                                data-search-url="<?php echo $search_url ?>"
                                data-delete-url="<?php echo $delete_url ?>"
                                data-submit-text="Set as Profile Picture">
                            Change Profile Picture
                        </button>

                        <input type="hidden" id="update-photo-nonce" value="<?php echo nonce::create('update_photo') ?>" data-user-id="<?php echo $user_id ?>"/>
                    </p>

                </div>
            </section>
        </div>
    </div>
<?php endif; ?>

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
