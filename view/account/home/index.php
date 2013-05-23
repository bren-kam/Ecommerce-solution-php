<?php
/**
 * @package Grey Suit Retail
 * @page Home
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $advertising_url
 */

echo $template->start( '', false );

$links = array(
    'pages'				    => array( 'website', _('Website') )
    , 'product_catalog'	    => array( 'products', _('Products') )
    , 'live' 			    => array( 'analytics', _('Analytics') )
    , 'blog'			    => array( '', 'Blog' )
    , 'email_marketing'	    => array( 'email-marketing', _('Email Marketing') )
    , 'shopping_cart'	    => array( 'shopping-cart/users', _('Shopping Cart') )
    , 'craigslist'		    => array( 'craigslist', _('Craigslist Ads') )
    , 'social_media'	    => array( 'social-media', _('Social Media') )
    , 'mobile_marketing'    => array( 'mobile-marketing', _('Mobile Marketing') )
);

$exceptions = array(
    'shopping-cart/users' => 'shopping-cart'
);

$keys = array_keys( $links );

foreach ( $links as $key => $link ) {
    if ( 'email_marketing' != $key && !$user->account->$key )
        continue;

    switch ( $key ) {
        case 'blog':
        ?>
        <form action="http://<?php echo $user->account->domain; ?>/blog/wp-login.php" target="_blank" method="post" id="fBlogForm" class="hidden">
            <input type="hidden" name="log" value="<?php echo security::decrypt( base64_decode( $user->account->wordpress_username ), ENCRYPTION_KEY ); ?>" />
            <input type="hidden" name="pwd" value="<?php echo security::decrypt( base64_decode( $user->account->wordpress_password ), ENCRYPTION_KEY ); ?>" />
        </form>
        <a href="javascript:document.getElementById('fBlogForm').submit();" title="<?php echo $link[1]; ?>" class="service"><img src="/images/dashboard/blog.png" width="149" height="160" alt="<?php echo _('Blog'); ?>" /></a>
        <?php
        break;

        default:
            $selection = ( isset( $exceptions[$link[0]] ) ) ? $exceptions[$link[0]] : $link[0];
            ?>
            <a href="/<?php echo $link[0]; ?>/" title="<?php echo $link[1]; ?>" class="service"><img src="/images/dashboard/<?php echo str_replace( '_', '-', $selection ); ?>.png" width="149" height="160" alt="<?php echo $links[$k][1]; ?>" /></a>
            <?php
        break;
    }
}
    if ( !empty( $advertising_url ) ) {
    ?>
        <a href="<?php echo $advertising_url; ?>" title="<?php echo _('Advertising Portal'); ?>" target="_blank" class="service"><img src="/images/dashboard/ads.png" width="149" height="160" alt="<?php echo _('Advertising Portal'); ?>" /></a>
        <?php
    }

echo $template->end();
?>