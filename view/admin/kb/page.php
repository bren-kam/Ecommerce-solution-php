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


<div class="row-fluid">
  <div class="col-lg-12">
      <ul class="breadcrumb">
          <li><a href="/kb/"><i class="fa fa-home"></i></a></li>

          <?php foreach ( $categories as $category ): ?>
              <li><a href="<?php echo url::add_query_arg( 'cid', $category->id, '/kb/category/' ); ?>" title="<?php echo $category->name; ?>"><?php echo $category->name; ?></a></li>
          <?php endforeach; ?>

          <li class="active"><?php echo $page->name; ?></li>
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
    <div class="col-lg-<?php echo ( count( $pages ) == 1 ) ? '12' : '9' ?>">
        <section class="panel">
            <header class="panel-heading">
                Articles on Page: <?php echo $page->name ?>
            </header>

            <div class="panel-body">
                <ul>
                    <?php foreach ( $articles as $article ): ?>
                        <li><a href="<?php echo url::add_query_arg( 'aid', $article->id, '/kb/article/' ); ?>" title="<?php echo $article->title; ?>"><?php echo $article->title; ?></a></li>
                    <?php endforeach; ?>
                </ul>

            </div>

        </section>
    </div>

    <?php if ( count( $pages ) > 1 ): ?>
        <div class="col-lg-3">
            <section class="panel">
                <header class="panel-heading">
                    Other Pages
                </header>

                <div class="panel-body">
                    <ul>
                        <?php
                            foreach ( $pages as $p ):
                                if ( $p->id == $page->id )
                                    continue;
                        ?>
                            <li><a href="<?php echo url::add_query_arg( 'pid', $p->id, '/kb/page/' ); ?>" title="<?php echo $p->name; ?>"><?php echo $p->name; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </section>
        </div>
    <?php endif; ?>
</div>