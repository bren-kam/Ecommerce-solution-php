<?php
/**
 * @package Grey Suit Retail
 * @page Reach | Reaches | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var WebsiteReach $reach
 * @var WebsiteReachComment[] $comments
 * @var User[] $assignable_users
 */

$date = new DateTime( $reach->date_created );
echo $template->start( _('Quote') . ' #' . $reach->id, '../sidebar' );
?>

<input type="hidden" id="hWebsiteID" value="<?php echo $reach->website_id; ?>" />
<input type="hidden" id="hUserID" value="<?php echo $user->id; ?>" />
<?php
nonce::field( 'update_status', '_update_status');
nonce::field( 'update_assigned_to', '_update_assigned_to' );
nonce::field( 'update_priority', '_update_priority' );
$delete_comment_nonce = nonce::create( 'delete_comment' );
?>
<table class="formatted">
    <tr>
        <td>
            <strong><?php echo _('Name'); ?></strong>
            <?php echo $reach->name; ?>
        </td>
        <td>
            <strong><?php echo _('Date'); ?></strong>
            <?php
            $date = new DateTime( $reach->date_created );
            echo $date->format( 'F jS, Y' );
            ?>
        </td>
    </tr>
    <tr>
        <td rowspan="3">
            <strong><?php echo _('Information:'); ?></strong>
            <p>
                <ul>
                    <?php
                    if ( $reach->info )
                    foreach ( $reach->info as $key => $value ) {
                        ?>
                            <li><?php echo $key; ?>: <?php echo $value; ?></li>
                    <?php } ?>
                </ul>
            </p>
        </td>
        <td>
            <label for="sAssignedTo"><?php echo _('Assigned To'); ?></label>
            <br />
            <select id="sAssignedTo">
                <?php
                    foreach ( $assignable_users as $au ) {
                        $selected = ( $reach->assigned_to_user_id == $au->id ) ? ' selected="selected"' : '';

                        echo '<option value="' . $au->id . '"' . $selected . '>' . $au->contact_name . "</option>";
                    }
                ?>
            </select>
            <span><?php if ( '0000-00-00 00:00:00' != $reach->assigned_to_date ) echo ' ', $date->format('F jS, Y g:ia'); ?></span>
        </td>
    </tr>
    <tr>
        <td>
            <label for="sPriority"><?php echo _('Priority'); ?></label>
            <br />
            <select id="sPriority">
            <?php
                $priorities = array(
                    WebsiteReach::PRIORITY_NORMAL => _('Normal'),
                    WebsiteReach::PRIORITY_HIGH => _('High'),
                    WebsiteReach::PRIORITY_URGENT => _('Urgent')
                );

                foreach ( $priorities as $pn => $p ) {
                    $selected = ( $reach->priority == $pn ) ? ' selected="selected"' : '';

                    echo '<option value="' . $pn . '"' . $selected . '>' . $p . "</option>";
                }
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <label for="sStatus"><?php echo _('Status'); ?></label>
            <br />
            <select id="sStatus" class="dd">
            <?php
                $statuses = array(
                    WebsiteReach::STATUS_OPEN => _('Open'),
                    WebsiteReach::STATUS_CLOSED => _('Closed')
                );

                foreach ( $statuses as $sn => $s ) {
                    $selected = ( $reach->status == $sn ) ? ' selected="selected"' : '';

                    echo '<option value="' . $sn . '"' . $selected . '>' . $s . "</option>\n";
                }
            ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <strong>Type:</strong> <?php echo str_replace( array( 'contact', 'quote' ), array( 'Contact Form', 'Product Request a Quote' ), $reach->meta['type'] );  ?>
        </td>
        <td></td>
    </tr>
</table>
<br /><br />

<h2><?php echo _('Message'); ?></h2>
<blockquote>
    <?php echo $reach->message; ?>
</blockquote>

<br /><hr />
<div id="comments">
    <form action="/products/reaches/add-comment/" method="POST" ajax="1">
        <div id="comment-wrapper">
            <textarea name="comment" id="comment" cols="5" rows="3" placeholder="<?php echo _('Write a comment...'); ?>"></textarea>
        </div>

        <input type="submit" id="add-comment" class="button hidden" value="<?php echo _('Add Comment'); ?>" />
        <div id="private-wrapper" class="hidden">
            <input type="checkbox" name="private" id="private" value="1" /> <label for="private"><?php echo _('Private'); ?></label>
        </div>

        <input type="hidden" name="hReachId" id="hReachId" value="<?php echo $reach->id; ?>" />
        <?php nonce::field( 'add_comment' ); ?>
    </form>
    <br clear="all" />
    <div class="divider" id="comments-divider"></div>
    <div id="comments-list">
        <?php
        $confirmation = _('Are you sure you want to delete this comment? This cannot be undone.');

        if ( is_array( $comments ) )
        foreach ( $comments as $comment ) {
            $date = new DateTime( $comment->date_created );
        ?>
        <div class="comment" id="comment-<?php echo $comment->id; ?>">
            <p class="name">
                <?php if ( '1' == $comment->private ) { ?>
                <img src="/images/icons/reaches/lock.gif" width="11" height="15" alt="<?php echo _('Private'); ?>" class="private" />
                <?php
                }

                if ( in_array( $comment->user_id, $assignable_users ) )
                    echo '<a href="#" class="assign-to" rel="', $comment->user_id, '">';

                echo $comment->contact_name;

                if ( in_array( $comment->user_id, $assignable_users ) )
                    echo '</a>'
                ?>
                <span class="date"><?php echo $date->format( 'F jS, Y g:ia' ); ?></span>
                <?php if ( $user->id == $comment->user_id ): ?>
                    <a href="<?php echo url::add_query_arg( array( '_nonce' => $delete_comment_nonce, 'wrcid' => $comment->id ), '/products/reaches/delete-comment/' ); ?>" ajax="1" class="delete-comment" title="<?php echo _('Delete Feedback Comment'); ?>" confirm="<?php echo $confirmation; ?>">
                        <img src="/images/icons/x.png" alt="X" width="16" height="16" />
                    </a>
                <?php endif; ?>
            </p>
            <p class="message"><?php echo $comment->comment; ?></p>
            <br clear="left" />
        </div>
        <?php } ?>
    </div>
</div>
<br clear="all" />

<?php echo $template->end(); ?>