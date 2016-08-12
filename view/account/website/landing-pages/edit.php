<?php
/**
 * @package Grey Suit Retail
 * @page Edit Page
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AccountPage $page
 * @var string $page_title
 * @var array $files
 * @var string $js_validation
 * @var string $errs
 * @var int $product_count
 * @var string $contact_validation
 * @var bool $hide_sidebar
 */

$upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
$search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
$delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );
nonce::field( 'set_pagemeta', '_set_pagemeta' );
?>

<form id="fEditPage" action="/website/edit/?apid=<?php echo $page->id ?>" method="post" role="form" data-account-page-id="<?php echo $page->id ?>">

    <div class="row-fluid">
        <div class="col-lg-12">
            <section class="panel">
                <header class="panel-heading">
                    Edit Page
                </header>


                <div class="panel-body">
                    <?php if ( $errs ) { ?>
                        <div class="alert alert-danger">
                            <?php echo $errs; ?>
                        </div>
                    <?php } ?>

                    <div class="form-group">
                        <label for="tTitle">Title:</label>
                        <input type="text" class="form-control" name="tTitle" id="tTitle" placeholder="Title" value="<?php echo $page->title ?>"/>
                    </div>

                    <p><strong>Link:</strong> http://<?php echo $user->account->domain ?>/<input type="text" id="tPageSlug" name="tPageSlug" value="<?php echo $page->slug ?>" placeholder="Slug" />/</p>
                    <p>
			<button type="button" class="btn-sm btn-primary btn" data-toggle="modal" data-target="#pageBuilderModal">
			    Launch demo modal
			</button>
                    </p>


                        <input type="hidden"  name="taContent" id="taContent" rows="3" cols="60" rte="1" value="<?php echo $page->content ?>">

                    <p>
                        <button type="button" class="btn btn-xs btn-default" title="Open Media Manager" data-media-manager data-upload-url="<?php echo $upload_url ?>" data-search-url="<?php echo $search_url ?>" data-delete-url="<?php echo $delete_url ?>">Upload File</button>
                    </p>

                    <p class="clearfix">
                        <?php nonce::field( 'edit' ) ?>
                        <button type="submit" class="btn btn-primary pull-right">Save</button>
                    </p>
                </div>
            </section>

            <?php include __DIR__ . '/edit/_products.php' ?>

            <?php
            if ( in_array ($page->slug, array( 'contact-us', 'current-offer', 'financing' ) ) )
                include __DIR__ . '/edit/_' . $page->slug . '.php';
            ?>

        </div>

    </div>



</form>
<!-- Modal -->
<div class="modal fade" id="pageBuilderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        <p> <iframe style="width:100%;height:786px;border:0;" src="/website/landing-pages/builder/"></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

