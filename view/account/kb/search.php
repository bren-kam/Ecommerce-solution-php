<?php
/**
 * @package Grey Suit Retail
 * @page Search | Knowledge Base
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $search
 * @var KnowledgeBaseArticle[] $articles
 * @var KnowledgeBaseCategory[] $categories
 * @var KnowledgeBasePage[] $pages
 */
?>

<div id="content">
    <div id="kb-search">
        <form name="fKBSearch" action="/kb/search/">
            <img src="/images/kb/search.png" width="48" height="35">
            <input type="text" id="kbs" name="kbs" placeholder="<?php echo _('Enter a question or keyword to search'); ?>">
            <input type="submit" id="kbs-button" value="<?php echo _('Search'); ?>">
        </form>
    </div>
    <div id="subcontent-wrapper">
        <div id="breadcrumb">
            <a href="/kb/" title="<?php echo _('Home'); ?>"><img src="/images/kb/icons/home.png" width="14" height="12" alt="<?php echo _('Home'); ?>"></a> >
            <span class="last"><?php echo _('Search'); ?></span>
        </div>
        <div id="subcontent">
            <h1><?php echo _('Search') . ': ' . $search; ?></h1>
            <br />
            <?php if ( empty( $categories ) && empty( $pages ) && empty( $articles ) ) { ?>
                <p><?php echo _("We weren't able to find anything under your search criteria. Please try broadening your search"); ?></p>
            <?php
            }

            if ( !empty( $categories ) ) {
                $hr_needed = true;
                ?>
                <hr />
                <br />
                <section>
                    <h2><?php echo _('Categories'); ?></h2>
                    <ul>
                        <?php foreach ( $categories as $category ) { ?>
                            <li><a href="<?php echo url::add_query_arg( 'cid', $category->id, '/kb/category/' ); ?>" title="<?php echo $category->name; ?>"><?php echo $category->name; ?></a></li>
                        <?php } ?>
                    </ul>
                </section>
                <br />
            <?php
            }

            if ( !empty( $pages ) ) {
                if ( $hr_needed )
                    echo '<hr />';

                $hr_needed = true;
                ?>
                <br />
                <section>
                    <h2><?php echo _('Pages'); ?></h2>
                    <ul>
                        <?php foreach ( $pages as $page ) { ?>
                            <li><a href="<?php echo url::add_query_arg( 'pid', $page->id, '/kb/page/' ); ?>" title="<?php echo $page->name; ?>"><?php echo $page->name; ?></a></li>
                        <?php } ?>
                    </ul>
                </section>
                <br />
            <?php
            }

            if ( !empty( $articles ) ) {
                if ( $hr_needed )
                    echo '<hr />';
                ?>
                <br />
                <section id="articles">
                    <h2><?php echo _('Articles'); ?></h2>
                    <?php foreach ( $articles as $article ) { ?>
                        <section>
                            <h3><a href="<?php echo url::add_query_arg( 'aid', $article->id, '/kb/article/' ); ?>" title="<?php echo $article->title; ?>"><?php echo $article->title; ?></a></h3>
                            <p><?php echo format::limit_words( strip_tags( $article->content ) ); ?></p>
                            <p class="text-right"><a href="<?php echo url::add_query_arg( 'aid', $article->id, '/kb/article/' ); ?>" title="<?php echo $article->title; ?>"><?php echo _('Read more'); ?>...</a></p>
                        </section>
                    <?php } ?>
                </section>
            <?php
            }
            ?>
            <br><br>
            <hr>
            <br>
            <p><?php echo _("Can't find what you're looking for?"); ?> <a href="#" class="support-ticket" title="<?php echo _('Support Request'); ?>"><?php echo _('Submit a support request'); ?></a>. <?php echo _("We'll get back to you as soon as possible."); ?></p>
            <br class="clr" />
        </div>
    </div>
</div>

<?php echo $template->end(0); ?>