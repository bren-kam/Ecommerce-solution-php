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

<div class="row-fluid">
  <div class="col-lg-12">
      <ul class="breadcrumb">
          <li><a href="/kb/"><i class="fa fa-home"></i></a></li>

          <?php foreach ( $categories as $category ): ?>
              <li><a href="<?php echo url::add_query_arg( 'cid', $category->id, '/kb/category/' ); ?>" title="<?php echo $category->name; ?>"><?php echo $category->name; ?></a></li>
          <?php endforeach; ?>

          <?php if ( $page->id ) { ?>
              <li><a href="<?php echo url::add_query_arg( 'pid', $page->id, '/kb/page/' ); ?>" title="<?php echo $page->name; ?>"><?php echo $page->name; ?></a></li>
          <?php } ?>

          <li class="active"><?php echo $article->title; ?></li>
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
    <div class="col-lg-<?php echo ( count( $articles ) == 1 ) ? '12' : '9' ?>">
        <section class="panel">
            <header class="panel-heading">
                <?php echo $article->title; ?>
            </header>

            <div class="panel-body">
                <?php echo $article->content; ?>
            </div>

            <div class="clearfix" id="helpful">
                <div class="alert alert-info pull-left">
                    <p>Was this article helpful?</p>
                    <a href="<?php echo url::add_query_arg( array( '_nonce' => $rate_nonce, 'aid' => $article->id, 'r' => KnowledgeBaseArticleRating::POSITIVE ), '/kb/rate/' ); ?>" class="btn btn-xs btn-success rate">Yes</a>
                    <a href="<?php echo url::add_query_arg( array( '_nonce' => $rate_nonce, 'aid' => $article->id, 'r' => KnowledgeBaseArticleRating::NEGATIVE ), '/kb/rate/' ); ?>" class="btn btn-xs btn-danger rate">No</a>
                </div>
            </div>
        </section>
    </div>

    <?php if ( count( $articles ) > 1 ): ?>
        <div class="col-lg-3">
            <section class="panel">
                <header class="panel-heading">
                    Other Articles
                </header>

                <div class="panel-body">
                    <ul>
                        <?php
                            foreach ( $articles as $art ):
                                if ( $art->id == $article->id )
                                    continue;
                        ?>
                            <li><a href="<?php echo url::add_query_arg( 'aid', $art->id, '/kb/article/' ); ?>" title="<?php echo $art->title; ?>"><?php echo $art->title; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </section>
        </div>
    <?php endif; ?>
</div>