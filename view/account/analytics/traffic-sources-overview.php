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
                Traffic Sources Overview
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
    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Site Usage
            </header>

            <div class="panel-body small-graphs">

                <div class="row">
                    <div class="col-lg-12">
                        <a href="javascript:;" data-report="direct" data-title="Direct Traffic"><img src="<?php echo $sparklines['direct']; ?>" /></a>
                        <span class="analytics-count"><?php echo number_format( $total['direct'], 2 ); ?>%</span> Direct Traffic
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <a href="javascript:;" data-report="referring" data-title="Referring Sites"><img src="<?php echo $sparklines['referring']; ?>" /></a>
                        <span class="analytics-count"><?php echo number_format( $total['referring'], 2 ); ?>%</span> Referring Sites
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <a href="javascript:;" data-report="search_engines" data-title="Search Engines"><img src="<?php echo $sparklines['search_engines']; ?>" /></a>
                        <span class="analytics-count"><?php echo number_format( $total['search_engines'], 2); ?>%</span> Search Engines
                    </div>
                </div>

            </div>
        </section>
    </div>
    <div class="col-lg-6">
        <section class="panel">
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
</div>


<div class="row-fluid">

    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Top Traffic Sources
            </header>

            <div class="panel-body">

                <table class="table">
                    <thead>
                        <tr>
                            <th>Source</th>
                            <th class="text-right">Visits</th>
                            <th class="text-right">% New Visits</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $top_traffic_sources as $tts ) : ?>
                            <tr>
                                <td><a href="/analytics/source/?s=<?php echo urlencode( $tts['source'] ); ?>" title="<?php echo $tts['source'], ' / ', $tts['medium']; ?>"><?php echo $tts['source']; ?></a></td>
                                <td class="text-right"><?php echo number_format( $tts['visits'] ); ?></td>
                                <td class="text-right"><?php echo $tts['new_visits']; ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p class="text-right">
                    <a href="/analytics/traffic-sources/" class="btn btn-primary">View Report</a>
                </p>
            </div>
        </section>
    </div>

    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Top Keywords
            </header>

            <div class="panel-body">

                <table class="table">
                    <thead>
                    <tr>
                        <th>Keyword</th>
                        <th class="text-right">Visits</th>
                        <th class="text-right">% New Visits</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ( $top_keywords as $tk ) : ?>
                        <tr>
                            <td><a href="/analytics/keyword/?k=<?php echo urlencode( $tk['keyword'] ); ?>" title="<?php echo $tk['keyword']; ?>"><?php echo $tk['keyword']; ?></a></td>
                            <td class="text-right"><?php echo number_format( $tk['visits'] ); ?></td>
                            <td class="text-right"><?php echo $tk['new_visits']; ?>%</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <p class="text-right">
                    <a href="/analytics/keywords/" class="btn btn-primary">View Report</a>
                </p>
            </div>
        </section>
    </div>

</div>


<script>
    var AnalyticsSettings = <?php echo json_encode( array( 'pie_chart' => $pie_chart, 'plotting_data' => $visits_plotting_array, 'plotting_label' => 'Visits', 'show_pie_chart' => true ) ); ?>;
</script>