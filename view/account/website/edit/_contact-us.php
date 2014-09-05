<?php
nonce::field( 'upload_file', '_upload_file' );
nonce::field( 'get_location', '_get_location' );
nonce::field( 'delete_location', '_delete_location' );
nonce::field( 'update_location_sequence', '_update_location_sequence' );
?>

<section class="panel" id="contact-us-settings">
    <header class="panel-heading">
        Contact Us Settings
    </header>

    <div class="panel-body">
        <?php var_dump($multiple_location_map, $hide_all_maps) ?>
        <p>Note: These locations are updated in real-time. Leaving the page will not undo a created or deleted location.</p>

        <div id="location-list" class="clearfix">
            <?php foreach ( $locations as $location ): ?>
                <div class="location" data-location-id="<?php echo $location->id ?>">
                    <h3><?php echo $location->name ?></h3>
                    <p class="clearfix">
                        <span class="pull-left">
                            <?php echo $location->address ?> <br />
                            <?php echo "{$location->city}, {$location->state} {$location->zip}" ?>
                        </span>

                        <span class="pull-right">
                            <?php echo $location->phone ?> <br />
                            <?php echo $location->fax ?>
                        </span>
                    </p>
                    <p>
                        <?php echo $location->email ?> <br />
                        <?php echo $location->website ?>
                    </p>
                    <p class="store-hours">
                        <strong>Store Hours:</strong> <br />
                        <?php echo $location->store_hours ?>
                    </p>

                    <a href="javascript:;" class="remove"><i class="fa fa-trash-o"></i></a>
                    <a href="javascript:;" class="edit"><i class="fa fa-pencil"></i></a>
                </div>
            <?php endforeach; ?>
        </div>

        <p>
            <button type="button" class="btn btn-default" id="add-location">Add Location</button>
        </p>

        <h4>Contact Us Form Settings</h4>

        <div class="form-group">
            <label for="tEmail">Email:</label>
            <input type="text" class="form-control" name="tEmail" id="tEmail" value="<?php echo $email ?>" />
        </div>

        <h4>Map Settings</h4>

        <div class="radio">
            <label>
                <input type="radio" name="rPosition" value="1" <?php if ( $page->top == '1' ) echo 'checked="checked"'; ?> />
                Map will be placed after content
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="rPosition" value="0" <?php if ( $page->top == '0' ) echo 'checked="checked"'; ?> />
                Map will be placed before content
            </label>
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox" id="cbHideAllMaps" value="yes" <?php if ( 'true' == $hide_all_maps ) echo 'checked'; ?> />
                Hide All Maps
            </label>
        </div>

        <div class="checkbox">
            <label>
                <input type="checkbox" id="cbMultipleLocationMap" value="yes" <?php if ( 'true' == $multiple_location_map ) echo 'checked'; ?> />
                Multiple Location Map
            </label>
        </div>

    </div>

</section>

<div id="location-template" class="location hidden">
    <h3></h3>
    <p class="clearfix">
        <span class="pull-left"></span>
        <span class="pull-right"></span>
    </p>
    <p></p>
    <p class="store-hours"></p>

    <a href="javascript:;" class="remove"><i class="fa fa-trash-o"></i></a>
    <a href="javascript:;" class="edit"><i class="fa fa-pencil"></i></a>
</div>

<!-- Location Form Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="modalLabel">Location</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Name" />
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone:</label>
                            <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone" />
                        </div>
                        <div class="form-group">
                            <label for="fax">Fax:</label>
                            <input type="text" class="form-control" name="fax" id="fax" placeholder="Fax" />
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="text" class="form-control" name="email" id="email" placeholder="Email" />
                        </div>
                        <div class="form-group">
                            <label for="store-hours">Store Hours:</label>
                            <textarea class="form-control" name="store-hours" id="store-hours" placeholder="Store Hours" rows="3"></textarea>
                        </div>

                        <p>
                            <button type="button" class="btn btn-default btn-sm" id="upload">Upload Store Image</button>
                            <div class="progress progress-sm hidden" id="upload-loader">
                                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                            <!-- Where the uploader lives -->
                            <div id="uploader"></div>
                        </p>

                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input type="text" class="form-control" name="address" id="address" placeholder="Address" />
                        </div>
                        <div class="form-group">
                            <label for="city">City:</label>
                            <input type="text" class="form-control" name="city" id="city" placeholder="City" />
                        </div>
                        <div class="form-group">
                            <label for="state">State:</label>
                            <select class="form-control" name="state" id="state">
                                <option value="">-- Select State --</option>
                                <?php data::states(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="zip">Zip:</label>
                            <input type="text" class="form-control" name="zip" id="zip" placeholder="Zip" />
                        </div>
                        <p id="store-image" class="pull-right hidden">
                            <input type="hidden" name="store-image" id="store-image" />
                            <img />
                            <a href="javascript:;" id="remove-store-image"><i class="fa fa-trash-o"></i></a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
                <?php nonce::field( 'add_edit_location' ) ?>
            </div>
        </div>
    </div>
</div>