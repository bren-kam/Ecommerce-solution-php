<?php
/**
 * @package Grey Suit Retail
 * @page Traffic Sources Overview | Traffic Sources | Analytics
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var array $sparklines
 * @var string $date_start
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
<h1><?php echo _('Traffic Sources Overview'); ?></h1>
<br clear="all" /><br />

<div id="dLargeGraphWrapper"><div id="dLargeGraph"></div></div>
<br />
<div class="info-box col-1">
    <p class="info-box-title"><?php echo _('Site Usage'); ?></p>
    <div class="info-box-content">
        <table id="sparklines">
            <tr>
                <td width="50%">
                    <div id="dSmallContainer">
                        <p><a href="#direct" class="sparkline" title="<?php echo _('Direct Traffic Sparkline'); ?>"><img src="<?php echo $sparklines['direct']; ?>" class="sparkline" width="150" height="36" alt="<?php echo _('Direct Traffic Sparkline'); ?>" /></a> <span class="data"><?php echo ( 0 == $traffic_sources['total'] ) ? '0' : number_format( $traffic_sources['direct'] / $traffic_sources['total'] * 100, 2 ); ?>%</span> <span class="label"><?php echo _('Direct Traffic'); ?></span></p>
                        <p><a href="#referring" class="sparkline" title="<?php echo _('Referring Sites Sparkline'); ?>"><img src="<?php echo $sparklines['referring']; ?>" class="sparkline" width="150" height="36" alt="<?php echo _('Referring Sites Sparkline'); ?>" /></a> <span class="data"><?php echo ( 0 == $traffic_sources['total'] ) ? '0' : number_format( $traffic_sources['referring'] / $traffic_sources['total'] * 100, 2 ); ?>%</span> <span class="label"><?php echo _('Referring Sites'); ?></span></p>
                        <p><a href="#search_engines" class="sparkline" title="<?php echo _('Search Engines Sparkline'); ?>"><img src="<?php echo $sparklines['search_engines']; ?>" class="sparkline" width="150" height="36" alt="<?php echo _('Search Engines Sparkline'); ?>" /></a> <span class="data"><?php echo ( 0 == $traffic_sources['total'] ) ? '0' : number_format( $traffic_sources['search_engines'] / $traffic_sources['total'] * 100, 2 ); ?>%</span> <span class="label"><?php echo _('Search Engines'); ?></span></p>
                    </div>
                </td>
                <td style="padding-top: 20px">
                    <div class="pie-chart-container"><div id="dTrafficSources"></div></div>
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
                    <br clear="all" />
                </td>
            </tr>
        </table>
    </div>
</div>
<br clear="both" /><br />
<div class="col-2 float-left">
    <div class="info-box">
        <p class="info-box-title"><?php echo _('Top Traffic Sources'); ?></p>
        <div class="info-box-content">
            <table>
                <tr>
                    <th width="60%" class="text-left"><strong><?php echo _('Sources'); ?></strong></th>
                    <th width="20%" class="text-right"><strong><?php echo _('Visits'); ?></strong></th>
                    <th width="20%" class="text-right"><strong><?php echo _('% New Visits'); ?></strong></th>
                </tr>
                <?php
                if ( is_array( $top_traffic_sources ) )
                foreach ( $top_traffic_sources as $tts ) {
                ?>
                <tr>
                    <td><a href="/analytics/source/?s=<?php echo urlencode( $tts['source'] ); ?>" title="<?php echo $tts['source'], ' / ', $tts['medium']; ?>"><?php echo $tts['source']; ?></a></td>
                    <td class="text-right"><?php echo number_format( $tts['visits'] ); ?></td>
                    <td class="text-right"><?php echo $tts['new_visits']; ?>%</td>
                </tr>
                <?php } ?>
            </table>
            <br />
            <p align="right";><a href="/analytics/traffic-sources/" title="<?php echo _('View Report'); ?>" class="big bold"><?php echo _('View'); ?> <span class="gray"><?php echo _('Report'); ?></span></a></p>
        </div>
    </div>
</div>
<div class="col-2 float-left">
    <div class="info-box">
        <p class="info-box-title"><?php echo _('Top Keywords'); ?></p>
        <div class="info-box-content">
            <table>
                <tr>
                    <th width="60%" class="text-left"><strong><?php echo _('Keywords'); ?></strong></th>
                    <th width="20%" class="text-right" column="formatted-num"><strong><?php echo _('Visits'); ?></strong></th>
                    <th width="20%" class="text-right" column="formatted-num"><strong><?php echo _('% New Visits'); ?></strong></th>
                </tr>
                <?php
                if ( is_array( $top_keywords ) )
                foreach ( $top_keywords as $tk ) {
                ?>
                <tr>
                    <td><a href="/analytics/keyword/?k=<?php echo urlencode( $tk['keyword'] ); ?>" title="<?php echo $tk['keyword']; ?>"><?php echo $tk['keyword']; ?></a></td>
                    <td class="text-right"><?php echo number_format( $tk['visits'] ); ?></td>
                    <td class="text-right"><?php echo $tk['new_visits']; ?>%</td>
                </tr>
                <?php } ?>
            </table>
            <br />
            <p align="right"><a href="/analytics/keywords/" title="<?php echo _('View Report'); ?>" class="big bold"><?php echo _('View'); ?> <span class="highlight"><?php echo _('Report'); ?></span></a></p>
        </div>
    </div>
</div>
<br clear="left" />

<script type="text/javascript">
    function open_flash_chart_data() {
        return JSON.stringify(<?php echo $pie_chart; ?>);
    }
    plotting_data = [<?php echo $visits_plotting; ?>];
    plotting_label = 'Visits';
    show_piechart = true;
</script>
<?php
nonce::field( 'get_graph', '_get_graph');

echo $template->end();
?>