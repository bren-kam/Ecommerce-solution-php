<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                <h3>Route53 Replace</h3>
            </header>
            <div class="panel-body">
                <form method="post" role="form">
                    <div class="form-group">
                        <label for="domains">Domains (one per line):</label>
                        <textarea class="form-control" name="domains" id="domains" rows="10"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="replace-1">Replace <?php echo $replace_search_1 ?> with:</label>
                        <input type="text" class="form-control" name="replace-1" id="replace-1" />
                    </div>

                    <div class="form-group">
                        <label for="replace-2">Replace <?php echo $replace_search_2 ?> with:</label>
                        <input type="text" class="form-control" name="replace-2" id="replace-2" />
                    </div>

                    <p>
                        <?php nonce::field('route53_replace') ?>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </p>
                </form>
            </div>
        </section>
    </div>
</div>