<?php
/**
 * @package Grey Suit Retail
 * @page Layout
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var AccountPage[] $pages
 * @var array $layout
 */

?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Home Page Layout
            </header>

            <div class="panel-body">

                <form method="post" role="form">
                    <div id="layout-list">

                        <?php
                            foreach ( $layout as $element ):
                                // Trending Items is only for New Template sites
                                if ( in_array( $element->name, array( 'popular-items', 'best-seller-items', 'last-viewed-items' ) ) && !$user->account->is_new_template() )
                                    continue;
                        ?>

                            <div class="layout <?php echo $element->disabled ? 'disabled' : '' ?>" data-attachment-id="<?php echo $element->id ?>">

                                <div class="layout-actions">
                                    <input type="checkbox" data-toggle="switch" value="active" <?php if ( !$element->disabled ) echo 'checked' ?>/>
                                </div>

                                <img src="http://placehold.it/350x50&text=<?php echo urlencode( str_replace( '-', ' ', ucwords( $element->name ) ) ); ?>">

                                <input type="hidden" name="layout[]" class="layout-value" value="<?php echo $element->name . '|' . $element->disabled; ?>">

                            </div>

                        <?php endforeach; ?>

                    </div>

                    <p>
                        <?php nonce::field( 'home_page_layout' ) ?>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </p>
                </form>

            </div>
        </section>
    </div>
</div>

