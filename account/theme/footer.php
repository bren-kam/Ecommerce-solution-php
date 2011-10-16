<?php
/**
 * @package Imagine Retailer
 * @page Footer
 */
global $user;
?>
<div id="footer">
		<p>
			<?php if( $user ) { ?>
			<a href="/" title="<?php echo _('Home'); ?>"><?php echo _('Home'); ?></a> | 
			<a href="/websites/" title="<?php echo _('Websites'); ?>"><?php echo _('Websites'); ?></a> | 
			<a href="/products/" title="<?php echo _('Product Catalog'); ?>"><?php echo _('Product Catalog'); ?></a> | 
			<a href="/users/" title="<?php echo _('Users'); ?>"><?php echo _('Users'); ?></a> | 
			<a href="/checklists/" title="<?php echo _('Checklists'); ?>"><?php echo _('Checklists'); ?></a> | 
			<a href="/help/" title="<?php echo _('Help'); ?>"><?php echo _('Help'); ?></a>
			<?php } ?>
		</p>
		<br /><br />
		<p id="copyright">&copy; <?php echo _('Copyright'), ' ', date_time::date('Y'), '. ', _('All Rights Reserved'); ?>.</p>
	</div>
</div>
<div id="ticket"><a href="javascript:;" id="aTicket" title="<?php echo _('Support'); ?>"><img src="/images/trans.gif" width="26" height="100" alt="<?php echo _('Support'); ?>" /></a></div>
<div id="dTicketPopup" class="hidden" title="<?php echo _('Create Ticket'); ?>">
	<form action="/ajax/support/create-ticket/" id="fCreateTicket" method="post" enctype="application/x-www-form-urlencoded">
		<input type="text" class="tb" name="tTicketSummary" id="tTicketSummary" maxlength="140" style="width:360px" tmpval="<?php echo _('Enter summary'); ?>..." error="<?php echo _('You must enter in a summary'); ?>" />
		<br />
		<textarea name="taTicket" id="taTicket" rows="5" cols="50" style="width:360px" tmpval="<?php echo _('Enter message'); ?>..." error="<?php echo _('You must enter in a message'); ?>"></textarea>
		<br /><br />
		<div id="ticket-attachments"></div>
		<input type="hidden" name="hTicketID" id="hTicketID" value="0" />
		<?php nonce::field( 'create-ticket', '_ajax_create_ticket' ); ?>
		<p class="col-2 float-right text-right"><input type="submit" class="button" value="<?php echo _('Create Ticket'); ?>" /></p>
	</form>
	<p class="col-2 float-left"><input type="file" id="fTicketUpload" class="hidden" /></p>
	<input type="hidden" id="hTicketWebsiteID" value="<?php echo $user['website']['website_id']; ?>" />
	<input type="hidden" id="hUserID" value="<?php echo $user['user_id']; ?>" />
	<?php nonce::field( 'ticket-upload', '_ajax_ticket_upload' ); ?>
</div>

<!-- End: Footer -->
<?php list( $javascript, $before_javascript, $callback ) = get_js( true ); ?>
<script type="text/javascript"><?php echo $before_javascript; ?>head.js( 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js', '/js/?files=<?php echo $javascript; ?>'<?php if( !empty( $callback ) ) echo ', function() {', $callback, '}'; ?>);</script>
<?php footer(); ?>
</body>
</html>