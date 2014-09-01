<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                <?php echo $page->id ? 'Edit' : 'Add' ?> Facebook Page
            </header>
            <div class="panel-body">
                <?php
                    if ( $has_permission ): echo $form; else: ?>
                        <div class="alert alert-warning">
                            You have reached your maximum amount of facebook pages, please see your online specialist about getting more.
                        </div>
                    <?php endif; ?>
            </div>
        </section>
    </div>
</div>