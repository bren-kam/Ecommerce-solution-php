<?php
/**
 * @package Grey Suit Retail
 * @page Article | Knowledge Base
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var KnowledgeBaseCategory $category
 * @var KnowledgeBaseCategory[] $parent_categories
 * @var KnowledgeBaseCategory[] $child_categories
 * @var KnowledgeBaseCategory[] $sibling_categories
 * @var KnowledgeBaseArticle[] $articles
 * @var KnowledgeBasePage[] $pages
 */
?>

<div id="content">
    <div id="kb-search">
        <form name="fKBSearch" action="/kb/search/">
            <img src="/images/kb/search.png" width="48" height="35">
            <input type="text" id="kbs" name="kbs" tmpval="<?php echo _('Enter a question or keyword to search'); ?>">
            <input type="submit" id="kbs-button" value="<?php echo _('Search'); ?>">
        </form>
    </div>
    <div id="subcontent-wrapper">
        <div id="breadcrumb">
            <a href="/kb/" title="<?php echo _('Home'); ?>"><img src="/images/kb/icons/home.png" width="14" height="12" alt="<?php echo _('Home'); ?>"></a> >
            <?php
            $parent_category_count = count( $parent_categories );

            for ( $i = 0; $i < $parent_category_count; $i++ ) {
                // Set variables
                $parent_category = $parent_categories[$i];

                ?>
                <a href="<?php echo url::add_query_arg( 'cid', $parent_category->id, '/kb/category/' ); ?>" title="<?php echo $parent_category->name; ?>"><?php echo $parent_category->name; ?></a> >
            <?php } ?>

            <span class="last"><?php echo $category->name; ?></span>
        </div>
        <div id="subcontent">
            <h1><?php echo _('Category') . ': ' . $category->name; ?></h1>
            <br /><br />
            <?php if ( !empty( $pages ) ) { ?>
                <section>
                    <h2><?php echo _('Pages'); ?></h2>
                    <ul>
                    <?php
                    foreach ( $pages as $page ) {
                        ?>
                        <li><a href="<?php echo url::add_query_arg( 'pid', $page->id, '/kb/page/' ); ?>" title="<?php echo $page->name; ?>"><?php echo $page->name; ?></a></li>
                    <?php } ?>
                    </ul>
                </section>
                <br /><br />
            <?php } ?>
            <section>
                <h2><?php echo _('Articles'); ?></h2>
                <ul>
                <?php
                foreach ( $articles as $article ) {
                    ?>
                    <li><a href="<?php echo url::add_query_arg( 'aid', $article->id, '/kb/article/' ); ?>" title="<?php echo $article->title; ?>"><?php echo $article->title; ?></a></li>
                <?php } ?>
                </ul>
            </section>
            <br /><br />
            <br /><br />
            <br /><br />

            <?php if ( count( $sibling_categories ) > 1 ) { ?>
            <aside id="right-sidebar">
                <h2><?php echo _('Other Categories'); ?></h2>
                <?php
                foreach ( $sibling_categories as $sc ) {
                    if ( $sc->id == $category->id )
                        continue;
                    ?>
                    <a href="<?php echo url::add_query_arg( 'cid', $sc->id, '/kb/category/' ); ?>" title="<?php echo $sc->name; ?>"><?php echo $sc->name; ?></a>
                <?php } ?>
            </aside>
            <?php } ?>
            <br class="clr" />
        </div>
    </div>
</div>

<?php echo $template->end(0); ?>