<?php
/**
 * @package Grey Suit Retail
 * @page Page | Content Overview | Analytics
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
<h1><?php echo _('Source'), ': ', $_GET['s']; ?></h1>
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

<script type="text/javascript">
    plotting_data = [<?php echo $visits_plotting; ?>];
    plotting_label = 'Visits';
</script>
<?php
nonce::field( 'get_graph', '_get_graph');

echo $template->end();
?>