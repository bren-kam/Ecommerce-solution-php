<?php
    nonce::field( 'send_test', '_send_test');
    nonce::field( 'save_draft', '_save_draft');
    nonce::field( 'save_campaign', '_save_campaign');
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body">

                <div class="email-layout" id="email-preview"></div>

                <p>
                    <a href="javascript:;" data-step="2" class="btn btn-default">&lt; Back</a>
                    <?php if ( $campaign->status < EmailMessage::STATUS_SCHEDULED ) {  ?>
                        <a href="javascript:;" class="btn btn-default save-draft">Save Draft</a>
                    <?php } ?>
                    <a href="#" class="btn btn-default" data-toggle="modal" data-target="#send-test-modal">Send a Test Campaign</a>
                    <?php if ( $campaign->status < EmailMessage::STATUS_SCHEDULED ) {  ?>
                        <a href="javascript:;" class="btn btn-primary save-campaign">Looks Good! Send it Out.</a>
                    <?php } ?>
                </p>

            </div>
        </section>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="send-test-modal" tabindex="-1" role="dialog" aria-labelledby="send-test-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="send-test-modal-label">Send a Test Campaign</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="test-destination">Email Address:</label>
                    <input type="text" id="test-destination" class="form-control" placeholder="Email to send Campaign Preview" />
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary send-test" data-dismiss="modal">Send Preview</button>
            </div>
        </div>
    </div>
</div>
