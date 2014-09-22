<?php
/**
 * @package Grey Suit Retail
 * @page Catalog Dump | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $errs
 * @var string $js_validation
 */

nonce::field( 'autocomplete', '_autocomplete' );
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Catalog Dump
            </header>

            <div class="panel-body">

                <p>NOTE: This will add <em>every</em> item in a selected brand into your product catalog.</p>

                <form method="post" role="form">
                    <div class="form-group">
                        <label for="brand">Brand:</label>
                        <select name="hBrandID" class="form-control">
                            <option>-- Select a Brand --</option>
                            <?php foreach ( $brands as $brand ): ?>
                                <option value="<?php echo $brand->id ?>"><?php echo $brand->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <p>
                        <?php nonce::field( 'catalog_dump' ) ?>
                        <button type="submit" class="btn btn-primary">Dump Brand</button>
                    </p>
                </form>
            </div>

        </section>
    </div>
</div>