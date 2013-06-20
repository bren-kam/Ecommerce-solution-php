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
    <div id="kb-search">
        <form name="fKBSearch" action="/kb/search/">
            <img src="/images/kb/search.png" width="48" height="35">
            <input type="text" id="kbs" name="kbs" tmpval="<?php echo _('Enter a question or keyword to search'); ?>">
            <select name="cid" id="sKBCategory">
                <option value="">-- <?php echo _('All'); ?> --</option>
                <?php foreach ( $search_categories as $scat ) { ?>
                    <option value="<?php echo $scat->id; ?>"<?php echo $selected; ?>><?php echo str_repeat( '&nbsp;', $scat->depth * 5 ) . $scat->name; ?></option>
                <?php } ?>
            </select>
            <input type="submit" id="kbs-button" value="<?php echo _('Search'); ?>">
        </form>
    </div>
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