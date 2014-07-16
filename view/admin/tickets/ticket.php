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

$statuses = array(
    Ticket::STATUS_OPEN => _('Open'),
    Ticket::STATUS_CLOSED => _('Closed')
);

$priorities = array(
    Ticket::PRIORITY_NORMAL => _('Normal'),
    Ticket::PRIORITY_HIGH => _('High'),
    Ticket::PRIORITY_URGENT => _('Urgent')
);

nonce::field( 'update_status', '_update_status' );
nonce::field( 'update_priority', '_update_priority' );
nonce::field( 'delete_comment', '_delete_comment' );
nonce::field( 'update_assigned_to', '_update_assigned_to' );
nonce::field( 'upload_to_comment', '_upload_to_comment' );

?>

<div class="row-fluid">
    <div class="col-lg-8">

        <section class="panel">
            <header class="panel-heading" id="ticket-title">
                <?php echo $ticket->summary ?>
                <?php if ( $ticket->priority == Ticket::PRIORITY_URGENT ): ?>
                    <span class="label label-danger">URGENT TICKET</span>
                <?php endif; ?>
                <?php if ( $ticket->priority == Ticket::PRIORITY_HIGH ): ?>
                    <span class="label label-warning">High priority</span>
                <?php endif; ?>
            </header>

            <div class="panel-body">
                <?php echo $ticket->message ?>

                <?php if ( !empty( $ticket_uploads ) ): ?>
                    <br /><br />
                    <ul class="list-inline comment-attachments">
                        <li>Attachments:</li>
                        <?php foreach ( $ticket_uploads as $upload ): ?>
                            <li>
                                <a href="http://s3.amazonaws.com/retailcatalog.us/attachments/<?php echo $upload; ?>" target="_blank" title="Download"><?php echo f::name( $upload ); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

            </div>
        </section>

        <section class="panel">
            <header class="panel-heading">
                Comments
            </header>

            <div class="panel-body">

                <form id="add-comment-form" method="post" role="form">
                    <div class="form-group">
                        <textarea class="form-control" name="comment" id="comment" rows="1" placeholder="Write a comment..."></textarea>
                    </div>
                    <div class="checkbox hidden">
                        <label>
                            <input type="checkbox" name="private"> This is a Private Comment
                        </label>
                    </div>
                    <div class="row hidden clearfix">
                        <div class="col-lg-4">
                            <button type="button" id="upload" class="btn btn-default">Attach</button>

                            <div class="progress progress-sm hidden" id="upload-loader">
                                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>

                            <!-- Where the uploader lives -->
                            <div id="upload-files"></div>

                            <ul id="file-list"></ul>
                        </div>
                        <div class="col-lg-8">
                            <button type="submit" class="btn btn-primary pull-right">Send comment</button>
                        </div>
                    </div>

                    <input type="hidden" name="hTicketId" id="hTicketId" value="<?php echo $ticket->id; ?>" />
                    <?php nonce::field('add_comment'); ?>
                </form>

                <div class="timeline-messages" id="ticket-comments">

                    <?php
                    if ( is_array( $comments ) )
                        foreach ( $comments as $comment ):
                            if ( $user->id == $ticket->user_id && '1' == $comment->private )
                                continue;

                            $date = new DateTime( $comment->date_created );
                    ?>

                        <div class="msg-time-chat">
                            <div class="message-body msg-in">
                                <span class="arrow"></span>
                                <div class="text">
                                    <p class="attribution clearfix">
                                        <?php if ( $comment->private ): ?>
                                            <i class="fa fa-lock" title="This is a private comment"></i>
                                        <?php endif; ?>

                                        <?php if ( in_array( $comment->user_id, $admin_user_ids ) ): ?>
                                            <a href="javascript:;" data-assign-to="<?php echo $comment->user_id ?>" title="Assign ticket to this user">
                                                <?php echo $comment->name?>
                                            </a>
                                        <?php else: ?>
                                            <?php echo $comment->name?>
                                        <?php endif; ?>

                                        at <?php echo $date->format( 'F j, Y g:ia' ); ?>

                                        <a href="javascript:;" class="delete-comment pull-right" title="Delete this comment" data-comment-id="<?php echo $comment->id?>"><i class="fa fa-trash-o"></i></a>
                                    </p>
                                    <p class="comment-text"><?php echo $comment->comment ?></p>

                                    <?php if ( is_array( $comment->uploads ) ): ?>
                                        <ul class="list-inline comment-attachments">
                                            <li>Attachments:</li>
                                            <?php foreach ( $comment->uploads as $upload ): ?>
                                                <li><a href="<?php echo $upload['link']?>" target="_blank"><?php echo $upload['name']; ?></a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </div>
            </div>
        </section>

    </div>

    <div class="col-lg-4">
        <section class="panel">
            <header class="panel-heading">
                Information
            </header>

            <div class="panel-body">
                <ul>
                    <li>
                        <strong>From:</strong>
                        <?php if ( $user_is_admin ): ?>
                            <a href="javascript:;" title="Assign ticket to this user" data-assign-to="<?php echo $ticket->user_id ?>"><?php echo $ticket->name ?></a>
                        <?php else:
                            echo $ticket->name;
                        endif; ?>
                    </li>
                    <li>
                        <strong>Account:</strong>
                        <?php if ( $ticket->website ): ?>
                            <a href="http://<?php echo $ticket->domain; ?>/" title="Go to http://<?php echo $ticket->domain; ?>" target="_blank"><?php echo $ticket->website; ?></a>
                            <?php if ( !empty( $ticket->website_id ) ): ?>
                                <br>
                                (
                                <a href="/accounts/control/?aid=<?php echo $ticket->website_id; ?>" target="_blank" title="<?php echo _('Control'); ?>"><?php echo _('Control'); ?></a>
                                <?php if ( $user->has_permission( User::ROLE_SUPER_ADMIN ) ): ?>
                                    | <a href="/accounts/edit/?aid=<?php echo $ticket->website_id; ?>" target="_blank" title="<?php echo _('Edit'); ?>"><?php echo _('Edit'); ?></a>
                                <?php endif; ?>
                                )
                            <?php endif; ?>
                        <?php else: ?>
                            No Account
                        <?php endif; ?>
                    </li>
                    <li>
                        <strong>Date:</strong>
                        <?php
                        $date = new DateTime( $ticket->date_created );
                        echo $date->format( 'F jS, Y' );
                        ?>
                    </li>
                    <li>
                        <strong>Browser:</strong>
                        <?php echo $ticket->browser_name, ' ', $ticket->browser_version ?>
                    </li>
                </ul>

                <div class="form-group">
                    <label for="sAssignedTo">Assigned To:</label>
                    <select class="form-control" id="sAssignedTo">
                        <?php echo $admin_user_options; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sStatus">Status:</label>
                    <select class="form-control" id="sStatus">
                        <?php foreach ( $statuses as $sn => $s ): ?>
                            <option value="<?php echo $sn ?>" <?php if ( $ticket->status == $sn ) echo ' selected="selected"' ?>><?php echo $s ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sPriority">Priority:</label>
                    <select class="form-control" id="sPriority">
                        <?php foreach ( $priorities as $pn => $p ): ?>
                            <option value="<?php echo $pn ?>" <?php if ( $ticket->priority == $pn ) echo ' selected="selected"' ?>><?php echo $p ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </section>
    </div>
</div>


<div class="msg-time-chat hidden" id="comment-template">
    <div class="message-body msg-in">
        <span class="arrow"></span>
        <div class="text">
            <p class="attribution clearfix">
                <i class="fa fa-lock template-private-comment" title="This is a private comment"></i>

                <?php if ( in_array( $user->id, $admin_user_ids ) ): ?>
                    <a href="javascript:;" data-assign-to="" title="Assign ticket to this user">
                        <span class="template-contact-name"><?php echo $comment->name?></span>
                    </a>
                <?php else: ?>
                    <span class="template-contact-name"><?php echo $comment->name?></span>
                <?php endif; ?>

                just now.

                <a href="javascript:;" class="delete-comment pull-right" title="Delete this comment" data-comment-id=""><i class="fa fa-trash-o"></i></a>
            </p>
            <p class="comment-text"></p>

            <ul class="list-inline comment-attachments hidden">
                <li>Attachments:</li>
            </ul>
        </div>
    </div>
</div>
