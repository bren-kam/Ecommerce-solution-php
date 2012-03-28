<?php
/**
 * @package Grey Suit Retail
 * @page Footer
 */
global $user;
?>
<div id="footer">
		<p>
			<?php if ( $user ) { ?>
			<a href="/" title="<?php echo _('Home'); ?>"><?php echo _('Home'); ?></a> | 
			<a href="/websites/" title="<?php echo _('Websites'); ?>"><?php echo _('Websites'); ?></a> | 
			<a href="/products/" title="<?php echo _('Product Catalog'); ?>"><?php echo _('Product Catalog'); ?></a> | 
			<a href="/users/" title="<?php echo _('Users'); ?>"><?php echo _('Users'); ?></a> | 
			<a href="/checklists/" title="<?php echo _('Checklists'); ?>"><?php echo _('Checklists'); ?></a> | 
			<a href="/help/" title="<?php echo _('Help'); ?>"><?php echo _('Help'); ?></a>
			<?php } ?>
		</p>
		<br /><br />
		<p id="copyright">&copy; <?php echo _('Copyright'), ' ', dt::date('Y'), '. ', _('All Rights Reserved'); ?>.</p>
	</div>
</div>
<div id="ticket"><a href="javascript:;" id="aTicket" title="<?php echo _('Support'); ?>"><img src="/images/trans.gif" width="26" height="100" alt="<?php echo _('Support'); ?>" /></a></div>
<div id="dTicketPopup" class="hidden" title="<?php echo _('Create Ticket'); ?>">
	<form action="/ajax/support/create-ticket/" id="fCreateTicket" method="post" enctype="application/x-www-form-urlencoded">
		<input type="text" class="tb" name="tTicketSummary" id="tTicketSummary" maxlength="140" style="width:360px" tmpval="<?php echo _('Enter summary'); ?>..." error="<?php echo _('You must enter in a summary'); ?>" />
		<br />
		<textarea name="taTicket" id="taTicket" rows="5" cols="50" style="width:360px" tmpval="<?php echo _('Enter message'); ?>..." error="<?php echo _('You must enter in a message'); ?>"></textarea>
		<br /><br />
		<input type="hidden" name="hTicketID" id="hTicketID" value="0" />
        <?php
        nonce::field( 'create-ticket', '_ajax_create_ticket' );

        // Only show checklists if the user is an online specialist and if hte website is not live
        if ( $user['role'] >= 7 && 1 != $user['website']['live'] ) {
            $c = new Checklists;
            $checklist_items = $c->get( $user['website']['website_id'] );
            ?>
            <!-- Checklist Section -->
            <h3 class="section">
                <?php echo _('Checklist'); ?>
                <a href="javascript:;" class="expander open" rel="dChecklist" title="<?php echo _('Click to Expand'); ?>"><img src="/images/trans.gif" width="27" height="19" alt="" /></a>
            </h3>
            <div id="dChecklist" class="hidden">
                <p><strong><?php echo $user['website']['title']; ?></strong></p>
                <p><?php echo _('Select the checklist items you want to mark as complete.'); ?></p>
                <select name="sChecklistItems[]" id="sChecklistItems" multiple="multiple" title="<?php echo _('Hint: Hit Ctrl + Click to select multiple items'); ?>">
                <?php
                if( is_array( $checklist_items ) )
                foreach ( $checklist_items as $section => $item_array ) {
                    $options = '';

                    if ( is_array( $item_array ) )
                    foreach( $item_array as $item ) {
                        // We don't want to see checked items
                        if ( 1 == $item['checked'] )
                            continue;

                        $options .= '<option value="' . $item['checklist_website_item_id'] . '">' . $item['name'] . '</option>';
                    }

                    if ( !empty( $options ) )
                        echo '<optgroup label="', $section, '">', $options, '</optgroup>';
                }
                ?>
                </select>
            </div>
            <br /><br />
        <?php } ?>

        <!-- Attachment Section -->
        <h3 class="section">
            <?php echo _('Attachment'); ?>
            <a href="javascript:;" class="expander open" rel="dTicketAttachment" title="<?php echo _('Click to Expand'); ?>"><img src="/images/trans.gif" width="27" height="19" alt="" /></a>
        </h3>
        <div id="dTicketAttachment" class="hidden">
            <p><input type="file" id="fTicketUpload" class="hidden" /></p>
            <div id="ticket-attachments"></div>
        </div>
    </form>
	<input type="hidden" id="hTicketWebsiteID" value="<?php echo $user['website']['website_id']; ?>" />
	<input type="hidden" id="hUserID" value="<?php echo $user['user_id']; ?>" />
	<?php nonce::field( 'ticket-upload', '_ajax_ticket_upload' ); ?>
    <div class="boxy-footer hidden">
        <p class="col-2 float-left"><a href="javascript:;" class="close"><?php echo _('Close'); ?></a></p>
        <p class="text-right col-2 float-right"><input type="submit" class="button" id="bCreateTicket" value="<?php echo _('Create Ticket'); ?>" /></p>
    </div>
</div>

<!-- End: Footer -->
<?php list( $javascript, $before_javascript, $callback ) = get_js( true ); ?>
<script type="text/javascript"><?php echo $before_javascript; ?>head.js( 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js', '/js/?files=<?php echo $javascript; ?>'<?php if ( !empty( $callback ) ) echo ', function() {', $callback, '}'; ?>);</script>
<?php footer(); ?>
</body>
</html>