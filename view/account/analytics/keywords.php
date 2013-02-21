<?php
/**
 * @package Grey Suit Retail
 * @page Keywords | Traffic Sources | Analytics
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
<h1><?php echo _('Traffic Keywords'); ?></h1>
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
                <td width="15%"><a href="#pages_by_visits" class="sparkline" title="<?php echo _('Pages/Visits Sparkline'); ?>"><img src="<?php echo $sparklines['pages_by_visits']; ?>" width="150" height="36" alt="<?php echo _('Pages/Visits Sparkline'); ?>" /></a></td>
                <td><span class="data"><?php echo $total['pages_by_visits']; ?></span> <span class="label"><?php echo _('Pages/Visits'); ?></span></td>
            </tr>
            <tr>
                <td><a href="#time_on_site" class="sparkline" title="<?php echo _('Avg. Time On Site Sparkline'); ?>"><img src="<?php echo $sparklines['time_on_site']; ?>" width="150" height="36" alt="<?php echo _('Avg. Time On a Page Sparkline'); ?>" /></a></td>
                <td><span class="data"><?php echo $total['time_on_site']; ?></span> <span class="label"><?php echo _('Avg. Time on Site'); ?></span></td>
                <td><a href="#new_visits" class="sparkline" title="<?php echo _('New Visits Sparkline'); ?>"><img src="<?php echo $sparklines['new_visits']; ?>" width="150" height="36" alt="<?php echo _('New Visits Sparkline'); ?>" /></a></td>
                <td><span class="data"><?php echo number_format( $total['new_visits'], 2 ); ?>%</span> <span class="label"><?php echo _('New Visits'); ?></span></td>
            </tr>
            <tr>
                <td><a href="#bounce_rate" class="sparkline" title="<?php echo _('Bounce Rate Sparkline'); ?>"><img src="<?php echo $sparklines['bounce_rate']; ?>" width="150" height="36" alt="<?php echo _('Bounce Rate Sparkline'); ?>" /></a></td>
                <td><span class="data"><?php echo number_format( $total['bounce_rate'], 2 ); ?>%</span> <span class="label"><?php echo _('Bounce Rate'); ?></span></td>
            </tr>
        </table>
    </div>
</div>
<br clear="both" /><br />
<div class="info-box col-1">
    <p class="info-box-title"><?php echo _('Pages'); ?></p>
    <div class="info-box-content">
        <table class="dt" perPage="30,50,100">
            <thead>
                <tr>
                    <th><?php echo _('Keyword'); ?></th>
                    <th class="text-right" sort="1 desc" column="formatted-num"><?php echo _('Visits'); ?></th>
                    <th class="text-right" column="formatted-num"><?php echo _('Pages/Visit'); ?></th>
                    <th class="text-right"><?php echo _('Avg. Time on Site'); ?></th>
                    <th class="text-right" column="formatted-num"><?php echo _('% New Visits'); ?></th>
                    <th class="text-right" column="formatted-num"><?php echo _('Bounce Rate'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
            if ( is_array( $keywords ) )
            foreach ( $keywords as $k ) {
            ?>
            <tr>
                <td><a href="/analytics/traffic-sources/keyword/?k=<?php echo urlencode( $k['keyword'] ); ?>" title="<?php echo $k['keyword']; ?>"><?php echo $k['keyword']; ?></a></td>
                <td class="text-right"><?php echo number_format( $k['visits'] ); ?></td>
                <td class="text-right"><?php echo number_format( $k['pages_by_visits'], 2 ); ?></td>
                <td class="text-right"><?php echo $k['time_on_site']; ?></td>
                <td class="text-right"><?php echo $k['new_visits']; ?>%</td>
                <td class="text-right last"><?php echo $k['bounce_rate']; ?>%</td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
        <br />
    </div>
</div>
<br clear="left" />

<script type="text/javascript">
    function open_flash_chart_data() {
        return JSON.stringify(<?php echo $pie_chart; ?>);
    }
    plotting_data = [<?php echo $visits_plotting; ?>];
    plotting_label = 'Visits';
</script>
<?php
nonce::field( 'get_graph', '_get_graph');

echo $template->end();
?>