<?php
/**
 * @package Grey Suit Retail
 * @page Add/Edit Password -- Dialog
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AccountPassword $account_password
 * @var string $title
 * @var string $username
 * @var string $password
 * @var string $url
 * @var string $notes
 */

$add_edit_url = '/accounts/passwords/add-edit/';
$delete_url = '/accounts/passwords/delete/';

if ( $account_password->id ){
    $add_edit_url = url::add_query_arg( array( 'pid' => $account_password->id, 'aid' => $_GET['aid'] ), $add_edit_url );
    $delete_url = url::add_query_arg( array( 'pid' => $account_password->id, 'aid' => $_GET['aid'] ), $delete_url );
}else{
    $add_edit_url = url::add_query_arg( array( 'aid' => $_GET['aid'] ), $add_edit_url );
    $delete_url = url::add_query_arg( array( 'aid' => $_GET['aid'] ), $delete_url );
}

?>

<form name="fAddEditPassword" id="fAddEditPassword" action="<?php echo $add_edit_url; ?>" method="post" role="form">

    <!-- Modal -->
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="modalLabel"><?php echo $account_password->id ? 'Edit' : 'Add'?> Password</h4>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label for="sTitle">Title:</label>
                    <input type="text" class="form-control" name="sTitle" id="sTitle" value="<?php echo $title ?>" placeholder="Title" />
                </div>

                <div class="form-group">
                    <label for="tUsername">Username:</label>
                    <input type="text" class="form-control" name="tUsername" id="tUsername" value="<?php echo $username ?>" placeholder="Username" />
                </div>

                <div class="form-group">
                    <label for="tPassword">Password (<a href="#" id="generate-password">Generate Password</a>):</label>
                    <input type="text" class="form-control" name="tPassword" id="tPassword" value="<?php echo $password ?>" placeholder="Password" />
                </div>

                <div class="form-group">
                    <label for="sUrl">URL:</label>
                    <input type="text" class="form-control" name="sUrl" id="sUrl" value="<?php echo $url ?>" placeholder="URL" />
                </div>

                <div class="form-group">
                    <label for="tNotes">Notes:</label>
                    <textarea type="text" class="form-control" name="tNotes" id="tNotes" placeholder="Notes"><?php echo $notes ?></textarea>
                </div>

            </div>
            <div class="modal-footer">
                <?php if( $account_password->id ){ ?>
                <a type="button" class="btn btn-warning delete-password" href="<?php echo $delete_url ?>">Delete</a>
                <?php } ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>

</form>
