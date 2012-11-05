<?php
/**
 * @package Grey Suit Retail
 * @page Errors > 404
 *
 * Declare the variables we have available from other sources
 * @var Template $template
 */

echo $template->start( _('404 not found'), NULL );
?>
<p><?php echo _("Welcome to a 404 page. You are here because the page you have requested doesn't exist, at least not at this location."); ?></p>
<br clear="all" />
<br /><br />
<br /><br />

<?php echo $template->end( 2 ); ?>