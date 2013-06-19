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
 * @var KnowledgeBaseCategory[] $categories
 * @var KnowledgeBaseCategory $category
 * @var KnowledgeBasePage $page
 */

$rate_nonce = nonce::create('rate');
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

            <a href="<?php echo url::add_query_arg( 'pid', $page->id, '/kb/page/' ); ?>" title="<?php echo $page->name; ?>"><?php echo $page->name; ?></a> >

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
            <aside>
                <header><?php echo _('Other Articles'); ?></header>
            </aside>
            <br class="clr" />
        </div>
    </div>
</div>

<?php echo $template->end(0); ?>