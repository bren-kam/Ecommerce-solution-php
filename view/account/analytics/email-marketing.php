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
                Email Marketing Analytics - Last 10 Emails
            </header>

            <div class="panel-body">

                <div class="adv-table">
                    <table class="dt display table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Sent</th>
                                <th>Opens</th>
                                <th>Clicked</th>
                                <th>Bounces</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            foreach ( $emails as $email ):
                                if ( !isset( $email->subject ) )
                                    continue;
                                $date = new DateTime( $email->date_sent );
                            ?>
                                <tr>
                                    <td><a href="<?php echo url::add_query_arg( 'eid', $email->id, '/analytics/email/' ); ?>" title="<?php echo $email->subject; ?>"><?php echo $email->subject; ?></a></td>
                                    <td><?php echo $stats[$email->id]->requests; ?></td>
                                    <td><?php echo $stats[$email->id]->opens; ?></td>
                                    <td><?php echo $stats[$email->id]->clicks; ?></td>
                                    <td><?php echo $stats[$email->id]->bounces; ?></td>
                                    <td><?php echo $date->format( 'F jS, Y'); ?></td>
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