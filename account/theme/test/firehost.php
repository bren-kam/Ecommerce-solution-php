<?php
/**
 * @page Firehost Test
 * @package Imagine Retailer
 */


$title = 'Test | ' . TITLE;
get_header();
?>

<div id="content">
	<h1>FireHost Test</h1>
	<br clear="all" />
	<br /><br />
	<p>The link below will trigger an AJAX request to <a href="http://account2.imagineretailer.com/ajax/test/firehost/" target="_blank">http://account2.imagineretailer.com/ajax/test/firehost/</a>. Verify with Firebug.</p>
	<p>That AJAX request will show up with a blank response in in the Firebug response box as it is expecting a JSON result and there is a PHP Parse Error on that page. It will show the content length correctly (in the response headers). If you go to the link above in your browser, you will see the Parse Error.</p>
	<p><a href="/ajax/test/firehost/" ajax="1">Send AJAX Request</a></p>
	<p>The response on that page should contain the following:</p>
	<blockquote style="margin-left:40px">
<?php echo str_replace( '[br]', '<br />', htmlentities("<br />[br]
<b>Parse error</b>:  syntax error, unexpected ';' in <b>/home/develop4/public_html/account2/ajax/test/firehost.php</b> on line <b>7</b><br />") ); ?>
	</blockquote>
	<br clear="all" />
	
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
	<br /><br />
</div>

<?php get_footer(); ?>