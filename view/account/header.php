<?php
/**
 * @package Grey Suit Retail
 * @page Header
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */

$resources->css_before( 'labels/' . DOMAIN, 'redactor', 'style' );
$resources->javascript( 'sparrow', 'jquery.notify', 'header' );
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" lang="en-US">
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" lang="en-US">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html lang="en-US">
<!--<![endif]-->
<head>
    <meta charset="UTF-8" />
    <title><?php echo $template->v('title') . ' | ' . TITLE; ?></title>
    <link type="text/css" rel="stylesheet" href="/resources/css/?f=<?php echo $resources->get_css_file(); ?>" />
    <?php echo $resources->get_css_urls(); ?>
    <script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/headjs/0.99/head.min.js"></script>
    <link rel="icon" href="<?php echo '/images/favicons/' . DOMAIN . '.ico'; ?>" type="image/x-icon" />
    <?php $template->get_head(); ?>
</head>
<body>
<?php $template->get_top(); ?>
<div id="wrapper">
	<div id="header">
		<?php $margin = floor( ( 82 - LOGO_HEIGHT ) / 2 ); ?>
		<div id="logo"><img src="/images/logos/<?php echo DOMAIN; ?>.png" width="<?php echo LOGO_WIDTH; ?>" height="<?php echo LOGO_HEIGHT; ?>" alt="<?php echo TITLE, ' ', _('Logo'); ?>" style="margin: <?php echo $margin; ?>px 0" /></div>

        <div id="log-out">
        <?php if ( $user && $user->id ) { ?>
		    <p><?php echo _('Welcome'), ' ', $user->contact_name; ?> | <a href="/logout/" title="<?php echo _('Log Out'); ?>"><?php echo _('Log out'); ?></a></p>
            <p><a href="/settings/" title="<?php echo _('Account Settings'); ?>" class="float-right"><?php echo _('Account Settings'); ?></a></p>
		<?php } ?>
        </div>
	</div>
	<div id="nav">
		<div id="nav-links">
            <a href="/" title="<?php echo _('Dashboard'); ?>"<?php $template->select( 'dashboard', true ); ?>><?php echo _('Dashboard'); ?></a>
            <?php
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

            if ( isset( $user->account ) )
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
                    <a href="javascript:document.getElementById('fBlogForm').submit();" title="<?php echo $link[1]; ?>"><?php echo $link[1]; ?></a>
                    <?php
                    break;

                    default:
                        $selection = ( isset( $exceptions[$link[0]] ) ) ? $exceptions[$link[0]] : $link[0];
                        ?>
                        <a href="/<?php echo $link[0]; ?>/" title="<?php echo $link[1]; ?>"<?php $template->select( $selection, true ); ?>><?php echo $link[1]; ?></a>
                        <?php
                    break;
                }
            }
			?>
            <div id="nav-right">
                <div id="support">
                    <a href="#" id="aSupport" title="<?php echo _('Support'); ?>"><?php echo _('Support'); ?></a>
                    <div id="support-drop-down" class="hidden">
                        <a href="#" id="aTicket" title="<?php echo _('Support'); ?>"<?php echo $template->v('section_support'); ?>><?php echo _('Support Request'); ?></a>
                        <a href="/help/" title="<?php echo _('Knowledge Base'); ?>"<?php echo $template->v('section_support'); ?>><?php echo _('Knowledge Base'); ?></a>
                    </div>
                </div>
            </div>
		</div>
	</div>
    <div id="current-site">
        <?php if ( isset( $user->account ) ) { ?>
        <div class="float-left">
            <h3><?php echo _('Site'); ?>: <a href="http://<?php echo $user->account->domain; ?>/" title="<?php echo $user->account->title; ?>" target="_blank"><?php echo $user->account->title; ?></a></h3>
            <?php if ( count( $user->accounts ) > 1 ) { ?>
                <p><a href="/home/select-account/" class="highlight" title="<?php echo _('Change Account'); ?>">(<?php echo _('Change'); ?>)</a></p>
            <?php } ?>
        </div>
        <?php
        }
        if ( $user->has_permission( User::ROLE_MARKETING_SPECIALIST ) ) {
        ?>
        <div id="return-to-admin">
            <p><a href="http://admin.<?php echo DOMAIN; ?>/accounts/" class="button" title="<?php echo _('Back to Admin'); ?>"><?php echo _('Back to Admin'); ?></a></p>
        </div>
        <?php } ?>
    </div>