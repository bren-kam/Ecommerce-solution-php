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
                Traffic Sources
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
                        <a href="javascript:;" data-report="pages_by_visits" data-title="Pages/Visit"><img src="<?php echo $sparklines['pages_by_visits']; ?>" /></a>
                        <span class="analytics-count"><?php echo number_format( $total['pages_by_visits'] ); ?></span> Pages/Visit
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <a href="javascript:;" data-report="time_on_site" data-title="Time on Site"><img src="<?php echo $sparklines['time_on_site']; ?>" /></a>
                        <span class="analytics-count"><?php echo $total['time_on_site']; ?></span> Time on Site
                    </div>
                    <div class="col-lg-6">
                        <a href="javascript:;" data-report="new_visits" data-title="Direct Traffic"><img src="<?php echo $sparklines['new_visits']; ?>" /></a>
                        <span class="analytics-count"><?php echo number_format( $total['new_visits'], 2 ); ?>%</span> New Visits
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <a href="javascript:;" data-report="bounce_rate" data-title="Search Engines"><img src="<?php echo $sparklines['bounce_rate']; ?>" /></a>
                        <span class="analytics-count"><?php echo number_format( $total['bounce_rate'], 2); ?>%</span> Bounce Rate
                    </div>
                </div>

            </div>
        </section>
    </div>

</div>


<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Traffic Sources
            </header>

            <div class="panel-body">

                <div class="adv-table">
                    <table class="dt display table table-bordered table-striped" perPage="30,50,100">
                        <thead>
                        <tr>
                            <th>Source/Medium</th>
                            <th class="text-right" sort="1 desc">Visits</th>
                            <th class="text-right">Pages/Visit</th>
                            <th class="text-right">Avg. Page Time</th>
                            <th class="text-right">% New Visits</th>
                            <th class="text-right">Bounce Rate</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ( $traffic_sources as $ts ): ?>
                            <tr>
                                <td><a href="/analytics/source/?s=<?php echo urlencode( $ts['source'] ); ?>" title="<?php echo $ts['source'], ' / ', $ts['medium']; ?>"><?php echo $ts['source'], ' / ', $ts['medium']; ?></a></td>
                                <td class="text-right"><?php echo number_format( $ts['visits'] ); ?></td>
                                <td class="text-right"><?php echo number_format( $ts['pages_by_visits'], 2 ); ?></td>
                                <td class="text-right"><?php echo $ts['time_on_site']; ?></td>
                                <td class="text-right"><?php echo $ts['new_visits']; ?>%</td>
                                <td class="text-right"><?php echo $ts['bounce_rate']; ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>

            </div>
        </section>
    </div>
</div>


<script>
    var AnalyticsSettings = <?php echo json_encode( array( 'plotting_data' => $visits_plotting_array, 'plotting_label' => 'Visits' ) ); ?>;
</script>