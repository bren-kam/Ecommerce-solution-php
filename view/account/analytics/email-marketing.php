<?php
/**
 * @package Grey Suit Retail
 * @page List Website Pages
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

echo $template->start( _('Email Marketing Analytics') );
?>

<div>
    <table ajax="/analytics/list-emails/" perPage="30,50,100">
        <thead>
            <tr>
                <th><?php echo _('Subject'); ?></th>
                <th class="text-right" column="formatted-num"><?php echo _('Sent *'); ?></th>
                <th class="text-right" column="formatted-num"><?php echo _('Opens *'); ?></th>
                <th class="text-right" column="formatted-num"><?php echo _('Clicked *'); ?></th>
                <th class="text-right" sort="1 desc"><?php echo _('Date'); ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<?php
nonce::field( 'store_session', '_store_session' );
echo $template->end();
?>