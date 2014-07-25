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

if ( '/' == $_GET['p'] )
    $_GET['p'] = 'Home';

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
<h1><?php echo _('Page'), ': ', $_GET['p']; ?></h1>
<br clear="all" /><br />

<div id="dLargeGraphWrapper"><div id="dLargeGraph"></div></div>
<br />
<div class="info-box col-1">
    <p class="info-box-title"><?php echo _('Site Usage'); ?></p>
    <div class="info-box-content">
        <table id="sparklines">
            <tr>
                <td width="15%"><a href="#page_views" class="sparkline" title="<?php echo _('Page Views Sparkline'); ?>"><img src="<?php echo $sparklines['page_views']; ?>" width="150" height="36" alt="<?php echo _('Page Views Sparkline'); ?>" /></a></td>
                <td width="35%"><span class="data"><?php echo number_format( $total['page_views'] ); ?></span> <span class="label"><?php echo _('Page Views'); ?></span></td>
                <td width="15%"><a href="#time_on_page" class="sparkline" title="<?php echo _('Time On Page Sparkline'); ?>"><img src="<?php echo $sparklines['time_on_page']; ?>" width="150" height="36" alt="<?php echo _('Time On Page'); ?>" /></a></td>
                <td><span class="data"><?php echo $total['time_on_page']; ?></span> <span class="label"><?php echo _('Time On Page'); ?></span></td>
            </tr>
            <tr>
                <td><a href="#bounce_rate" class="sparkline" title="<?php echo _('Bounce Rate Sparkline'); ?>"><img src="<?php echo $sparklines['bounce_rate']; ?>" class="sparkline" width="150" height="36" alt="<?php echo _('Bounce Rate Sparkline'); ?>" /></a></td>
                <td><span class="data"><?php echo number_format( $total['bounce_rate'], 2 ); ?>%</span> <span class="label"><?php echo _('Bounce Rate'); ?></span></td>
                <td><a href="#exit_rate" class="sparkline" title="<?php echo _('Exit Rate Sparkline'); ?>"><img src="<?php echo $sparklines['exit_rate']; ?>" class="sparkline" width="150" height="36" alt="<?php echo _('Exit Rate Sparkline'); ?>" /></a></td>
                <td><span class="data"><?php echo number_format( $total['exit_rate'], 2 ); ?>%</span> <span class="label"><?php echo _('Exit Rate'); ?></span></td>
            </tr>
        </table>
    </div>
</div>
<br clear="both" /><br />

<script type="text/javascript">
    plotting_data = [<?php echo $page_views_plotting; ?>];
    plotting_label = 'Page Views';
</script>
<?php
nonce::field( 'get_graph', '_get_graph');

echo $template->end();
?>