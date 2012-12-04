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
 * @var array $comments
 */

// Determine the select options
$admin_user_options = '';
$admin_user_ids = array();

foreach ( $admin_users as $au ) {
    $selected = ( $ticket->assigned_to_user_id == $au->user_id ) ? ' selected="selected"' : '';

    $admin_user_options .= '<option value="' . $au->user_id . '"' . $selected . '>' . $au->contact_name . "</option>\n";

    $admin_user_ids[] = $au->user_id;
}

// Find out if the user is an admin user
$user_is_admin = in_array( $ticket->user_id, $admin_user_ids );

echo $template->start( $ticket->summary, false );
nonce::field( 'update_status', '_update_status' );
nonce::field( 'update_priority', '_update_priority' );
nonce::field( 'delete_comment', '_delete_comment' );
nonce::field( 'update_assigned_to', '_update_assigned_to' );
nonce::field( 'upload_to_comment', '_upload_to_comment' );
?>

<table class="formatted">
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
            <strong><?php echo _('Account'); ?></strong>
            <a href="http://<?php echo $ticket->domain; ?>/" title="<?php echo $ticket->website; ?>" target="_blank"><?php echo $ticket->website; ?></a>
            <?php if ( !empty( $ticket->website_id ) ) { ?>
                <br />
                (<a href="/accounts/control/?aid=<?php echo $ticket->website_id; ?>" target="_blank" title="<?php echo _('Control'); ?>"><?php echo _('Control'); ?></a>

                <?php if ( $user->has_permission( User::ROLE_SUPER_ADMIN ) ) { ?>
                    | <a href="/accounts/edit/?aid=<?php echo $ticket->website_id; ?>" target="_blank" title="<?php echo _('Edit'); ?>"><?php echo _('Edit'); ?></a><?php } ?>)
            <?php } ?>
        </td>
        <td>
            <strong><?php echo _('Browser'); ?></strong>
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
            <select id="sStatus">
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

<?php if ( is_array( $ticket_uploads ) ) { ?>
    <br />
    <div class="uploads">
        <p><strong><?php echo _('Attachments'); ?>:</strong></p>
        <?php foreach ( $ticket_uploads as $upload ) { ?>
            <p><a href="http://s3.amazonaws.com/retailcatalog.us/attachments/<?php echo $upload; ?>" target="_blank" title="<?php echo _('Download'); ?>"><?php echo f::name( $upload ); ?></a></p>
        <?php } ?>
    </div>
<?php } ?>

<br /><hr />

<div id="comments">
    <form name="fAddComment" method="post" action="/tickets/add-comment/" ajax="1">
        <div id="comment-wrapper">
            <textarea id="comment" name="comment" cols="5" rows="3" tmpval="<?php echo _('Write a comment...'); ?>"></textarea>
        </div>
        <input type="submit" id="add-comment" class="button hidden" value="<?php echo _('Add Comment'); ?>" />
        <div id="private-wrapper" class="hidden">
            <input type="checkbox" name="private" id="private" value="1" /> <label for="private"><?php echo _('Private'); ?></label>
        </div>

        <div id="uploader" class="hidden"></div>
        <a href="#" id="attach" class="button hidden" title="<?php echo _('Attach'); ?>"><?php echo _('Attach'); ?></a>

        <br clear="all" />
        <div id="uploads"></div>
        <input type="hidden" name="hTicketId" id="hTicketId" value="<?php echo $ticket->id; ?>" />
        <?php nonce::field('add_comment'); ?>
    </form>

    <div class="divider" id="comments-divider"></div>
    <div id="comments-list">
    <?php
    $confirmation = _('Are you sure you want to delete this comment? This cannot be undone.');

    if ( is_array( $comments ) )
    foreach ( $comments as $comment ) {

        if ( $user->id == $ticket->user_id && '1' == $comment->private )
            continue;

        $date = new DateTime( $comment->date_created );
    ?>
    <div class="comment" id="comment-<?php echo $comment->id; ?>">
        <p class="name">
            <?php if ( '1' == $comment->private ) { ?>
                <img src="/images/icons/lock.gif" width="11" height="15" alt="<?php echo _('Private'); ?>" class="private" />
            <?php
            }

            if ( in_array( $comment->user_id, $admin_user_ids ) )
                echo '<a href="#" class="assign-to" rel="', $comment->user_id, '">';

            echo $comment->name;

            if ( in_array( $comment->user_id, $admin_user_ids ) )
                echo '</a>';
            ?>
            <span class="date"><?php echo $date->format( 'F j, Y g:ia' ); ?></span>

            <a href="#" class="delete-comment" title="<?php echo _('Delete'); ?>" confirm="<?php echo $confirmation; ?>"><img src="/images/icons/x.png" alt="<?php echo _('X'); ?>" width="15" height="17" /></a>
        </p>
        <p class="message"><?php echo $comment->comment; ?></p>

        <div class="attachments">
        <?php
        if ( is_array( $comment->uploads ) )
        foreach ( $comment->uploads as $upload ) {
        ?>
            <p><a href="<?php echo $upload['link']; ?>" target="_blank" title="<?php echo _('Download'); ?>"><?php echo $upload['name']; ?></a></p>
        <?php } ?>
        </div>
        <br clear="left" />
    </div>
    <?php } ?>
    </div>
</div>
<br clear="all" />

<?php echo $template->end(); ?>