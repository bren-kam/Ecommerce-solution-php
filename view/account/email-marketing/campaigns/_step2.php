<?php
    nonce::field( 'autocomplete_owned', '_autocomplete_owned' );
    nonce::field( 'get', '_get' );
    nonce::field( 'preview', '_preview' );

    $mm_upload_url = '/website/upload-file/?_nonce=' . nonce::create( 'upload_file' );
    $mm_search_url = '/website/get-files/?_nonce=' . nonce::create( 'get_files' );
    $mm_delete_url = '/website/delete-file/?_nonce=' . nonce::create( 'delete_file' );
?>

<div class="row-fluid">
    <div class="col-lg-8">
        <section class="panel">
            <div class="panel-body">

                <div class="email-layout" id="email-editor"><?php echo $campaign->message ?></div>

                <p>
                    <a href="javascript:;" data-step="1" class="btn btn-default"">&lt; Back</a>
                    <a class="btn btn-default save-draft">Save Draft</a>

                    <a href="javascript:;" data-step="3" class="btn btn-primary pull-right">Next &gt;</a>
                </p>

            </div>
        </section>
    </div>

    <div class="col-lg-4">
        <section class="panel">
            <div class="panel-body">

                <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><a href="#email-content" role="tab" data-toggle="tab">Content</a></li>
                    <li><a href="#email-layouts" role="tab" data-toggle="tab">Layout</a></li>
                    <li><a href="#email-settings" role="tab" data-toggle="tab">Settings</a></li>
                </ul>

                <div class="tab-content">
                    <div id="email-content" class="tab-pane active">
                        <ul class="content-thumbnails list-inline">
                            <li data-content-type="product"><img src="/images/campaigns/product.png" /><br>Add Product</li>
                            <li data-content-type="text"><img src="/images/campaigns/text.png" /><br>Add Text</li>
                            <li data-content-type="image"><img src="/images/campaigns/image.png" /><br>Add Image</li>
                        </ul>
                    </div>
                    <div id="email-layouts" class="tab-pane">
                        <ul class="layout-thumbnails list-inline">
                            <li data-layout="layout-1"><img src="/images/campaigns/layout-1.jpg" /></li>
                            <li data-layout="layout-2"><img src="/images/campaigns/layout-2.jpg" /></li>
                            <li data-layout="layout-3"><img src="/images/campaigns/layout-3.jpg" /></li>
                            <li data-layout="layout-4"><img src="/images/campaigns/layout-4.jpg" /></li>
                            <li data-layout="layout-5"><img src="/images/campaigns/layout-5.jpg" /></li>
                            <li data-layout="layout-6"><img src="/images/campaigns/layout-6.jpg" /></li>
                            <li data-layout="layout-7"><img src="/images/campaigns/layout-7.jpg" /></li>
                            <li data-layout="layout-8"><img src="/images/campaigns/layout-8.jpg" /></li>
                            <li data-layout="layout-9"><img src="/images/campaigns/layout-9.jpg" /></li>
                            <li data-layout="layout-10"><img src="/images/campaigns/layout-10.jpg" /></li>
                            <li data-layout="layout-11"><img src="/images/campaigns/layout-11.jpg" /></li>
                        </ul>
                    </div>
                    <div id="email-settings" class="tab-pane">
                        <div class="checkbox">
                            <label for="no-template">
                                <input type="checkbox" name="no-template" id="no-template" value="1" <?php if ( $campaign->id && !$campaign->email_template_id ) echo 'checked="checked"' ?>>
                                Remove Header/Footer
                            </label>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>
</div>


<div class="hidden" id="email-builder-types">

    <div class="content-type-template" data-content-type="product">
        <div class="placeholder-actions">
            <a data-action="clear" href="javascript:;"><i class="fa fa-trash-o"></i></a>
            <a data-action="edit" class="hidden" href="javascript:;"><i class="fa fa-pencil"></i></a>
            <a data-action="edit-price" href="javascript:;"><i class="fa fa-dollar"></i></a>

            <input type="text" class="product-autocomplete hidden" placeholder="SKU or Name" />

            <div class="edit-price-actions hidden">
                <input type="text" class="product-sale-price" placeholder="Sale price" />
                <input type="text" class="product-price" placeholder="Price" />
                <a data-action="save-price" href="javascript:;"><i class="fa fa-save"></i></a>
            </div>
        </div>
        <div class="placeholder-content content-type-product"></div>
    </div>

    <div class="content-type-template" data-content-type="text">
        <div class="placeholder-actions">
            <a data-action="clear" href="javascript:;"><i class="fa fa-trash-o"></i></a>
            <a data-action="edit" href="#" title="Edit Content" class="open-text-editor" data-toggle="modal" data-target="#text-editor-modal"><i class="fa fa-pencil"></i></a>
        </div>
        <div class="placeholder-content content-type-text"></div>
    </div>

    <div class="content-type-template" data-content-type="image">
        <div class="placeholder-actions">
            <a data-action="clear" href="javascript:;"><i class="fa fa-trash-o"></i></a>
            <a data-action="edit"  href="javascript:;" title="Open Media Manager" rel="dialog"  data-media-manager data-upload-url="<?php echo $mm_upload_url ?>" data-search-url="<?php echo $mm_search_url ?>" data-delete-url="<?php echo $mm_delete_url ?>"><i class="fa fa-pencil"></i></a>
            <a data-action="edit-link" class="hidden" href="javascript:;" title="Open Media Manager"><i class="fa fa-link"></i></a>

            <input type="text" class="image-link-url hidden" placeholder="Enter URL" />
            <a data-action="save-link" class="hidden" href="javascript:;"><i class="fa fa-save"></i></a>
        </div>
        <div class="placeholder-content content-type-image"></div>
    </div>

    <div data-layout="layout-1">
        <table width="600">
            <tr class="email-row-4">
                <td colspan="6" width="300" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="6" width="300" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-3">
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
        </table>
    </div>

    <div data-layout="layout-2">
        <table width="600">
            <tr class="email-row-6">
                <td colspan="12" width="600" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
        </table>
    </div>

    <div data-layout="layout-3">
        <table width="600">
            <tr class="email-row-1">
                <td colspan="12" width="600" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-3">
                <td colspan="4" width="200" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="4" width="200" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="4" width="200" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-4">
                <td colspan="4" width="200" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="4" width="200" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="4" width="200" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
        </table>
    </div>

    <div data-layout="layout-4">
        <table width="600">
            <tr class="email-row-4">
                <td colspan="6" width="300" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="6" width="300" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-4">
                <td colspan="6" width="300" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="6" width="300" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
        </table>
    </div>

    <div data-layout="layout-5">
        <table width="600">
            <tr class="email-row-6">
                <td colspan="4">
                    <table>
                        <tr class="email-row-3">
                            <td colspan="12" width="200" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                        </tr>
                        <tr class="email-row-6">
                            <td colspan="12" width="200" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                        </tr>
                    </table>
                </td>
                <td colspan="8">
                    <table>
                        <tr class="email-row-6">
                            <td colspan="12" width="400" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                        </tr>
                        <tr class="email-row-6">
                            <td colspan="12" width="400" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div data-layout="layout-6">
        <table width="600">
            <tr class="email-row-6">
                <td colspan="8">
                    <table>
                        <tr class="email-row-6">
                            <td colspan="12" width="400" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                        </tr>
                        <tr class="email-row-6">
                            <td colspan="12" width="400" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                        </tr>
                    </table>
                </td>
                <td colspan="4">
                    <table>
                        <tr class="email-row-3">
                            <td colspan="12" width="200" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                        </tr>
                        <tr class="email-row-6">
                            <td colspan="12" width="200" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div data-layout="layout-7">
        <table width="600">
            <tr class="email-row-1">
                <td colspan="12" width="600" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-3">
                <td colspan="6" width="300" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="6" width="300" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-4">
                <td colspan="6" width="300" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="6" width="300" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
        </table>
    </div>

    <div data-layout="layout-8">
        <table width="600">
            <tr class="email-row-1">
                <td colspan="12" width="600" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-3">
                <td colspan="12" width="600" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-4">
                <td colspan="12" width="600" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
        </table>
    </div>

    <div data-layout="layout-9">
        <table width="600">
            <tr class="email-row-4">
                <td colspan="12" width="600" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-3">
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
        </table>
    </div>

    <div data-layout="layout-10">
        <table width="600">
            <tr class="email-row-4">
                <td colspan="12" width="600" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-3">
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-3">
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
        </table>
    </div>

    <div data-layout="layout-11">
        <table width="600">
            <tr class="email-row-4">
                <td colspan="12" width="600" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-3">
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-3">
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
            <tr class="email-row-3">
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
                <td colspan="3" width="150" class="droppable"><p class="placeholder">Drag Content Here</p></td>
            </tr>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="text-editor-modal" tabindex="-1" role="dialog" aria-labelledby="text-editor-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="text-editor-modal-label">Edit Text</h4>
            </div>
            <div class="modal-body">
                <div id="editor-container"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="save-text">Save</button>
            </div>
        </div>
    </div>
</div>
