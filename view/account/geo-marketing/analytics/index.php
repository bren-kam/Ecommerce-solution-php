<?php
/**
 * @package Grey Suit Retail
 * @page Dashboard | Analytics | GeoMarketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var array $sparklines
 * @var string $date_start
 * @var string $date_end
 */

nonce::field( 'get_graph', '_get_graph' );
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                GeoMarketing - Reports
            </header>

            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-3">
                        <select class="form-control" id="location-id">
                            <option value="">-- All Locations --</option>
                            <?php foreach( $locations as $location ): ?>
                                <option value="<?php echo $location->id ?>" <?php if ( $location->id == $location_id ) echo 'selected' ?>><?php echo $location->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-9 text-right">
                        <form class="form-inline">
                            <div class="form-group">
                                <input type="text" class="form-control" id="date-start" value="<?php echo $start_date->format('n/j/Y') ?>" />
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" id="date-end" value="<?php echo $end_date->format('n/j/Y') ?>" />
                            </div>
                        </form>
                    </div>
                </div>

                <?php if( !$has_analytics_data ): ?>
                    <br>
                    <div class="alert alert-warning">
                        Analytics will not show until 3 weeks after your first location is created.
                    </div>
                <?php endif; ?>

            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Searches
            </header>

            <div class="panel-body">
                <div id="searches" class="report"></div>
            </div>
        </section>
    </div>

    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Profile Views
            </header>

            <div class="panel-body">
                <div id="profile-views" class="report"></div>
            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Special Offer Clicks
            </header>

            <div class="panel-body">
                <div id="special-offer-clicks" class="report"></div>
            </div>
        </section>
    </div>

    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Foursquare Checkins
            </header>

            <div class="panel-body">
                <div id="foursquare-checkins" class="report"></div>
            </div>
        </section>
    </div>
</div>

<script>
    var AnalyticsSettings = <?php echo json_encode( [ 'reports' => $reports ] ); ?>;
</script>