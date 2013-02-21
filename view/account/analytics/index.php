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
 * @var striung $date_start
 * @var string $date_end
 */

require VIEW_PATH . $this->variables['view_base'] . 'sidebar.php';
?>
<div id="content">
<div id="subcontent-wrapper">
<div id="subcontent">
<div class="dates">
    <input type="text" id="tDateStart" name="ds" class="tb" value="<?php echo $date_start; ?>" />
    -
    <input type="text" id="tDateEnd" name="de" class="tb" value="<?php echo $date_end; ?>" />
</div>
<h1><?php echo _('Dashboard'); ?></h1>
<br clear="all" /><br />

<div id="dLargeGraphWrapper"><div id="dLargeGraph"></div></div>
<br />
<div class="info-box col-1">
    <p class="info-box-title"><?php echo _('Site Usage'); ?></p>
    <div class="info-box-content">
        <table id="sparklines">
            <tr>
                <td width="15%"><a href="#visits" class="sparkline" title="<?php echo _('Visits Sparkline'); ?>"><img src="<?php echo $sparklines['visits']; ?>" width="150" height="36" alt="<?php echo _('Visits Sparkline'); ?>" /></a></td>
                <td width="35%"><span class="data"><?php echo number_format( $total['visits'] ); ?></span> <span class="label"><?php echo _('Visits'); ?></span></td>
                <td width="15%"><a href="#bounce_rate" class="sparkline" title="<?php echo _('Bounce Rate Sparkline'); ?>"><img src="<?php echo $sparklines['bounce_rate']; ?>" width="150" height="36" alt="<?php echo _('Bounce Rate Sparkline'); ?>" /></a></td>
                <td width="35%"><span class="data"><?php echo number_format( $total['bounce_rate'], 2 ); ?>%</span> <span class="label"><?php echo _('Bounce Rate'); ?></span></td>
            </tr>
            <tr>
                <td><a href="#page_views" class="sparkline" title="<?php echo _('Page Views Sparkline'); ?>"><img src="<?php echo $sparklines['page_views']; ?>" width="150" height="36" alt="<?php echo _('Page Views Sparkline'); ?>" /></a></td>
                <td><span class="data"><?php echo number_format( $total['page_views'] ); ?></span> <span class="label"><?php echo _('Page Views'); ?></span></td>
                <td><a href="#time_on_site" class="sparkline" title="<?php echo _('Avg. Time On Site Sparkline'); ?>"><img src="<?php echo $sparklines['time_on_site']; ?>" width="150" height="36" alt="<?php echo _('Avg. Time On Site Sparkline'); ?>" /></a></td>
                <td><span class="data"><?php echo $total['time_on_site']; ?></span> <span class="label"><?php echo _('Avg. Time on Site'); ?></span></td>
            </tr>
        </table>
    </div>
</div>
<br clear="both" /><br />
<div class="col-2 float-left">
    <div class="info-box">
        <p class="info-box-title"><?php echo _('Traffic Sources Overview'); ?></p>
        <div class="info-box-content">
            <div class="pie-chart-container" style="width:190px; right:0; left: 30px;"><div id="dTrafficSources"></div></div>
            <div id="dTrafficSourcesData">
                <p class="blue-marker">
                    <span class="label"><?php echo _('Direct Traffic'); ?></span><br />
                    <span class="data"><?php echo number_format( $traffic_sources['direct'] ); ?> (<?php echo ( 0 == $traffic_sources['total'] ) ? '0' : round( $traffic_sources['direct'] / $traffic_sources['total'] * 100, 2 ); ?>%)</span>
                </p>
                <p class="green-marker">
                    <span class="label"><?php echo _('Referring Sites'); ?></span><br />
                    <span class="data"><?php echo number_format( $traffic_sources['referring'] ); ?> (<?php echo ( 0 == $traffic_sources['total'] ) ? '0' : round( $traffic_sources['referring'] / $traffic_sources['total'] * 100, 2 ); ?>%)</span>
                </p>
                <p class="orange-marker">
                    <span class="label"><?php echo _('Search Engines'); ?></span><br />
                    <span class="data"><?php echo number_format( $traffic_sources['search_engines'] ); ?> (<?php echo ( 0 == $traffic_sources['total'] ) ? '0' : round( $traffic_sources['search_engines'] / $traffic_sources['total'] * 100, 2 ); ?>%)</span>
                </p>
                <?php if ( $traffic_sources['email'] > 0 ) { ?>
                <p class="yellow-marker">
                    <span class="label"><?php echo _('Campaigns'); ?></span><br />
                    <span class="data"><?php echo number_format( $traffic_sources['email'] ); ?> (<?php echo round( $traffic_sources['email'] / $traffic_sources['total'] * 100, 2 ); ?>%)</span>
                </p>
                <?php } ?>
            </div>
            <br clear="left" />
            <p align="right";><a href="/analytics/traffic-sources/overview/" title="<?php echo _('View Report'); ?>" class="big bold"><?php echo _('View'); ?> <span class="highlight"><?php echo _('Report'); ?></span></a></p>
        </div>
    </div>
</div>
<div class="col-2 float-left">
    <div class="info-box">
        <p class="info-box-title"><?php echo _('Content Overview'); ?></p>
        <div class="info-box-content">
            <table>
                <tr>
                    <th width="40%" class="text-left"><strong><?php echo _('Pages'); ?></strong></th>
                    <th align="right"><strong><?php echo _('Page Views'); ?></strong></th>
                    <th align="right"><strong><?php echo _('% Page Views'); ?></strong></th>
                </tr>
                <?php
                if ( is_array( $content_overview_pages ) )
                foreach ( $content_overview_pages as $top ) {
                ?>
                <tr>
                    <td><a href="/analytics/page/?p=<?php echo urlencode( $top['page'] ); ?>" title="<?php echo $top['page']; ?>"><?php echo ( '/' == $top['page'] ) ? 'Home' : $top['page']; ?></a></td>
                    <td align="right"><?php echo number_format( $top['page_views'] ); ?></td>
                    <td align="right"><?php echo round( $top['page_views'] / $total['page_views'] * 100, 2 ); ?>%</td>
                </tr>
                <?php } ?>
            </table>
            <br />
            <p align="right"><a href="/analytics/content-overview/" title="<?php echo _('View'); ?>" class="big bold"><?php echo _('View'); ?> <span class="highlight"><?php echo _('Report'); ?></span></a></p>
        </div>
    </div>
</div>
<br clear="left" />

<script type="text/javascript">
    function open_flash_chart_data() {
        return JSON.stringify(<?php echo $pie_chart; ?>);
    }
    visits_plotting = [<?php echo $visits_plotting; ?>];
</script>
<?php
nonce::field( 'get_graph', '_get_graph');

echo $template->end();
?>