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

<div class="row-fluid">
  <div class="col-lg-12">
      <ul class="breadcrumb">
          <li><a href="/kb/"><i class="fa fa-home"></i></a></li>

          <?php foreach ( $parent_categories as $parent_cat ): ?>
              <li><a href="<?php echo url::add_query_arg( 'cid', $parent_cat->id, '/kb/category/' ); ?>" title="<?php echo $parent_cat->name; ?>"><?php echo $parent_cat->name; ?></a></li>
          <?php endforeach; ?>

          <li class="active"><?php echo $category->name; ?></li>
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
    <div class="col-lg-<?php echo ( count( $sibling_categories ) == 1 ) ? '12' : '9' ?>">

        <section class="panel">
            <header class="panel-heading">
                Pages on Category: <?php echo $page->name ?>
            </header>

            <div class="panel-body">
                <ul>
                    <?php foreach ( $pages as $page ): ?>
                        <li><a href="<?php echo url::add_query_arg( 'pid', $page->id, '/kb/page/' ); ?>" title="<?php echo $page->name; ?>"><?php echo $page->name; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>

        <section class="panel">
            <header class="panel-heading">
                Articles on Category: <?php echo $page->name ?>
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

    <?php if ( count( $sibling_categories ) > 1 ): ?>
        <div class="col-lg-3">
            <section class="panel">
                <header class="panel-heading">
                    Other Pages
                </header>

                <div class="panel-body">
                    <ul>
                        <?php
                            foreach ( $sibling_categories as $c ):
                                if ( $c->id == $category->id )
                                    continue;
                        ?>
                            <li><a href="<?php echo url::add_query_arg( 'cid', $c->id, '/kb/category/' ); ?>" title="<?php echo $c->name; ?>"><?php echo $c->name; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
        </div>
    <?php endif; ?>
</div>