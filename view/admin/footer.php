<?php
/**
 * @package Grey Suit Retail
 * @page Footer
 *
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */
?>
<div id="footer">
    <p id="copyright">&copy; <?php echo _('Copyright'); ?> <?php echo date('Y'); ?>. <?php echo _('All Rights Reserved'); ?>.</p>
    <p>
        <a href="/accounts/" title="<?php echo _('Accounts'); ?>"><?php echo _('Accounts'); ?></a> |
        <?php if ( $user->has_permission( User::ROLE_ONLINE_SPECIALIST ) ) { ?>
            <a href="/products/" title="<?php echo _('Products'); ?>"><?php echo _('Products'); ?></a> |
            <a href="/users/" title="<?php echo _('Users'); ?>"><?php echo _('Users'); ?></a> |
            <a href="/checklists/" title="<?php echo _('Checklists'); ?>"><?php echo _('Checklists'); ?></a> |
        <?php
        }
        if ( $user->has_permission( User::ROLE_ADMIN ) ) { ?>
            <a href="/reports/" title="<?php echo _('Reports'); ?>"><?php echo _('Reports'); ?></a> |
        <?php } ?>
        <a href="/help/" title="<?php echo _('Support'); ?>"><?php echo _('Support'); ?></a>
    </p>
</div>
<div id="dTicketPopup" class="hidden" title="<?php echo _('Create Ticket'); ?>">
	<form action="/tickets/create/" id="fCreateTicket" method="post">
		<input type="text" class="tb" name="tTicketSummary" id="tTicketSummary" maxlength="140" tmpval="<?php echo _('Enter summary'); ?>..." error="<?php echo _('You must enter in a summary'); ?>" />
		<br />
		<textarea name="taTicketMessage" id="taTicketMessage" rows="5" cols="50" tmpval="<?php echo _('Enter message'); ?>..." error="<?php echo _('You must enter in a message'); ?>"></textarea>
		<br /><br />
        <a href="#" id="aUploadTicketAttachment" title="<?php echo _('Add Attachment'); ?>"><?php echo _('Add Attachment'); ?></a>
        <div class="hidden-fix position-absolute" id="upload-ticket-attachment"></div>
        <div id="ticket-attachments-list"></div>

        <input type="hidden" name="hSupportTicketId" id="hSupportTicketId" value="" />
		<?php nonce::field( 'create' ); ?>
	</form>

	<div class="boxy-footer hidden">
        <p class="col-2 float-left"><a href="#" class="close"><?php echo _('Cancel'); ?></a></p>
        <p class="text-right col-2 float-right"><input type="submit" class="button" id="bCreateTicket" value="<?php echo _('Create Ticket'); ?>" /></p>
    </div>
    <?php nonce::field( 'upload_to_ticket', '_upload_to_ticket' ); ?>
</div>

<!-- End: Footer -->
<script type="text/javascript">head.js( '/resources/js/?f=<?php echo $resources->get_javascript_file(); ?>');</script>
<?php $template->get_footer(); ?>
</body>
</html>