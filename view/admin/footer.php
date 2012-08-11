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
			<?php if ( $user && $user->id ) { ?>
                <a href="/accounts/" title="<?php echo _('Accounts'); ?>"><?php echo _('Accounts'); ?></a> |
                <a href="/products/" title="<?php echo _('Products'); ?>"><?php echo _('Products'); ?></a> |
                <a href="/users/" title="<?php echo _('Users'); ?>"><?php echo _('Users'); ?></a> |
                <a href="/checklists/" title="<?php echo _('Checklists'); ?>"><?php echo _('Checklists'); ?></a> |
                <?php if ( $user->has_permission(8) ) { ?>
                    <a href="/reports/" title="<?php echo _('Reports'); ?>"><?php echo _('Reports'); ?></a> |
                <?php } ?>
                <a href="/support/" title="<?php echo _('Support'); ?>"><?php echo _('Support'); ?></a>
			<?php } ?>
		</p>
	</div>
</div>
<div id="dTicketPopup" class="hidden" title="<?php echo _('Create Ticket'); ?>">
	<form action="/tickets/create/" id="fCreateTicket" method="post">
		<input type="text" class="tb" name="tTicketSummary" id="tTicketSummary" maxlength="140" tmpval="<?php echo _('Enter summary'); ?>..." error="<?php echo _('You must enter in a summary'); ?>" />
		<br />
		<textarea name="taTicket" id="taTicket" rows="5" cols="50" tmpval="<?php echo _('Enter message'); ?>..." error="<?php echo _('You must enter in a message'); ?>"></textarea>
		<br /><br />
		<input type="hidden" name="hTicketID" id="hTicketID" value="0" />
		<?php nonce::field( 'create', '_ajax_create_ticket' ); ?>
	</form>
	<input type="hidden" id="hTicketWebsiteID" value="<?php if ( isset( $user->website ) ) echo $user->website->website_id; ?>" />
	<input type="hidden" id="hUserID" value="<?php echo $user->id; ?>" />
	<div class="boxy-footer hidden">
        <p class="col-2 float-left"><a href="javascript:;" class="close"><?php echo _('Cancel'); ?></a></p>
        <p class="text-right col-2 float-right"><input type="submit" class="button" id="bCreateTicket" value="<?php echo _('Create Ticket'); ?>" /></p>
    </div>
</div>

<!-- End: Footer -->
<script type="text/javascript">head.js( 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js', '/resources/js/?f=<?php echo $resources->get_javascript_file(); ?>');</script>
<?php $template->get_footer(); ?>
</body>
</html>