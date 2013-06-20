<?php
/**
 * @package Grey Suit Retail
 * @page Article | Knowledge Base
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var KnowledgeBaseArticle $article
 * @var KnowledgeBaseArticle[] $articles
 * @var KnowledgeBaseCategory[] $categories
 * @var KnowledgeBaseCategory $category
 * @var KnowledgeBasePage $page
 */

$rate_nonce = nonce::create('rate');
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

            <?php if ( $page->id ) { ?>
                <a href="<?php echo url::add_query_arg( 'pid', $page->id, '/kb/page/' ); ?>" title="<?php echo $page->name; ?>"><?php echo $page->name; ?></a> >
            <?php } ?>

            <span class="last"><?php echo $article->title; ?></span>
        </div>
        <div id="subcontent">
            <h1><?php echo $article->title; ?></h1>
            <br /><br />
            <section>
                <?php echo $article->content; ?>
            </section>
            <br /><br />
            <br /><br />
            <br /><br />
            <div id="helpful">
                <p><?php echo _('Was this article helpful?'); ?></p>
                <p>
                    <a href="<?php echo url::add_query_arg( array( '_nonce' => $rate_nonce, 'aid' => $article->id, 'r' => KnowledgeBaseArticleRating::POSITIVE ), '/kb/rate/' ); ?>" title="<?php echo _('Yes'); ?>" ajax="1"><?php echo _('Yes'); ?></a>
                    <?php echo _('or'); ?>
                    <a href="<?php echo url::add_query_arg( array( '_nonce' => $rate_nonce, 'aid' => $article->id, 'r' => KnowledgeBaseArticleRating::NEGATIVE ), '/kb/rate/' ); ?>" title="<?php echo _('No'); ?>" ajax="1"><?php echo _('No'); ?></a>
                </p>
                <p id="thanks" class="hidden"><?php echo _('Thank you for your feedback!'); ?></p>
                <div id="helpful-bar"></div>
                <div id="helpful-fade"></div>
            </div>
            <?php if ( count( $articles ) > 1 ) { ?>
            <aside id="right-sidebar">
                <header><?php echo _('Other Articles'); ?></header>
                <?php
                foreach ( $articles as $art ) {
                    if ( $art->id == $article->id )
                        continue;
                    ?>
                    <a href="<?php echo url::add_query_arg( 'aid', $art->id, '/kb/article/' ); ?>" title="<?php echo $art->title; ?>"><?php echo $art->title; ?></a>
                <?php } ?>
            </aside>
            <?php } ?>
            <br class="clr" />
        </div>
    </div>
</div>

<?php echo $template->end(0); ?>