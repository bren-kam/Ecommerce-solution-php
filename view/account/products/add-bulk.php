<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Add Bulk
            </header>

            <div class="panel-body">

                <?php if ( $success ): ?>
                    <?php if ( $already_existed > 0 ): ?>
                        <p><?php echo number_format( $already_existed ) ?> SKU(s) were already on your website</p>
                    <?php endif; ?>

                    <?php if ( count( $not_added_skus ) > 0 ): ?>
                        <p>The following SKU(s) were not added for one of the following reasons:</p>

                        <ol>
                            <li>The SKU is not a valid SKU or does not match the SKU in our master catalog.</li>
                            <li>The SKUs are for industries not associated with this account.</li>
                            <li>There is no image associated with the SKU.</li>
                        </ol>


                        <blockquote>
                            <?php echo implode( '<br />', $not_added_skus ); ?>
                        </blockquote>

                    <?php endif; ?>
                <?php endif; ?>

                <?php echo $form ?>
            </div>
        </section>
    </div>
</div>