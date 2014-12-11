<?php
    nonce::field('store_session', '_store_session');
?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Listings
            </header>

            <div class="panel-body">

                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="location-id">Location:</label>
                            <select class="form-control" id="location-id">
                                <option value="">All Locations</option>
                                <?php foreach ( $locations as $location ): ?>
                                    <option value="<?php echo $location->id ?>"><?php echo $location->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/geo-marketing/listings/list-all/" perPage="30,50,100">
                        <thead>
                            <th sort="1">Location</th>
                            <th sort="2">Site</th>
                            <th>Status</th>
                            <th>URL</th>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>