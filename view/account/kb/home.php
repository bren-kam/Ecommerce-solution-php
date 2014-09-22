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
 */

$links = array(
    100      => array( 'accounts', _('Accounts') )
    , 4    => array( 'products', _('Products') )
    , 107    => array( 'users', _('Users') )
    , 108    => array( 'checklists', 'Checklists' )
    , 109    => array( 'tickets', _('Tickets') )
    , 110   => array( 'reports', _('Reports') )
    , 111    => array( 'knowledge-base', _('Knowledge Base') )
    , 122   => array( 'faqs', _('Frequently Asked Questions') )
);

$rate_nonce = nonce::create('rate');
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <ul class="breadcrumb">
            <li><a href="/kb/"><i class="fa fa-home"></i></a></li>
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

    <div class="col-lg-6" id="kb-home-categories">
        <section class="panel">
            <header class="panel-heading">
                Browse By Category
            </header>

            <div class="panel-body">
                <?php foreach ( $links as $kb_category_id => $link ): ?>
                    <a href="<?php echo url::add_query_arg( 'cid', $kb_category_id, '/kb/category/' ); ?>" title="<?php echo $link[1]; ?>" class="service"><img src="/images/kb/dashboard/<?php echo str_replace( '_', '-', $link[0] ); ?>.png" width="149" height="160" alt="<?php echo $link[1]; ?>" /></a>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <div class="col-lg-3">
        <section class="panel">
            <header class="panel-heading">
                Most Frequently Viewed Articles
            </header>

            <div class="panel-body">
                <ol>
                    <?php foreach( $articles as $article ): ?>
                        <li><a href="<?php echo url::add_query_arg( 'aid', $article->id, '/kb/article/' ); ?>" title="<?php echo $article->title; ?>"><?php echo $article->title; ?></a> </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </section>
    </div>

    <div class="col-lg-3">
        <section class="panel">
            <header class="panel-heading">
                Having browser troubles?
            </header>

            <div class="panel-body">
                <p>
                    Check out our <a href="/kb/browser/" title="Browser Troubleshooting">browser troubleshooting</a> page.
                </p>
            </div>
        </section>
    </div>

</div>
