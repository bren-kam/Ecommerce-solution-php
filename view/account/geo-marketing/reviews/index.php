<?php
    nonce::field('store_session', '_store_session');
?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Customer Reviews
            </header>

            <div class="panel-body">

                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="location-id">Location:</label>
                            <select class="form-control" id="location-id">
                                <option value="">All Locations</option>
                                <?php foreach ( $locations as $location ): ?>
                                    <option value="<?php echo $location->id ?>" <?php if ( isset($_SESSION['reviews']['location-id']) && $_SESSION['reviews']['location-id'] == $location->id ) echo 'selected' ?>><?php echo $location->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="location-id">Sites:</label>
                            <select class="form-control" id="site-id">
                                <option value="">All Sites</option>
                                <?php foreach ( $sites as $site ): ?>
                                    <option value="<?php echo $site ?>" <?php if ( isset($_SESSION['reviews']['site-id']) && $_SESSION['reviews']['site-id'] == $site ) echo 'selected' ?>><?php echo $site ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/geo-marketing/reviews/list-all/" perPage="30,50,100">
                        <thead>
                            <th sort="1">Location</th>
                            <th sort="2">Site</th>
                            <th>Author</th>
                            <th>Review</th>
                            <th>Rating</th>
                            <th>Created</th>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>