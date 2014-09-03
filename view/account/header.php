<?php
/**
 * @var Template $template
 * @var Resources $resources
 * @var Notification[] $notifications
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="/images/favicons/<?php echo DOMAIN ?>.ico">

    <title><?php echo $template->v('title') . ' | ' . TITLE ?></title>

    <!-- Bootstrap core CSS -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="/resources/css_single/?f=bootstrap-reset" rel="stylesheet" />

    <!--external css-->
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet" />
    <link href="//cdn.datatables.net/plug-ins/be7019ee387/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet" />
    <link href="//cdn.jsdelivr.net/jquery.gritter/1.7.4/css/jquery.gritter.css" rel="stylesheet" />
    <link href="/resources/css_single/?f=bootstrap-switch" rel="stylesheet" />

    <?php echo $resources->get_css_urls(); ?>

    <!-- Custom styles for this template -->
    <link type="text/css" rel="stylesheet" href="/resources/css_single/?f=style" />
    <link type="text/css" rel="stylesheet" href="/resources/css_single/?f=style-responsive" />

    <link type="text/css" rel="stylesheet" href="/resources/css/?f=<?php echo $resources->get_css_file(); ?>" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery -->
    <script src="//code.jquery.com/jquery-2.1.1.js"></script>
</head>
<body>

<section id="container" >

<?php if ( !empty( $notifications ) ): ?>
    <?php foreach( $notifications as $notification ): ?>
        <div class="alert alert-dismissible alert-<?php echo $notification->success ? 'success' : 'danger' ?> fade in" role="alert">
            <button data-dismiss="alert" class="close close-sm" type="button">
                <i class="fa fa-times"></i>
            </button>
            <?php echo $notification->message ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!--header start-->
<header class="header white-bg">
<div class="sidebar-toggle-box">
    <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
</div>
<!--logo start-->
    <a href="/" class="logo"><img src="/images/logos/<?php echo DOMAIN; ?>.png" width="<?php echo LOGO_WIDTH; ?>" <?php if ( LOGO_HEIGHT < 60 ) echo 'style="margin-top: '. ( (60-LOGO_HEIGHT) / 2) .'px"' ?> alt="<?php echo TITLE, ' ', _('Logo'); ?>" /></a>
<!--logo end-->
<div class="top-nav ">
    <!--search & user info start-->
    <ul class="nav pull-right top-menu">
        <!-- kb support dropdown start -->
        <li class="dropdown" id="kb-dropdown">
            <a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;">
                <span class="glyphicon glyphicon-question-sign"></span>
            </a>
            <ul class="dropdown-menu extended">
                <div class="log-arrow-up"></div>
                <li><a href="#" data-toggle="modal" data-target="#support-modal"><i class="fa fa-ticket"></i> Support Request</a></li>
                <?php if ( !empty( $kbh_articles ) ): ?>
                    <li><a href="javascript:;" class="keep-open"><i class="fa fa-book"></i> <strong>Knowledge Base</strong></a></li>
                    <?php foreach ( $kbh_articles as $kbh_article ): ?>
                        <li><a href="<?php echo url::add_query_arg( 'aid', $kbh_article->id, '/kb/article/' ); ?>" title="<?php echo $kbh_article->title; ?>" target="_blank"><?php echo str_repeat( '&nbsp;', 8 ) . $kbh_article->title; ?></a></li>
                    <?php endforeach ; ?>
                <?php endif; ?>
                <li><a href="/kb/browser/"><i class="fa fa-globe"></i> Browser Support</a></li>
            </ul>
        </li>
        <!-- kb support dropdown end -->
        <!-- user login dropdown start-->
        <li class="dropdown">
            <a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;">
                <span class="account-name"><?php echo $user->account->title ?></span> | <span class="username"><?php echo $user->contact_name ?></span>
                <b class="caret"></b>
            </a>
            <ul class="dropdown-menu extended logout">
                <div class="log-arrow-up"></div>
                <li class="big">
                    <a href="http://<?php echo $user->account->domain ?>" target="_blank">Visit <?php echo $user->account->domain ?> <i class="fa fa-link" style="font-size:14px;"></i></a>
                </li>
                <li><a href="/settings/"><i class="fa fa-suitcase"></i> Settings</a></li>
                <li><a href="/settings/authorized-users/"><i class="fa fa-users"></i> Authorized <br> Users</a></li>
                <li><a href="/settings/logo-and-phone/"><i class="fa fa-phone"></i> Logo &amp; Phone</a></li>
                <?php if ( $online_specialist->id ): ?>
                    <li class="big">
                        <?php
                            echo '<a href="mailto:' . $online_specialist->email . '"><i class="fa fa-life-ring"></i> Online Specialist: ' . $online_specialist->contact_name . '</a>';
                            echo '<a href="mailto:' . $online_specialist->email . '">' . $online_specialist->email . '</a>';
                            if ( $online_specialist->work_phone ) echo ' | <a href="tel:' . $online_specialist->work_phone . '">' . $online_specialist->work_phone . '</a>';
                        ?>
                    </li>
                <?php endif; ?>
                <li class="big"><a href="/logout/"><i class="fa fa-key"></i> Log Out</a></li>
            </ul>
        </li>
        <!-- user login dropdown end -->
    </ul>
    <!--search & user info end-->
</div>
</header>
<!--header end-->
<!--sidebar start-->
<aside>
    <div id="sidebar"  class="nav-collapse ">
        <!-- sidebar menu start-->
        <ul class="sidebar-menu" id="nav-accordion">

            <?php if ( $user->account->pages ): ?>
                <li class="sub-menu">
                    <a href="javascript:;" <?php if ( $template->in_menu_item('website') ) echo 'class="active"' ?>>
                        <i class="fa fa-globe"></i>
                        <span>Website</span>
                    </a>
                    <ul class="sub">
                        <li class="submenu">
                            <a href="javascript:;" <?php if ( $template->in_menu_item('website/pages') ) echo 'class="active"' ?>>Pages</a>
                            <ul class="sub">
                                <li <?php if ( $template->in_menu_item('website/pages/list') ) echo 'class="active"' ?>><a href="/website/">List</a></li>
                                <li <?php if ( $template->in_menu_item('website/pages/categories') ) echo 'class="active"' ?>><a href="/website/categories/">Categories</a></li>
                                <li <?php if ( $template->in_menu_item('website/pages/brands') ) echo 'class="active"' ?>><a href="/website/brands/">Brands</a></li>
                            </ul>
                        </li>
                        <li <?php if ( $template->in_menu_item('website/sidebar') ) echo 'class="active"' ?>><a href="/website/sidebar/">Sidebar</a></li>
                        <li <?php if ( $template->in_menu_item('website/banners') ) echo 'class="active"' ?>><a href="/website/banners/">Banners</a></li>
                        <li class="submenu">
                            <a href="javascript:;" <?php if ( $template->in_menu_item('website/settings') ) echo 'class="active"' ?>>Settings</a>
                            <ul class="sub">
                                <li <?php if ( $template->in_menu_item('website/settings/settings') ) echo 'class="active"' ?>><a href="/website/settings/">Settings</a></li>
                                <li <?php if ( $template->in_menu_item('website/settings/home-page-layout') ) echo 'class="active"' ?>><a href="/website/home-page-layout/">Home Page Layout</a></li>
                                <li <?php if ( $template->in_menu_item('website/settings/header-navigation') ) echo 'class="active"' ?>><a href="/website/navigation/">Header Navigation</a></li>
                                <li <?php if ( $template->in_menu_item('website/settings/website-header') ) echo 'class="active"' ?>><a href="/website/header/">Website Header</a></li>
                                <li <?php if ( $template->in_menu_item('website/settings/footer-navigation') ) echo 'class="active"' ?>><a href="/website/footer-navigation/">Footer Navigation</a></li>
                                <li <?php if ( $template->in_menu_item('website/settings/website-footer') ) echo 'class="active"' ?>><a href="/website/footer/">Website Footer</a></li>
                                <li <?php if ( $template->in_menu_item('website/settings/html-head') ) echo 'class="active"' ?>><a href="/website/html-head/">HTML &lt;head&gt;</a></li>
                                <li <?php if ( $template->in_menu_item('website/settings/custom-404') ) echo 'class="active"' ?>><a href="/website/custom-404/">Custom 404 Page</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if ( $user->account->product_catalog ): ?>
                <li class="sub-menu">
                    <a href="javascript:;" <?php if ( $template->in_menu_item('products') ) echo 'class="active"' ?>>
                        <i class="fa fa-barcode"></i>
                        <span>Products</span>
                    </a>
                    <ul class="sub">
                        <li class="submenu">
                            <a href="javascript:;" <?php if ( $template->in_menu_item('products/products') ) echo 'class="active"' ?>>Products</a>
                            <ul class="sub">
                                <li <?php if ( $template->in_menu_item('products/products/list') ) echo 'class="active"' ?>><a href="/products/">List</a></li>
                                <li <?php if ( $template->in_menu_item('products/products/add') ) echo 'class="active"' ?>><a href="/products/add/">Add</a></li>
                                <li <?php if ( $template->in_menu_item('products/products/all') ) echo 'class="active"' ?>><a href="/products/all/">All Products</a></li>
                                <li <?php if ( $template->in_menu_item('products/products/catalog-dump') ) echo 'class="active"' ?>><a href="/products/catalog-dump/">Catalog Dump</a></li>
                                <li <?php if ( $template->in_menu_item('products/products/add-bulk') ) echo 'class="active"' ?>><a href="/products/add-bulk/">Add Bulk</a></li>
                                <li <?php if ( $template->in_menu_item('products/products/block-products') ) echo 'class="active"' ?>><a href="/products/block-products/">Block Products</a></li>
                                <li <?php if ( $template->in_menu_item('products/products/hide-categories') ) echo 'class="active"' ?>><a href="/products/hide-categories/">Hide Categories</a></li>
                                <li <?php if ( $template->in_menu_item('products/products/manually-priced') ) echo 'class="active"' ?>><a href="/products/manually-priced/">Manually Priced</a></li>
                                <li <?php if ( $template->in_menu_item('products/products/auto-price') ) echo 'class="active"' ?>><a href="/products/auto-price/">Pricing Tools</a></li>
                                <li <?php if ( $template->in_menu_item('products/products/export') ) echo 'class="active"' ?>><a href="/products/export/">Export</a></li>
                            </ul>
                        </li>
                        <li <?php if ( $template->in_menu_item('products/reaches') ) echo 'class="active"' ?>><a href="/products/reaches/">Reaches</a></li>
                        <li class="submenu">
                            <a href="/products/reaches" <?php if ( $template->in_menu_item('products/product-builder') ) echo 'class="active"' ?>>Product Builder</a>
                            <ul class="sub">
                                <li <?php if ( $template->in_menu_item('products/product-builder/list') ) echo 'class="active"' ?>><a href="/products/product-builder/">List</a></li>
                                <li <?php if ( $template->in_menu_item('products/product-builder/add') ) echo 'class="active"' ?>><a href="/products/product-builder/add-edit/">Add</a></li>
                            </ul>
                        </li>
                        <li <?php if ( $template->in_menu_item('products/brands') ) echo 'class="active"' ?>><a href="/products/brands/">Top Brands</a></li>
                        <li <?php if ( $template->in_menu_item('products/top-categories') ) echo 'class="active"' ?>><a href="/products/top-categories/">Top Categories</a></li>
                        <li class="submenu">
                            <a href="/products/reaches" <?php if ( $template->in_menu_item('products/related-products') ) echo 'class="active"' ?>>Related Products</a>
                            <ul class="sub">
                                <li <?php if ( $template->in_menu_item('products/related-products/list') ) echo 'class="active"' ?>><a href="/products/related-products/">List</a></li>
                                <li <?php if ( $template->in_menu_item('products/related-products/add') ) echo 'class="active"' ?>><a href="/products/related-products/add-edit/">Add</a></li>
                            </ul>
                        </li>
                        <li <?php if ( $template->in_menu_item('products/settings') ) echo 'class="active"' ?>><a href="/products/settings/">Settings</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if ( $user->account->live ): ?>
                <li class="sub-menu">
                    <a href="javascript:;" <?php if ( $template->in_menu_item('analytics') ) echo 'class="active"'?>>
                        <i class="fa fa-bar-chart-o"></i>
                        <span>Analytics</span>
                    </a>
                    <ul class="sub">
                        <li <?php if ( $template->in_menu_item('analytics/index') ) echo 'class="active"'?>><a href="/analytics/">Analytics</a></li>
                        <li <?php if ( $template->in_menu_item('analytics/content-overview') ) echo 'class="active"'?>><a href="/analytics/content-overview/">Content Overview</a></li>
                        <li <?php if ( $template->in_menu_item('analytics/traffic-sources-overview') ) echo 'class="active"'?>><a href="/analytics/traffic-sources-overview/">Traffic Sources Overview</a></li>
                        <li <?php if ( $template->in_menu_item('analytics/traffic-sources') ) echo 'class="active"'?>><a href="/analytics/traffic-sources/">Sources</a></li>
                        <li <?php if ( $template->in_menu_item('analytics/keywords') ) echo 'class="active"'?>><a href="/analytics/keywords/">Keywords</a></li>
                        <li <?php if ( $template->in_menu_item('analytics/email-marketing') ) echo 'class="active"'?>><a href="/analytics/email-marketing/">Email Marketing</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if ( $user->account->email_marketing ): ?>
                <li class="sub-menu">
                    <a href="javascript:;" <?php if ( $template->in_menu_item('email-marketing') ) echo 'class="active"'?>>
                        <i class="fa fa-inbox"></i>
                        <span>Email Marketing</span>
                    </a>
                    <ul class="sub">
                        <li <?php if ( $template->in_menu_item('email-marketing/dashboard') ) echo 'class="active"'?>><a href="/email-marketing/">Dashboard</a></li>
                        <li class="submenu">
                            <a href="/email-marketing/campaigns/" class="<?php if ( $template->in_menu_item('email-marketing/campaigns') ) echo 'active'?>">Campaigns</a>
                            <ul class="sub">
                                <li <?php if ( $template->in_menu_item('email-marketing/campaigns/list') ) echo 'class="active"'?>><a href="/email-marketing/campaigns/">List</a></li>
                                <li <?php if ( $template->in_menu_item('email-marketing/campaigns/create') ) echo 'class="active"'?>><a href="/email-marketing/campaigns/create">Create</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="/email-marketing/subscribers/" class="<?php if ( $template->in_menu_item('email-marketing/subscribers') ) echo 'active'?>">Subscribers</a>
                            <ul class="sub">
                                <li <?php if ( $template->in_menu_item('email-marketing/subscribers/list') ) echo 'class="active"'?>><a href="/email-marketing/subscribers/">Subscribed</a></li>
                                <li <?php if ( $template->in_menu_item('email-marketing/subscribers/unsubscribed') ) echo 'class="active"'?>><a href="/email-marketing/subscribers/unsubscribed/">Unsubscribed</a></li>
                                <li <?php if ( $template->in_menu_item('email-marketing/subscribers/add') ) echo 'class="active"'?>><a href="/email-marketing/subscribers/add-edit/">Add</a></li>
                                <li <?php if ( $template->in_menu_item('email-marketing/subscribers/import') ) echo 'class="active"'?>><a href="/email-marketing/subscribers/import/">Import</a></li>
                                <li <?php if ( $template->in_menu_item('email-marketing/subscribers/export') ) echo 'class="active"'?>><a href="/email-marketing/subscribers/export/">Export</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="/email-marketing/email-lists/" class="<?php if ( $template->in_menu_item('email-marketing/email-lists') ) echo 'active'?>">Email Lists</a>
                            <ul class="sub">
                                <li <?php if ( $template->in_menu_item('email-marketing/email-lists/list') ) echo 'class="active"'?>><a href="/email-marketing/email-lists/">List</a></li>
                                <li <?php if ( $template->in_menu_item('email-marketing/email-lists/add') ) echo 'class="active"'?>><a href="/email-marketing/email-lists/add-edit/">Add</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="/email-marketing/autoresponders/" class="<?php if ( $template->in_menu_item('email-marketing/autoresponders') ) echo 'active'?>">Autoresponders</a>
                            <ul class="sub">
                                <li <?php if ( $template->in_menu_item('email-marketing/autoresponders/list') ) echo 'class="active"'?>><a href="/email-marketing/autoresponders/">List</a></li>
                                <li <?php if ( $template->in_menu_item('email-marketing/autoresponders/add') ) echo 'class="active"'?>><a href="/email-marketing/autoresponders/add-edit/">Add</a></li>
                            </ul>
                        </li>
                        <li <?php if ( $template->in_menu_item('email-marketing/settings') ) echo 'class="active"'?>><a href="/email-marketing/settings/">Settings</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if ( $user->account->shopping_cart ): ?>
                <li class="sub-menu">
                    <a href="javascript:;" <?php if ( $template->in_menu_item('shopping-cart') ) echo 'class="active"'?>>
                        <i class="fa fa-shopping-cart"></i>
                        <span>Shopping Cart</span>
                    </a>
                    <ul class="sub">
                        <li <?php if ( $template->in_menu_item('shopping-cart/orders') ) echo 'class="active"'?>><a href="/shopping-cart/orders/">Orders</a></li>
                        <li <?php if ( $template->in_menu_item('shopping-cart/users') ) echo 'class="active"'?>><a href="/shopping-cart/users/">Users</a></li>
                        <li class="submenu">
                            <a href="/shopping-cart/shipping/" class="<?php if ( $template->in_menu_item('shopping-cart/shipping') ) echo 'active'?>">Shipping</a>
                            <ul class="sub">
                                <li <?php if ( $template->in_menu_item('shopping-cart/shipping/list') ) echo 'class="active"'?>><a href="/shopping-cart/shipping/">List</a></li>
                                <li <?php if ( $template->in_menu_item('shopping-cart/shipping/add-custom') ) echo 'class="active"'?>><a href="/shopping-cart/shipping/add-edit-custom/">Add Custom</a></li>
                                <li <?php if ( $template->in_menu_item('shopping-cart/shipping/add-ups') ) echo 'class="active"'?>><a href="/shopping-cart/shipping/add-edit-ups/">Add UPS</a></li>
                                <li <?php if ( $template->in_menu_item('shopping-cart/shipping/add-fedex') ) echo 'class="active"'?>><a href="/shopping-cart/shipping/add-edit-fedex/">Add FedEx</a></li>
                                <li <?php if ( $template->in_menu_item('shopping-cart/shipping/settings') ) echo 'class="active"'?>><a href="/shopping-cart/shipping/settings/">Settings</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="/shopping-cart/coupons/" class="<?php if ( $template->in_menu_item('shopping-cart/coupons') ) echo 'active'?>">Coupons</a>
                            <ul class="sub">
                                <li <?php if ( $template->in_menu_item('shopping-cart/coupons/list') ) echo 'class="active"'?>><a href="/shopping-cart/coupons/">List</a></li>
                                <li <?php if ( $template->in_menu_item('shopping-cart/coupons/add') ) echo 'class="active"'?>><a href="/shopping-cart/coupons/add-edit/">Add</a></li>
                                <li <?php if ( $template->in_menu_item('shopping-cart/coupons/apply-to-brand') ) echo 'class="active"'?>><a href="/shopping-cart/coupons/apply-to-brand/">Apply to Brand</a></li>
                                <li <?php if ( $template->in_menu_item('shopping-cart/coupons/products') ) echo 'class="active"'?>><a href="/shopping-cart/coupons/products/">Products in Coupon</a></li>
                            </ul>
                        </li>
                        <li class="submenu">
                            <a href="/shopping-cart/settings/" class="<?php if ( $template->in_menu_item('shopping-cart/settings') ) echo 'active'?>">Settings</a>
                            <ul class="sub">
                                <li <?php if ( $template->in_menu_item('shopping-cart/settings/general') ) echo 'class="active"'?>><a href="/shopping-cart/settings/">General</a></li>
                                <li <?php if ( $template->in_menu_item('shopping-cart/settings/payment-settings') ) echo 'class="active"'?>><a href="/shopping-cart/settings/payment-settings/">Payment Settings</a></li>
                                <li <?php if ( $template->in_menu_item('shopping-cart/settings/taxes') ) echo 'class="active"'?>><a href="/shopping-cart/settings/taxes/">Taxes</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if ( $user->account->craigslist ): ?>
                <li class="sub-menu">
                    <a href="javascript:;" <?php if ( $template->in_menu_item('craigslist') ) echo 'class="active"'?>>
                        <span>Craigslist</span>
                    </a>
                    <ul class="sub">
                        <li <?php if ( $template->in_menu_item('craigslist/list') ) echo 'class="active"'?>><a href="/craigslist/">List All</a></li>
                        <li <?php if ( $template->in_menu_item('craigslist/add') ) echo 'class="active"'?>><a href="/craigslist/add-edit/">Add</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if ( $user->account->social_media ): ?>
                <li class="sub-menu">
                    <a href="javascript:;" <?php if ( $template->in_menu_item('social-media') ) echo 'class="active"'?>>
                        <i class="fa fa-facebook"></i>
                        <span>Social Media</span>
                    </a>
                    <ul class="sub">
                        <li <?php if ( $template->in_menu_item('social-media/facebook/list') ) echo 'class="active"'?>><a href="/social-media/">List All</a></li>
                        <li <?php if ( $template->in_menu_item('social-media/facebook/add') ) echo 'class="active"'?>><a href="/social-media/facebook/add-edit/">Add</a></li>
                        <li <?php if ( $template->in_menu_item('social-media/facebook/settings') ) echo 'class="active"'?>><a href="/social-media/facebook/settings/">Settings</a></li>
                    </ul>
                </li>
            <?php endif; ?>

        </ul>
        <!-- sidebar menu end-->
    </div>
</aside>
<!--sidebar end-->
<!--main content start-->
<section id="main-content">
<section class="wrapper site-min-height">

