<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                HTML &lt;Head&gt;
                <a href="/website/settings/" class="pull-right btn btn-default btn-sm">Edit Settings</a>
                <a href="/website/custom-404/" class="pull-right btn btn-default btn-sm">Edit 404 Page</a>
            </header>
            <div class="panel-body">
                <form method="post" role="form">
                    <div class="form-group">
                        <label for="text">HTML Code:</label>
                        <textarea class="form-control" id="text" name="html-header" rows="10"><?php echo $html_header ?></textarea>
                    </div>

                    <p>
                        <?php nonce::field('html_head') ?>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </p>
                </form>
            </div>
        </section>
    </div>
</div>