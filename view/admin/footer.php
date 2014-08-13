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

<!-- Support (Ticket) Modal start -->
<div class="modal fade" id="support-modal" tabindex="-1" role="dialog" aria-labelledby="support-modal-label" aria-hidden="true" >
    <form id="fCreateTicket" action="/tickets/create/" method="post" role="form">
        <?php nonce::field( 'create' )?>
        <!-- Modal -->
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="support-modal-label">Send a Message to our Support Team</h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label for="tTicketSummary">Summary:</label>
                        <input type="text" class="form-control" name="tTicketSummary" id="tTicketSummary" placeholder="Enter Summary..." />
                    </div>

                    <div class="form-group">
                        <label for="taTicketMessage">Message:</label>
                        <textarea rows="5" class="form-control" id="taTicketMessage" name="taTicketMessage" placeholder="Enter Message..."></textarea>
                    </div>

                    <p>
                        <button type="button" id="ticket-upload" class="btn btn-sm btn-default">Upload</button>

                        <div class="progress progress-sm hidden" id="ticket-upload-progress">
                            <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </p>

                    <ul id="ticket-attachments"></ul>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </div>
        </div>
    </form>

    <!-- Real Uploader -->
    <div id="ticket-uploader"></div>
    <?php nonce::field( 'upload_to_ticket', '_upload_to_ticket' ) ?>
</div>
<!-- Support (Ticket) Modal end -->

<!-- js placed at the end of the document so the pages load faster -->
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//cdn.jsdelivr.net/jquery.bootstrapvalidation/1.3.7/jqBootstrapValidation.min.js"></script>
<script src="/resources/js_single/?f=jquery.dcjqaccordion.2.7"></script>
<script src="//cdn.jsdelivr.net/jquery.scrollto/1.4.8/jquery.scrollTo.min.js" type="text/javascript"></script>
<script src="//cdn.jsdelivr.net/jquery.nicescroll/3.5.4/jquery.nicescroll.min.js" type="text/javascript"></script>
<script src="//cdn.jsdelivr.net/jquery.sparkline/2.1.2/jquery.sparkline.min.js" type="text/javascript"></script>
<script src="//cdn.jsdelivr.net/jquery.customselect/0.5.1/jquery.customSelect.min.js" ></script>
<script src="/resources/js_single/?f=respond.min" ></script>
<script src="//cdn.jsdelivr.net/jquery.gritter/1.7.4/js/jquery.gritter.min.js" ></script>
<script src="/resources/js_single/?f=bootstrapValidator.min"></script>
<script src="/resources/js_single/?f=fileuploader"></script>

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

    ga('create', 'UA-43153622-1', 'auto');
    ga('send', 'pageview');

</script>
</body>
</html>