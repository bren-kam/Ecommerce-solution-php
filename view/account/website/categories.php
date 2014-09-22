<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Categories
            </header>

            <div class="panel-body">

                <select id="sParentCategoryID" class="form-control">
                    <option value="0">--Parent Category (Top) --</option>
                    <?php foreach ( $categories as $category ): ?>
                        <option value="<?php echo $category->id; ?>"><?php echo str_repeat( '&nbsp;', $category->depth * 5 ); echo $category->name; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php nonce::field( 'store_session', '_store_session' ) ?>
                <div class="adv-table">
                    <table class="display table table-bordered table-striped" ajax="/website/list-categories/" perPage="30,50,100">
                        <thead>
                        <th sort="1">Title</th>
                        <th>Updated</th>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>