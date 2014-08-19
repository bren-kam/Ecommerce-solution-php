<?php
/**
 * @package Grey Suit Retail
 * @page Brands | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Brand[] $top_brands
 */
nonce::field( 'autocomplete', '_autocomplete' );
nonce::field( 'add_brand', '_add_brand' );
nonce::field( 'remove_brand', '_remove_brand' );
nonce::field( 'update_brand_sequence', '_update_brand_sequence' );
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Add Brand
            </header>

            <div class="panel-body">

                <form class="form-inline" role="form">
                    <div class="form-group">
                        <input type="text" class="form-control" id="autocomplete" placeholder="Type brand name..." />
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="brand-link" value="1" <?php if ( $user->account->link_brands ) echo "checked"; ?>/>
                            Link to Brand Websites
                        </label>
                    </div>
                </form>

            </div>
        </section>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Brands
            </header>

            <div class="panel-body">

                <div id="top-categories">
                    <?php foreach ( $top_brands as $brand ): ?>
                        <div class="brand" data-brand-id="<?php echo $brand->id ?>">
                            <img src="<?php echo $brand->image; ?>" />
                            <h4><?php echo $brand->name; ?></h4>
                            <p class="brand-url"><a href="<?php echo $brand->link; ?>" title="<?php echo $brand->name; ?>" target="_blank" ><?php echo $brand->link; ?></a></p>
                            <a href="javascript:;" class="remove"><i class="fa fa-trash-o"></i></a>
                        </div>
                    <?php endforeach; ?>

                    <div class="brand hidden" id="brand-template">
                        <img />
                        <h4></h4>
                        <p class="brand-url"><a target="_blank" ></a></p>
                        <a href="javascript:;" class="remove"><i class="fa fa-trash-o"></i></a>
                    </div>
                </div>

            </div>
        </section>
    </div>
</div>
