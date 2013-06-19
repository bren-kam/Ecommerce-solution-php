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
    <div id="subcontent-wrapper">
        <div id="breadcrumb">
            <span class="last"><?php echo _('Home'); ?></span>
        </div>
        <div id="subcontent">
            <?php
                $links = array(
                    'pages'				    => array( 'website', _('Website') )
                    , 4	    => array( 'products', _('Products') )
                    , 'live' 			    => array( 'analytics', _('Analytics') )
                    , 'blog'			    => array( 'blog', 'Blog' )
                    , 'email_marketing'	    => array( 'email-marketing', _('Email Marketing') )
                    , 'shopping_cart'	    => array( 'shopping-cart', _('Shopping Cart') )
                    , 'craigslist'		    => array( 'craigslist', _('Craigslist Ads') )
                    , 'social_media'	    => array( 'social-media', _('Social Media') )
                    , 'mobile_marketing'    => array( 'mobile-marketing', _('Mobile Marketing') )
                );

    $keys = array_keys( $links );

    foreach ( $links as $key => $link ) {
        ?>
        <a href="/<?php echo $link[0]; ?>/" title="<?php echo $link[1]; ?>" class="service"><img src="/images/dashboard/<?php echo str_replace( '_', '-', $selection ); ?>.png" width="149" height="160" alt="<?php echo $links[$k][1]; ?>" /></a>
    <?php } ?>
            <br class="clr" />
        </div>
    </div>
</div>

<?php echo $template->end(0); ?>