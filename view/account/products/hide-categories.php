<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                Hide Categories
            </header>
            <div class="panel-body">
                <?php echo $form ?>
            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">

            <header class="panel-heading">
                Hidden Categories
            </header>
            <div class="panel-body">

                <form action="/products/unhide-categories/" method="post" role="form">
                    <?php if ( $hidden_categories ): ?>
                        <?php
                            foreach ( $hidden_categories as $category ):
                                $parent_categories = $category->get_all_parents( $category->id );
                                $name = $category->name;
                                foreach ( $parent_categories as $pc ):
                                    $name = $pc->name . ' &gt; ' . $name;
                                endforeach;
                        ?>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="unhide-categories[]" value="<?php echo $category->id; ?>" />
                                    <?php echo $name ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <p>
                        <?php nonce::field( 'unhide_categories' ) ?>
                        <button type="submit" class="btn btn-primary">Unblock Products</button>
                    </p>
                </form>

            </div>
        </section>
    </div>
</div>