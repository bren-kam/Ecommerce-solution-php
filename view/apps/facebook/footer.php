<?php
/**
 * @package Grey Suit Retail
 * @page Footer
 * 
 * @var Template $template
 * @var User $use
 * @var int $app_idr
 */
?>
<div id="footer">
	<a href="http://www.greysuitapps.com/" title="GreySuitApps.com">GreySuitApps.com</a>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;(800) 549-9206
</div><!-- #footer -->

<div id="credentials">
	&copy; <?php echo date('Y'); ?> Grey Suit Apps
</div><!-- #credentials -->
    
</div><!-- #page -->
</div><!-- #wrapper -->

<!-- End: Footer -->
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({appId: "<?php echo $app_id; ?>", status: true, cookie: true,
             xfbml: true});
	FB.setSize({ width: 720, height: 500 });
  };
  (function() {
    var e = document.createElement("script"); e.async = true;
    e.src = document.location.protocol +
      "//connect.facebook.net/en_US/all.js";
    document.getElementById("fb-root").appendChild(e);
  }());
</script?
>
<script type="text/javascript">head.load( 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js', '/resources/js/?f=<?php echo $resources->get_javascript_file(); ?>');</script>
<?php $template->get_footer(); ?>
</body>
</html>