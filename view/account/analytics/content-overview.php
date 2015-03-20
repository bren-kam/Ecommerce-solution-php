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
                Content Overview
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
                        <a href="javascript:;" data-report="bounce_rate" data-title="Page Views"><img src="<?php echo $sparklines['page_views']; ?>" /></a>
                        <span class="analytics-count"><?php echo number_format( $total['page_views'] ); ?></span> Page Views
                    </div>
                    <div class="col-lg-6">
                        <a href="javascript:;" data-report="page_views" data-title="Time on Page"><img src="<?php echo $sparklines['time_on_page']; ?>" /></a>
                        <span class="analytics-count"><?php echo number_format( $total['time_on_page'] ); ?></span> Time on Page
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <a href="javascript:;" data-report="time_on_page" data-title="Bounce Rate"><img src="<?php echo $sparklines['bounce_rate']; ?>" /></a>
                        <span class="analytics-count"><?php echo number_format( $total['bounce_rate'], 2 ); ?>%</span> Bounce Rate
                    </div>
                    <div class="col-lg-6">
                        <a href="javascript:;" data-report="exit_rate" data-title="Exit Rate"><img src="<?php echo $sparklines['exit_rate']; ?>" /></a>
                        <span class="analytics-count"><?php echo $total['exit_rate']; ?></span> Exit Rate
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
                Pages
            </header>

            <div class="panel-body">

                <div class="adv-table">
                    <table class="dt display table table-bordered table-striped" perPage="30,50,100">
                        <thead>
                            <tr>
                                <th>Page</th>
                                <th class="text-right" sort="1 desc">Page Views</th>
                                <th class="text-right">Avg. Page Time</th>
                                <th class="text-right">Bounce Rate></th>
                                <th class="text-right">% Exit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ( $content_overview_pages as $top ):
                                    $top['page'] = str_replace( '&', '&amp;', $top['page'] );
                            ?>
                                <tr>
                                    <td><a href="/analytics/page/?p=<?php echo urlencode( $top['page'] ); ?>" title="<?php echo $top['page']; ?>"><?php echo ( '/' == $top['page'] ) ? 'Home' : $top['page']; ?></a></td>
                                    <td class="text-right"><?php echo number_format( $top['page_views'] ); ?></td>
                                    <td class="text-right"><?php echo $top['time_on_page']; ?></td>
                                    <td class="text-right"><?php echo number_format( $top['bounce_rate'], 2 ); ?>%</td>
                                    <td class="text-right"><?php echo number_format( $top['exit_rate'], 2 ); ?>%</td>
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
    var AnalyticsSettings = <?php echo json_encode( array( 'plotting_label' => 'Page Views', 'show_pie_chart' => false, 'plotting_data' => $page_views_plotting_array ) ); ?>;
</script>