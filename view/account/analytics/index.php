<?php
/**
 * @package Grey Suit Retail
 * @page Dashboard | Analytics
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
                Dashboard
            </header>

            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12 text-right">
                        <form class="form-inline">
                            <div class="form-group">
                                <input type="text" class="form-control" id="date-start" value="<?php echo $date_start ?>" />
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" id="date-end" value="<?php echo $date_end ?>" />
                            </div>
                        </form>
                    </div>
                </div>

                <div id="large-graph"></div>
            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Site Usage
            </header>

            <div class="panel-body small-graphs">

                <div class="row">
                    <div class="col-lg-6">
                        <a href="javascript:;" data-report="visits" data-title="Visits"><img src="<?php echo $sparklines['visits']; ?>" /></a>
                        <span class="analytics-count"><?php echo number_format( $total['visits'] ); ?></span> Visits
                    </div>
                    <div class="col-lg-6">
                        <a href="javascript:;" data-report="bounce_rate" data-title="Bounce Rate"><img src="<?php echo $sparklines['bounce_rate']; ?>" /></a>
                        <span class="analytics-count"><?php echo number_format( $total['bounce_rate'], 2 ); ?>%</span> Bounce Rate
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <a href="javascript:;" data-report="page_views" data-title="Page Views"><img src="<?php echo $sparklines['page_views']; ?>" /></a>
                        <span class="analytics-count"><?php echo number_format( $total['page_views'] ); ?></span> Page Views
                    </div>
                    <div class="col-lg-6">
                        <a href="javascript:;" data-report="time_on_site" data-title="Time On Site"><img src="<?php echo $sparklines['time_on_site']; ?>" /></a>
                        <span class="analytics-count"><?php echo $total['time_on_site']; ?></span> Avg. Time on Site
                    </div>
                </div>

            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Traffic Sources Overview
            </header>

            <div class="panel-body">

                <div class="clearfix">
                    <div class="pie-chart-container" style="width:190px; right:0; left: 30px;"><div id="traffic-sources"></div></div>

                    <div id="traffic-source-data">
                        <p class="blue-marker">
                            Direct Traffic<br />
                            <span class="data"><?php echo number_format( $traffic_sources['direct'] ); ?> (<?php echo ( 0 == $traffic_sources['total'] ) ? '0' : round( $traffic_sources['direct'] / $traffic_sources['total'] * 100, 2 ); ?>%)</span>
                        </p>
                        <p class="green-marker">
                            Referring Sites<br />
                            <span class="data"><?php echo number_format( $traffic_sources['referring'] ); ?> (<?php echo ( 0 == $traffic_sources['total'] ) ? '0' : round( $traffic_sources['referring'] / $traffic_sources['total'] * 100, 2 ); ?>%)</span>
                        </p>
                        <p class="orange-marker">
                            Search Engines<br />
                            <span class="data"><?php echo number_format( $traffic_sources['search_engines'] ); ?> (<?php echo ( 0 == $traffic_sources['total'] ) ? '0' : round( $traffic_sources['search_engines'] / $traffic_sources['total'] * 100, 2 ); ?>%)</span>
                        </p>
                        <?php if ( $traffic_sources['email'] > 0 ) { ?>
                            <p class="yellow-marker">
                                Campaigns<br />
                                <span class="data"><?php echo number_format( $traffic_sources['email'] ); ?> (<?php echo round( $traffic_sources['email'] / $traffic_sources['total'] * 100, 2 ); ?>%)</span>
                            </p>
                        <?php } ?>
                    </div>
                </div>

                <p class="text-right">
                    <a href="/analytics/traffic-sources-overview/" class="btn btn-primary">View Report</a>
                </p>
            </div>
        </section>
    </div>
    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Content Overview
            </header>

            <div class="panel-body">

                <table class="table">
                    <thead>
                        <tr>
                            <th>Pages</th>
                            <th class="text-right">Page Views</th>
                            <th class="text-right">% Page Views</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $content_overview_pages as $top ) : ?>
                            <tr>
                                <td><a href="/analytics/page/?p=<?php echo urlencode( $top['page'] ); ?>" title="<?php echo $top['page']; ?>"><?php echo ( '/' == $top['page'] ) ? 'Home' : $top['page']; ?></a></td>
                                <td class="text-right"><?php echo number_format( $top['page_views'] ); ?></td>
                                <td class="text-right"><?php echo round( $top['page_views'] / $total['page_views'] * 100, 2 ); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p class="text-right">
                    <a href="/analytics/content-overview/" class="btn btn-primary">View Report</a>
                </p>
            </div>
        </section>
    </div>
</div>

<script>
    var AnalyticsSettings = <?php echo json_encode( array( 'pie_chart' => $pie_chart, 'plotting_data' => $visits_plotting_array, 'plotting_label' => 'Visits', 'show_pie_chart' => true ) ); ?>;
</script>