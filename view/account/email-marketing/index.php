<?php
/**
 * @package Grey Suit Retail
 * @page Dashboard | Email Marketing
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var EmailMessage[] $messages
 * @var Email[] $subscribers
 * @var AnalyticsEmail $email
 * @var string $bar_chart
 * @var int $email_count
 */
?>
<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Dashboard
            </header>

            <div class="panel-body">
                <?php if ( $email ) : ?>
                    <p>Last Email: <?php echo $messages[0]->subject ?></p>

                    <div id="statistics"></div>

                <?php else: ?>
                    <div class="alert alert-info">
                        You have not yet sent out an email. <a href="/email-marketing/campaigns/create/">Click here</a> to get started.
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Emails Sent
            </header>

            <div class="panel-body">
                <?php foreach ( $messages as $message ) : ?>
                    <p><a href="<?php echo url::add_query_arg( 'eid', $message->id, '/analytics/email/' ); ?>" title="<?php echo $message->subject; ?>"><?php echo $message->subject; ?></a></p>
                <?php endforeach; ?>
                <p class="text-right">
                    <a href="/email-marketing/campaigns/" class="btn btn-primary">View All</a>
                </p>
            </div>
        </section>
    </div>
    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                Latest Subscribers
            </header>

            <div class="panel-body">
                <?php foreach ( $subscribers as $s ) : ?>
                    <p><?php echo $s->email; ?></p>
                <?php endforeach; ?>
                <p class="text-right">
                    <a href="/email-marketing/subscribers/" class="btn btn-primary">View All</a>
                </p>
            </div>
        </section>
    </div>
</div>

<script>
    function open_flash_chart_data() {
        return JSON.stringify(<?php echo $bar_chart; ?>);
    }
    $(function(){
        swfobject.embedSWF("/media/flash/open-flash-chart.swf", "statistics", "787", "387", "9.0.0", "", null, { wmode:"transparent" } );
    });
</script>