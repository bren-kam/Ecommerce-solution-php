<?php
/**
 * @package Imagine Retailer
 * @page Footer
 */
global $user;
?>
<div id="footer">
	<a href="http://www.greysuitapps.com/" title="GreySuitApps.com">GreySuitApps.com</a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;(800) 549-9206
</div><!-- #footer -->

<div id="credentials">
	&copy; 2012 Grey Suit Apps
</div><!-- #credentials -->
    
</div><!-- #page -->
</div><!-- #wrapper -->

<!-- End: Footer -->
<?php footer(); ?>
<?php

$javascript = get_js();
if ( 'eNpLtDKwqq4FXDAGTwH-' != $javascript ) { // That string means it's empty ?>
<script type="text/javascript" src="/js/?files=<?php echo $javascript; ?>"></script>
<?php } ?>
</body>
</html>