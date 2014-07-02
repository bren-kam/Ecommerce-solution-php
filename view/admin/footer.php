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

</section>
</section>

<!--main content end-->
<!--footer start-->
<footer class="site-footer">
    <div class="text-center">
        <?php echo date("Y") ?> &copy; Grey Suit Retail
        <a href="#" class="go-top">
            <i class="fa fa-angle-up"></i>
        </a>
    </div>
</footer>
<!--footer end-->
</section>

<!-- js placed at the end of the document so the pages load faster -->
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//cdn.jsdelivr.net/jquery.bootstrapvalidation/1.3.7/jqBootstrapValidation.min.js"></script>
<script src="/resources/js_single/?f=jquery.dcjqaccordion.2.7"></script>
<script src="//cdn.jsdelivr.net/jquery.scrollto/1.4.8/jquery.scrollTo.min.js" type="text/javascript"></script>
<script src="//cdn.jsdelivr.net/jquery.nicescroll/3.5.4/jquery.nicescroll.min.js" type="text/javascript"></script>
<script src="//cdn.jsdelivr.net/jquery.sparkline/2.1.2/jquery.sparkline.min.js" type="text/javascript"></script>
<script src="//cdn.jsdelivr.net/jquery.customselect/0.5.1/jquery.customSelect.min.js" ></script>
<script src="/resources/js_single/?f=respond.min" ></script>

<script src="/resources/js_single/?f=common-scripts"></script>
<script src="/resources/js_single/?f=head.min" ></script>
<?php echo $resources->get_javascript_urls(); ?>

<script src="/resources/js_single/?f=global" ></script>
<script src="/resources/js/?f=<?php echo $resources->get_javascript_file(); ?>"></script>

<?php $template->get_footer(); ?>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-43152622-1', 'greysuitretail.com');
    ga('send', 'pageview');

</script>
</body>
</html>