<?php
/**
 * @package Grey Suit Retail
 * @page Email Analytics
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var stdClass $email
 * @var string $bar_chart
 * @var EmailMessage $email_message
 */
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Email: <?php echo $email->subject ?>
            </header>

            <div class="panel-body">

                <div id="graph-large"></div>

            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Email Details
            </header>

            <div class="panel-body">

                <div class="row" id="email-details">
                    <div class="col-lg-3">
                        <span class="data"><?php echo $email->requests ?></span> Emails Sent
                    </div>
                    <div class="col-lg-3">
                        <span class="data"><?php echo $email->opens ?></span> Opens
                    </div>
                    <div class="col-lg-3">
                        <span class="data"><?php echo $email->clicks ?></span> Clicks
                    </div>
                    <div class="col-lg-3">
                        <span class="data"><?php echo $email->bounces ?></span> Bounces
                    </div>
                </div>

            </div>
        </section>
    </div>
</div>

<script>
    function open_flash_chart_data() {
        return JSON.stringify(<?php echo $bar_chart; ?>);
    }
    $(function(){
        swfobject.embedSWF("/media/flash/open-flash-chart.swf", "graph-large", "787", "387", "9.0.0", "", null, { wmode:"transparent" } );
    });
</script>