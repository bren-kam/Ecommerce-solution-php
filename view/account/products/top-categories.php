<?php
/**
 * @package Grey Suit Retail
 * @page categories | Products
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var Category[] $categories
 * @var array $category_images
 */
nonce::field( 'autocomplete', '_autocomplete' );
nonce::field( 'add_category', '_add_category' );
nonce::field( 'remove_category', '_remove_category' );
nonce::field( 'update_top_category_sequence', '_update_top_category_sequence' );
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Add Category
            </header>

            <div class="panel-body">

                <form class="form-inline" role="form">
                    <div class="form-group">
                        <select class="form-control" id="category">
                            <option value="">-- Add Category --</option>
                            <?php foreach ( $categories as $category ) { ?>
                                <option value="<?php echo $category->id; ?>" data-img="<?php echo ( !isset( $category_images[$category->id] ) || empty( $category_images[$category->id] ) ) ? 'http://placehold.it/200x200&text=' . $category->name : $category_images[$category->id]; ?>"><?php echo str_repeat( '&nbsp;', $category->depth * 5 ); echo $category->name; ?></option>
                            <?php } ?>
                        </select>
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
                Categories
            </header>

            <div class="panel-body">

                <div id="category-list">

                    <?php if ( $top_categories ): ?>

                        <?php foreach ( $top_categories as $category ): ?>
                            <div class="category" data-category-id="<?php echo $category->id ?>">
                                <img src="<?php echo ( !isset( $category_images[$category->id] ) || empty( $category_images[$category->id] ) ) ? 'http://placehold.it/200x200&text=' . $category->name : $category_images[$category->id]; ?>" />
                                <h4><?php echo $category->name; ?></h4>
                                <p class="category-url"><a href="<?php echo $category->link; ?>" title="<?php echo $category->name; ?>" target="_blank" ><?php echo $category->link; ?></a></p>
                                <a href="javascript:;" class="remove"><i class="fa fa-trash-o"></i></a>
                            </div>
                        <?php endforeach; ?>

                    <?php else: ?>

                        <input type="hidden" id="action-create-categories" />

                        <?php foreach ( $default_top_categories as $category ): ?>
                            <div class="category" data-category-id="<?php echo $category->id ?>">
                                <img src="<?php echo ( !isset( $category_images[$category->id] ) || empty( $category_images[$category->id] ) ) ? 'http://placehold.it/200x200&text=' . $category->name : $category_images[$category->id]; ?>" />
                                <h4><?php echo $category->name; ?></h4>
                                <p class="category-url"><a href="<?php echo $category->link; ?>" title="<?php echo $category->name; ?>" target="_blank" ><?php echo $category->link; ?></a></p>
                                <a href="javascript:;" class="remove"><i class="fa fa-trash-o"></i></a>
                            </div>
                        <?php endforeach; ?>

                    <?php endif; ?>

                    <div class="category hidden" id="category-template">
                        <img />
                        <h4></h4>
                        <p class="category-url"><a target="_blank" ></a></p>
                        <a href="javascript:;" class="remove"><i class="fa fa-trash-o"></i></a>
                    </div>

                </div>

                <div class="clearfix"></div>

                <?php if ( $top_categories ): ?>
                    <p>
                        <strong>Default Categories:</strong>
                        <?php foreach( $default_top_categories as $category ): ?>
                            <?php echo $category->name ?>.
                        <?php endforeach; ?>
                    </p>
                <?php endif; ?>

            </div>
        </section>
    </div>
</div>
