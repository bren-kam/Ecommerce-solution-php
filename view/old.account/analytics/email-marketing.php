<?php
/**
 * @package Grey Suit Retail
 * @page List Website Pages
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var EmailMessage[] $emails
 * @var stdClass[] $stats
 */

echo $template->start( _('Email Marketing Analytics') );
?>

<div>
    <h2><?php echo _('Last 10 Emails'); ?></h2>
	<br />
    <table class="dt">
        <thead>
            <tr>
                <th><?php echo _('Subject'); ?></th>
                <th><?php echo _('Sent *'); ?></th>
                <th><?php echo _('Opens *'); ?></th>
                <th><?php echo _('Clicked *'); ?></th>
                <th><?php echo _('Bounces *'); ?></th>
                <th><?php echo _('Date'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ( $emails as $email ) {
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
            <?php } ?>
        </tbody>
    </table>
</div>

<?php echo $template->end(); ?>