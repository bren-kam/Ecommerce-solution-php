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
    <div id="subcontent-wrapper">
        <div id="breadcrumb">
            <span class="last"><?php echo _('Home'); ?></span>
        </div>
        <div id="subcontent">
            <div class="col-2 float-left divider">
                <?php
                $links = array(
                    14      => array( 'website', _('Website') )
                    , 15    => array( 'products', _('Products') )
                    , 16    => array( 'analytics', _('Analytics') )
                    , 18    => array( 'blog', 'Blog' )
                    , 20    => array( 'email-marketing', _('Email Marketing') )
                    , 123   => array( 'shopping-cart', _('Shopping Cart') )
                    , 22    => array( 'social-media', _('Social Media') )
                    , 98    => array( 'mobile-marketing', _('Mobile Marketing') )
                );

                $keys = array_keys( $links );

                foreach ( $links as $kb_category_id => $link ) {
                    ?>
                    <a href="<?php echo url::add_query_arg( 'cid', $kb_category_id, '/kb/category/' ); ?>" title="<?php echo $link[1]; ?>" class="service"><img src="/images/dashboard/<?php echo str_replace( '_', '-', $link[0] ); ?>.png" width="149" height="160" alt="<?php echo $link[1]; ?>" /></a>
                <?php } ?>
            </div>
            <div class="col-2 float-left" id="frequently">
                <p><strong><?php echo _('Most Frequently Viewed Articles'); ?></strong></p>
                <ol>
                <?php foreach( $articles as $article ) { ?>
                    <li><a href="<?php echo url::add_query_arg( 'aid', $article->id, '/kb/article/' ); ?>" title="<?php echo $article->title; ?>"><?php echo $article->title; ?></a> </li>
                <?php } ?>
                </ol>
            </div>
            <br class="clr" />
        </div>
    </div>
</div>

<?php echo $template->end(0); ?>