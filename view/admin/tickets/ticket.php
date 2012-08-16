<?php
/**
 * @package Grey Suit Retail
 * @page Ticket
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Ticket $ticket
 * @var array $admin_users
 * @var array $ticket_uploads
 */

// Determine the select options
$admin_user_options = '';

foreach ( $admin_users as $au ) {
    $selected = ( $ticket->assigned_to_user_id == $au->user_id ) ? ' selected="selected"' : '';

    $admin_user_options .= '<option value="' . $au->user_id . '"' . $selected . '>' . $au->contact_name . "</option>\n";

    $admin_user_ids[] = $au->user_id;
}

// Find out if the user is an admin user
$user_is_admin = in_array( $ticket->user_id, $admin_user_ids );

echo $template->start( $ticket->summary, false );
?>

<table>
    <tr>
        <td>
            <strong><?php echo _('Name'); ?></strong>
            <?php if ( $user_is_admin ) { ?>
                <a href="#" class="assign-to" rel="<?php echo $ticket->user_id; ?>">
            <?php
            }
            echo $ticket->name;

            if ( $user_is_admin )
                echo '</a>';
            ?>
        </td>
        <td>
            <strong><?php echo _('Date'); ?></strong>
            <?php
            $date = new DateTime( $ticket->date_created );
            echo $date->format( 'F jS, Y' );
            ?>
        </td>
    </tr>
    <tr>
        <td>
            <strong><?php echo _('Website'); ?></strong>
            <a href="http://<?php echo $ticket->domain; ?>/" title="<?php echo $ticket->website; ?>" target="_blank"><?php echo $ticket->website; ?></a>
            <?php if ( !empty( $ticket->website_id ) ) { ?>
                <br />
                (<a href="/accounts/control/?aid=<?php echo $ticket->website_id; ?>" target="_blank" title="<?php echo _('Control'); ?>"><?php echo _('Control'); ?></a>

                <?php if ( $user->has_permission(10) ) { ?>
                    | <a href="/accounts/edit/?aid=<?php echo $ticket->website_id; ?>" target="_blank" title="<?php echo _('Edit'); ?>"><?php echo _('Edit'); ?></a><?php } ?>)
            <?php } ?>
        </td>
        <td>
            <strong><?php echo _('Browser/OS'); ?></strong>
            <?php echo $ticket->browser_name, ' ', $ticket->browser_version ?>
        </td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
        <td>
            <strong><?php echo _('Assigned To'); ?></strong>
            <br />
            <select id="sAssignedTo">
                <?php echo $admin_user_options; ?>
            </select>
        </td>
        <td>
            <strong><?php echo _('Status'); ?></strong>
            <br />
            <select id="sStatus" class="dd" style="width: 150px">
            <?php
            $statuses = array(
                0 => _('Open'),
                1 => _('Closed')
            );

            foreach ( $statuses as $sn => $s ) {
                $selected = ( $ticket->status == $sn ) ? ' selected="selected"' : '';

                echo '<option value="' . $sn . '"' . $selected . '>' . $s . "</option>\n";
            }
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <strong><?php echo _('Priority'); ?></strong>
            <br />
            <select id="sPriority">
            <?php
            $priorities = array(
                0 => _('Normal'),
                1 => _('High'),
                2 => _('Urgent')
            );

            foreach ( $priorities as $pn => $p ) {
                $selected = ( $ticket->priority == $pn ) ? ' selected="selected"' : '';

                echo '<option value="' . $pn . '"' . $selected . '>' . $p . "</option>\n";
            }
            ?>
            </select>
        </td>
    </tr>
</table>
<br /><br />

<h2><?php echo _('Message'); ?></h2>
<blockquote>
    <?php echo $ticket->message; ?>
</blockquote>

<div class="uploads">
    <?php
    if ( is_array( $ticket_uploads ) )
    foreach ( $ticket_uploads as $upload ) {
    ?>
        <a href="<?php echo $upload['link']; ?>" target="_blank" title="<?php echo _('Download'); ?>"><?php echo $upload['name']; ?></a>
    <?php } ?>
</div>

<?php echo $template->end(); ?>