<?php
/**
 * @var User $user
 */

nonce::field( 'list_all', 'list-all-nonce' );
nonce::field( 'get', 'get-nonce' );
nonce::field( 'upload_to_comment', 'upload-to-comment-nonce' );
nonce::field( 'update_assigned_to', 'update-assigned-to-nonce' );
nonce::field( 'update_status', 'update-status-nonce' );
nonce::field( 'update_priority', 'update-priority-nonce' );
nonce::field( 'upload_to_ticket', 'upload-to-ticket-nonce' );
nonce::field( 'attach_user_to_account', 'attach-user-to-account-nonce' );
nonce::field( 'get_emails', 'get-emails-nonce' );

foreach ( $admin_users as $au ) {
    $selected = ( $user->user_id == $au->user_id ) ? ' selected="selected"' : '';

    $admin_user_options .= '<option value="' . $au->user_id . '"' . $selected . '>' . $au->contact_name . "</option>\n";

    $admin_user_ids[] = $au->user_id;
}

?>

<!--mail inbox start-->
<div class="mail-box">
    <aside class="sm-side">
        <div class="user-head">
            <form class="pull-left position" action="javascript:;">
                <div class="input-append">
                    <input type="text"  placeholder="Search..." class="sr-input" id="search">
                </div>
            </form>
            <a class="btn pull-right" href="javascript:;" id="compose">
                <i class="fa fa-plus"></i>
            </a>
        </div>
        <div class="inbox-body">
            <select id="filter-assigned-to" class="selectpicker" data-live-search="true" data-style="btn-primary">
                <option value="0">All Users</option>
                <option value="-1">Peers</option>
                <?php echo $admin_user_options; ?>
            </select>

            <select id="filter-status" class="selectpicker" data-style="btn-primary">
                <option value="-1">All Tickets</option>
                <option value="0">Open</option>
                <option value="2" selected="selected">In Progress</option>
                <option value="1">Closed</option>
            </select>
        </div>
        <ul class="inbox-nav inbox-divider" id="inbox-nav">
            <li class="hidden inbox-nav-item" id="inbox-nav-template">
                <a href="javascript:;" class="show-ticket">
                    <div class="pull-left inbox-nav-item-details col-md-12">
                        <ul>
                            <li>
                                <span class="email-name">User Name</span> <span class="email-address">user@email.com</span>
                                <span class="email-date pull-right"><i class="fa fa-circle text-urgent pull-right"></i> 2-18-15</span>
                            </li>
                            <li>
                                <span class="email-subject">Subject Here!</span>
                                <span class="email-status pull-right label label-default"></span>
                            </li>
                        </ul>
                    </div>
                </a>
            </li>
        </ul>
    </aside>

    <aside class="lg-side hidden" id="ticket-container">
        <div class="inbox-head">
            <div class="pull-left ticket-priority">
            </div>

            <div class="pull-left assign-to-container">
                Assigned To:
                <select id="assign-to" class="selectpicker" data-live-search="true" data-style="btn-primary">
                    <?php echo $admin_user_options; ?>
                </select>
            </div>

            <div class="pull-left change-status-container">
                Status:
                <select id="change-status" class="selectpicker" data-style="btn-primary">
                    <option value="0">Open</option>
                    <option value="2">In Progress</option>
                    <option value="1">Closed</option>
                </select>
            </div>

            <div class="pull-left change-priority-container">
                Priority:
                <select id="change-priority" class="selectpicker" data-style="btn-primary">
                    <option value="0">Low</option>
                    <option value="1">High</option>
                    <option value="2">Urgent Issue</option>
                </select>
            </div>

            <div class="pull-left attach-to-account-container">
                Priority:
                <select id="attach-to-account" class="selectpicker" data-style="btn-primary" data-live-search="true">
                    <option value="" selected>Attach user to Account</option>
                    <?php foreach ( $accounts as $account ): ?>
                        <option value="<?php echo $account->website_id ?>"><?php echo $account->title ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="inbox-body">
            <div class="heading-inbox row">
                <div class="col-md-12">
                    <h4 class="ticket-summary"></h4>
                </div>
            </div>
            <div class="sender-info">
                <div class="row">
                    <div class="col-md-12">
                        <ul>
                            <li>Primary Contact: <strong class="ticket-user-name"></strong> <span class="ticket-user-email"></span></li>
                        </ul>
                        <ul>
                            <li>Updated: <strong class="ticket-updated"></strong><br></li>
                            <li>Created: <strong class="ticket-created"></strong><br></li>
                            <li>Account: <strong class="ticket-account"></strong> <a href="javascript:;" class="edit-account">Edit</a> | <a href="javascript:;" class="control-account">Control</a><br></li>
                        </ul>
                    </div>
                </div>
            </div>
            <blockquote class="ticket-message"></blockquote>
            <ul class="list-inline" id="ticket-attachments"></ul>
            <div id="ticket-comments">
                <div class="comment" id="ticket-comment-template">
                    <ul>
                        <li>
                            From: <strong class="comment-user-name">User Name</strong> <span class="comment-user-email">&lt;user@email.com&gt;</span>
                            <span class="pull-right comment-created-ago">March 9th, 2015 at 12:13:14</span>
                        </li>
                        <li>To: <span class="comment-to-address"></span></li>
                    </ul>

                    <p class="comment-message">
                        Faucibus rutrum. Phasellus sodales vulputate urna, vel accumsan augue egestas ac. Donec vitae leo at sem lobortis porttitor eu consequat risus. Mauris sed congue orci. Donec ultrices faucibus rutrum. Phasellus sodales vulputate urna, vel accumsan augue egestas ac. Donec vitae leo at sem lobortis porttitor eu consequat risus. Mauris sed congue orci. Donec ultrices faucibus rutrum. Phasellus sodales vulputate urna, vel accumsan augue egestas ac. Donec vitae leo at sem lobortis porttitor eu consequat risus. Mauris sed congue orci.
                    </p>
                    <ul class="list-inline comment-attachments">
                    </ul>
                </div>
            </div>
            <div class="heading-inbox row">
                <div class="col-md-12">
                    <h4>Reply</h4>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <form metho="post" action="javascript:;" id="send-comment-form">
                        <div class="form-group">
                            <input type="text" class="form-control" id="to-address" name="to-address" value="" placeholder="To/Primary Contact">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="cc-address" name="cc-address" value="" placeholder="CC">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" id="bcc-address" name="bcc-address" value="" placeholder="BCC">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" name="comment" id="reply" rte="1"><?php echo $user->email_signature ?></textarea>
                        </div>
                        <div class="row clearfix">
                            <div class="col-lg-4">
                                <button type="button" id="upload" class="btn btn-default">Attach</button>

                                <div class="progress progress-sm hidden" id="upload-loader">
                                    <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                        <span class="sr-only">Loading...</span>
                                    </div>
                                </div>

                                <!-- Where the uploader lives -->
                                <div id="upload-files"></div>

                                <ul id="file-list" class="list-inline"></ul>
                            </div>
                            <div class="col-lg-8">
                                <button type="submit" class="btn btn-primary pull-right">Send</button>
                                <div class="checkbox pull-right">
                                    <label>
                                        <input type="checkbox" name="private" value="1">This is a Private Message - No email will be send &nbsp;
                                    </label>
                                </div>
                                <input type="hidden" name="ticket-id" id="ticket-id" />
                                <?php nonce::field('add_comment') ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </aside>
    <aside class="lg-side" id="create-ticket">
        <div class="inbox-head">
            <div class="pull-left ticket-status">
                Send a new Message
            </div>
        </div>
        <div class="inbox-body">
            <form method="post" action="javascript:;" role="form" id="new-ticket-form">
                <div class="form-group">
                    <input type="text" class="form-control input-lg" name="to" id="to" placeholder="To Email Address">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control input-lg" id="summary" name="summary" placeholder="Subject">
                </div>
                <div class="form-group">
                    <textarea class="form-control input-lg" name="message" id="message" placeholder="Ticket Description" rows="5"></textarea>
                </div>

                <div class="row clearfix">
                    <div class="col-lg-4">
                        <button type="button" id="new-ticket-upload" class="btn btn-default">Attach</button>

                        <div class="progress progress-sm hidden" id="new-ticket-upload-loader">
                            <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>

                        <!-- Where the uploader lives -->
                        <div id="new-ticket-upload-files"></div>

                        <ul id="new-ticket-file-list" class="list-inline"></ul>
                    </div>
                    <div class="col-lg-8">
                        <button type="submit" class="btn btn-primary pull-right">Create</button>
                        <?php nonce::field('create_ticket') ?>
                        <input type="hidden" id="new-ticket-id" name="ticket-id" value="" />
                    </div>
                </div>

            </form>
        </div>
    </aside>
</div>
<!--mail inbox end-->