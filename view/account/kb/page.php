<?php
/**
 * @package Grey Suit Retail
 * @page Article | Knowledge Base
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var KnowledgeBaseArticle[] $articles
 * @var KnowledgeBaseCategory[] $categories
 * @var KnowledgeBaseCategory $category
 * @var KnowledgeBasePage $page
 */
?>

<div id="content">
    <div id="subcontent-wrapper">
        <div id="breadcrumb">
            <a href="/kb/" title="<?php echo _('Home'); ?>"><?php echo _('Home'); ?></a> >
            <?php
            $category_count = count( $categories );

            for ( $i = 0; $i < $category_count; $i++ ) {
                // Set variables
                $category = $categories[$i];

                ?>
                <a href="<?php echo url::add_query_arg( 'cid', $category->id, '/kb/category/' ); ?>" title="<?php echo $category->name; ?>"><?php echo $category->name; ?></a> >
            <?php } ?>

            <span class="last"><?php echo $page->name; ?></span>
        </div>
        <div id="subcontent">
            <h1><?php echo _('Page') . ': ' . $page->name; ?></h1>
            <br /><br />
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

            <?php if ( count( $pages ) > 1 ) { ?>
            <aside id="right-sidebar">
                <header><?php echo _('Other Pages'); ?></header>
                <?php
                foreach ( $pages as $p ) {
                    if ( $p->id == $page->id )
                        continue;
                    ?>
                    <a href="<?php echo url::add_query_arg( 'pid', $p->id, '/kb/page/' ); ?>" title="<?php echo $p->name; ?>"><?php echo $p->name; ?></a>
                <?php } ?>
            </aside>
            <?php } ?>
            <br class="clr" />
        </div>
    </div>
</div>

<?php echo $template->end(0); ?>