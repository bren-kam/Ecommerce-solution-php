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

$rate_nonce = nonce::create('rate');
?>

<div id="content">
    <div id="kb-search">
        <form name="fKBSearch" action="/kb/search/">
            <img src="/images/kb/search.png" width="48" height="35">
            <input type="text" id="kbs" name="kbs" tmpval="<?php echo _('Enter a question or keyword to search'); ?>">
            <input type="submit" id="kbs-button" value="<?php echo _('Search'); ?>">
        </form>
    </div>
    <div id="subcontent-wrapper">
        <div id="breadcrumb">
            <span class="last"><img src="/images/kb/icons/home.png" width="14" height="12" alt="<?php echo _('Home'); ?>"></span>
        </div>
        <div id="subcontent">
            <div class="col-2 float-left divider">
                <?php
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

                $keys = array_keys( $links );

                foreach ( $links as $kb_category_id => $link ) {
                    ?>
                    <a href="<?php echo url::add_query_arg( 'cid', $kb_category_id, '/kb/category/' ); ?>" title="<?php echo $link[1]; ?>" class="service"><img src="/images/kb/dashboard/<?php echo str_replace( '_', '-', $link[0] ); ?>.png" width="149" height="160" alt="<?php echo $link[1]; ?>" /></a>
                <?php } ?>
            </div>
            <div class="col-4 float-left divider" id="frequently">
                <p><strong><?php echo _('Most Frequently Viewed Articles'); ?></strong></p>
                <ol>
                <?php foreach( $articles as $article ) { ?>
                    <li><a href="<?php echo url::add_query_arg( 'aid', $article->id, '/kb/article/' ); ?>" title="<?php echo $article->title; ?>"><?php echo $article->title; ?></a> </li>
                <?php } ?>
                </ol>
            </div>
            <div class="col-4 float-left" id="browser-troubles">
                <p><strong><?php echo _('Having browser troubles?'); ?></strong></p>
                <p><?php echo _('Check out our'); ?> <a href="/kb/browser/" title="<?php echo _('Browser Troubleshooting'); ?>"><?php echo _('browser troubleshooting'); ?></a> <?php echo _('page'); ?>.</p>
            </div>
            <br class="clr" />
        </div>
    </div>
</div>

<?php echo $template->end(0); ?>