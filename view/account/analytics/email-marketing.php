<?php
/**
 * @package Grey Suit Retail
 * @page List Website Pages
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var array $emails
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
                <th><?php echo _('Date'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ( $emails as $email ) {
				if ( !isset( $email->name ) )
					continue;
					
                $date = new DateTime( $email->sdate );
                ?>
                <tr>
                    <td><a href="<?php echo url::add_query_arg( 'accid', $email->id, '/analytics/email/' ); ?>" title="<?php echo $email->name; ?>"><?php echo $email->name; ?></a></td>
                    <td><?php echo $email->send_amt; ?></td>
                    <td><?php echo $email->uniqueopens; ?></td>
                    <td><?php echo $email->uniquelinkclicks; ?></td>
                    <td><?php echo $date->format( 'F jS, Y'); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php echo $template->end(); ?>