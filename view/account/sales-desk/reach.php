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
nonce::field( 'update_status', '_update_status');
nonce::field( 'update_assigned_to', '_update_assigned_to' );
nonce::field( 'update_priority', '_update_priority' );
nonce::field( 'delete_comment', '_delete_comment' );

$priorities = array(
    WebsiteReach::PRIORITY_NORMAL => _('Normal'),
    WebsiteReach::PRIORITY_HIGH => _('High'),
    WebsiteReach::PRIORITY_URGENT => _('Urgent')
);

$statuses = array(
    WebsiteReach::STATUS_OPEN => _('Open'),
    WebsiteReach::STATUS_CLOSED => _('Closed')
);

$reach->get_info();

?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading" id="reach-title">
                Quote #<?php echo $reach->id ?>

                <?php if ( $reach->priority == WebsiteReach::PRIORITY_URGENT ): ?>
                    <span class="label label-danger">URGENT</span>
                <?php endif; ?>
                <?php if ( $reach->priority == WebsiteReach::PRIORITY_HIGH ): ?>
                    <span class="label label-warning">High priority</span>
                <?php endif; ?>
            </header>

            <div class="panel-body">

                <div class="row">
                    <div class="col-lg-4">
                        <strong>Name:</strong> <?php echo $reach->name ?>
                    </div>
                    <div class="col-lg-4">
                        <strong>Date:</strong> <?php echo $date->format( 'F jS, Y' ) ?>
                    </div>
                    <div class="col-lg-4">
                        <strong>Type:</strong> <?php echo str_replace( array( 'contact', 'quote' ), array( 'Contact Form', 'Product Request a Quote' ), $reach->meta['type'] );  ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="assigned-to">Assigned To:</label>
                            <select id="assigned-to" class="form-control">
                                <option value="0" <?php if( $reach->assigned_to_user_id == 0 ) echo 'selected' ?>>Unassigned</option>
                                <?php foreach ( $assignable_users as $au ): ?>
                                    <option value="<?php echo $au->id ?>" <?php if( $reach->assigned_to_user_id == $au->id ) echo 'selected' ?>><?php echo $au->contact_name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="priority">Priority:</label>
                            <select id="priority" class="form-control">
                                <?php foreach ( $priorities as $key => $p ): ?>
                                    <option value="<?php echo $key ?>" <?php if( $reach->priority == $key ) echo 'selected' ?>><?php echo $p ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select id="status" class="form-control">
                                <?php foreach ( $statuses as $key => $s ): ?>
                                    <option value="<?php echo $key ?>" <?php if( $reach->status == $key ) echo 'selected' ?>><?php echo $s ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <?php if ( $reach->info['SKU'] ): ?>
                    <h4>Product - Information</h4>
                    <p><?php echo "Brand: " . $reach->info['Brand'] . ' <br> Product: ' . $reach->info['SKU'] . ' - ' . $reach->info['Product'] . ' <br> Location: ' . $reach->info['Location'] ?></p>
                <?php endif; ?>

                <?php if ( $reach->message ): ?>
                    <h4>Message:</h4>
                    <p><?php echo $reach->message ?></p>
                <?php endif; ?>

            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">

        <section class="panel">
            <header class="panel-heading">
                Comments
            </header>

            <div class="panel-body">

                <form id="add-comment-form" method="post" role="form">
                    <div class="form-group">
                        <textarea class="form-control" name="comment" id="comment" rows="1" placeholder="Write a comment..."></textarea>
                    </div>
                    <div class="row hidden clearfix">
                        <div class="col-lg-10 text-right">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="private">Private Comment
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <button type="submit" class="btn btn-primary">Send comment</button>
                        </div>
                    </div>

                    <input type="hidden" name="hReachId" id="hReachId" value="<?php echo $reach->id; ?>" />
                    <?php nonce::field('add_comment'); ?>
                </form>

                <div class="timeline-messages" id="comments">

                    <?php
                    if ( is_array( $comments ) )
                        foreach ( $comments as $comment ):
                            if ( $user->id == $reach->website_user_id && '1' == $comment->private )
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

                                        <a href="javascript:;" data-assign-to="<?php echo $comment->user_id ?>" title="Assign Reach to this user">
                                            <?php echo $comment->contact_name ? $comment->contact_name : $comment->website_user_name ?>
                                        </a>

                                        at <?php echo $date->format( 'F j, Y g:ia' ); ?>

                                        <a href="javascript:;" class="delete-comment pull-right" title="Delete this comment" data-comment-id="<?php echo $comment->id?>"><i class="fa fa-trash-o"></i></a>
                                    </p>
                                    <p class="comment-text"><?php echo $comment->comment ?></p>

                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
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

                <a href="javascript:;" data-assign-to="" title="Assign Reach to this user">
                    <span class="template-contact-name"></span>
                </a>

                just now.

                <a href="javascript:;" class="delete-comment pull-right" title="Delete this comment" data-comment-id=""><i class="fa fa-trash-o"></i></a>
            </p>
            <p class="comment-text"></p>

        </div>
    </div>
</div>

