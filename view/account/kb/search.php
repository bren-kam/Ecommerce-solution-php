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


<div class="row-fluid">
    <div class="col-lg-12">
        <ul class="breadcrumb">
            <li><a href="/kb/"><i class="fa fa-home"></i></a></li>

            <li class="active">Search</li>
        </ul>
    </div>
</div>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <div class="panel-body">

                <form class="form-inline" action="/kb/search" role="form">
                    <div class="form-group">
                        <input type="text" class="form-control" name="kbs" placeholder="Enter question or search..." />
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Search</button>
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
                Search Result for: <?php echo $search ?>
            </header>

            <div class="panel-body">

                <?php foreach( $categories as $category ): ?>
                    <div class="classic-search">
                        <h4><a href="<?php echo url::add_query_arg( 'cid', $category->id, '/kb/category/' ); ?>">Category: <?php echo $category->name ?></a></h4>
                    </div>
                <?php endforeach; ?>

                <?php foreach( $pages as $page ): ?>
                    <div class="classic-search">
                        <h4><a href="<?php echo url::add_query_arg( 'pid', $page->id, '/kb/page/' ); ?>">Page: <?php echo $page->name ?></a></h4>
                    </div>
                <?php endforeach; ?>

                <?php foreach( $articles as $article ): ?>
                    <div class="classic-search">
                        <h4><a href="<?php echo url::add_query_arg( 'aid', $article->id, '/kb/article/' ); ?>">Article: <?php echo $article->title ?></a></h4>
                        <p><?php echo format::limit_words( strip_tags( $article->content ) ); ?></p>
                    </div>
                <?php endforeach; ?>


                <p>Can't find what you're looking for? <a href="#" data-toggle="modal" data-target="#support-modal">Submit a support request</a>. We'll get back to you as soon as possible.</p>
            </div>

        </section>
    </div>

</div>