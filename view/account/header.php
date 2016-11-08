<?php
/**
 * @var Template $template
 * @var Resources $resources
 * @var Notification[] $notifications
 * @var User $user
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

    <link type="text/css" rel="stylesheet" href="/resources/company-css/" />

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
    <div class="row">
        <div class="col-md-5 col-sm-5 col-xs-12">
            <div class="sidebar-toggle-box">
                <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
            </div>
            <!--logo start-->
                <a href="/" class="logo"><img src="/images/logos/<?php echo DOMAIN; ?>.png" width="<?php echo LOGO_WIDTH; ?>" <?php if ( LOGO_HEIGHT < 60 ) echo 'style="margin-top: '. ( (60-LOGO_HEIGHT) / 2) .'px"' ?> alt="<?php echo TITLE, ' ', _('Logo'); ?>" /></a>
            <!--logo end-->
        </div>
        <div class="col-md-7 col-sm-7 col-xs-12">
            <div class="top-nav">
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
                        <a data-toggle="dropdown" class="dropdown-toggle hidden-xs" href="javascript:;">
                                <span class="account-name"><?php echo $user->account->title ?></span> | <span class="username"><?php echo $user->contact_name ?></span>
                            <b class="caret"></b>
                        </a>
                        <a data-toggle="dropdown" class="dropdown-toggle visible-xs" href="javascript:;">
                            <span class="visible-xs glyphicon glyphicon-user"></span>
                        </a>
                        <ul class="dropdown-menu extended logout">

                            <li class="dropdown-item-title">
                                <a href="javascript:;"><i class="fa fa-check"></i> <?php echo $user->contact_name ?></a>
                            </li>
                            <?php if ( $user->account->pages == 1 ): /* ONLY WITH USERS WITH A WEBSITE */ ?>
                                <li>
                                    <a href="http://<?php echo $user->account->domain ?>" target="_blank"><i class="fa fa-link" style="font-size:14px;"></i> <?php echo $user->account->title ?></a>
                                </li>
                            <?php endif; ?>
                            <?php foreach( array_slice($user->accounts, 0, 3) as $another_account ): ?>
                                <li>
                                    <a href="/home/change-account/?aid=<?php echo $another_account->id; ?>" title="<?php echo _('Change Account'); ?>"><i class="fa fa-circle"></i> <strong><?php echo $another_account->title; ?></strong></a>
                                </li>
                            <?php endforeach; ?>
                            <?php if ( count($user->accounts) > 3 ): ?>
                                <li>
                                    <a href="/home/select-account/" target="_blank"><i class="fa fa-plus" style="font-size:14px;"></i> More Accounts...</a>
                                </li>
                            <?php endif; ?>
                            <?php if ( $user->account->pages == 1 ): /* ONLY WITH USERS WITH A WEBSITE */ ?>
                                <li><a href="/settings/"><i class="fa fa-suitcase"></i> Settings</a></li>
                                <li><a href="/settings/authorized-users/"><i class="fa fa-users"></i> Authorized Users</a></li>
                                <li><a href="/settings/logo-and-phone/"><i class="fa fa-phone"></i> Logo &amp; Phone</a></li>
                            <?php else: ?>
                                <li>
                                    <a href="/settings/"><i class="fa fa-suitcase"></i> Settings</a>
                                </li>
                            <?php endif; ?>
                            <?php if ( $online_specialist->id ): ?>
                                <li class="multi-anchor">
                                    <?php
                                        echo '<a href="mailto:' . $online_specialist->email . '"><i class="fa fa-life-ring"></i> Online Specialist: ' . $online_specialist->contact_name . '</a>';
                                        echo '<a href="mailto:' . str_replace( strstr( $online_specialist->email, '@'), '@' . DOMAIN, $online_specialist->email ) . '">' . str_replace( strstr( $online_specialist->email, '@'), '@' . DOMAIN, $online_specialist->email ) . '</a>';
                                        if ( $online_specialist->work_phone ) echo '<a href="tel:' . $online_specialist->work_phone . '">' . $online_specialist->work_phone . '</a>';
                                    ?>
                                </li>
                            <?php endif; ?>
                            <li><a href="/logout/"><i class="fa fa-key"></i> Log Out</a></li>
                        </ul>
                    </li>
                    <!-- user login dropdown end -->
                </ul>
                <!--search & user info end-->
            </div>
        </div>
    </div>
</header>
<!--header end-->
<!--sidebar start-->
<aside>
    <div id="sidebar"  class="nav-collapse ">
        <!-- sidebar menu start-->
        <ul class="sidebar-menu" id="nav-accordion">

            <li class="sub-menu">
                <a href="/" <?php if ( $template->in_menu_item('dashboard') ) echo 'class="active"'?>>
                    <i class="fa fa-tachometer"></i>
                    <span>Dashboard</span>
                </a>
            </li>

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
                                <?php if ( $user->account->is_new_template() ): ?>
                                    <li <?php if ( $template->in_menu_item('website/pages/brands') ) echo 'class="active"' ?>><a href="/website/brands/">Brands</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                          <li class="submenu">
                            <a href="javascript:;" <?php if ( $template->in_menu_item('website/landing-pages/') ) echo 'class="active"' ?>>Landing Pages</a>
                            <ul class="sub">
                                <li <?php if ( $template->in_menu_item('website/landing-pages/list') ) echo 'class="active"' ?>><a href="/website/landing-pages/">List</a></li>
                            </ul>
                        </li>
                           <li class="submenu">
                                <a href="javascript:;" <?php if ( $template->in_menu_item('website/banners') ) echo 'class="active"' ?>>Sidebar</a>
                                <ul class="sub">
                                  <li <?php if ( $template->in_menu_item('website/banners') ) echo 'class="active"' ?>><a href="/website/sidebar/">Main Sidebar</a></li>
				  <li <?php if ( $template->in_menu_item('website/catalog_banners') ) echo 'class="active"' ?>><a href="/website/product-sidebar/">Product Sidebar</a></li>
                                </ul>
                            </li>
                                                                                        
                            <li class="submenu">
                                <a href="javascript:;" <?php if ( $template->in_menu_item('website/banners') ) echo 'class="active"' ?>>Banners</a>
                                <ul class="sub">
                                  <li <?php if ( $template->in_menu_item('website/banners') ) echo 'class="active"' ?>><a href="/website/banners/">Home Banners</a></li>
				  <li <?php if ( $template->in_menu_item('website/catalog_banners') ) echo 'class="active"' ?>><a href="/website/catalog-banner/">Catalog Banner</a></li>                                                                                        
                                </ul>
                            </li>
                                                                                        

                        <?php if ( $user->account->is_new_template() ): ?>
                            <li class="submenu">
                                <a href="javascript:;" <?php if ( $template->in_menu_item('website/navigation-menus') ) echo 'class="active"' ?>>Navigation Menus</a>
                                <ul class="sub">
                                    <li <?php if ( $template->in_menu_item('website/navigation-menus/header-navigation') ) echo 'class="active"' ?>><a href="/website/navigation/">Header Navigation</a></li>
                                    <li <?php if ( $template->in_menu_item('website/navigation-menus/footer-navigation') ) echo 'class="active"' ?>><a href="/website/footer-navigation/">Footer Navigation</a></li>
                                    <li <?php if ( $template->in_menu_item('website/navigation-menus/top-site-navigation') ) echo 'class="active"' ?>><a href="/website/top-site-navigation/">Top Site Navigation</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                        <li class="submenu">
                            <a href="javascript:;" <?php if ( $template->in_menu_item('website/settings') ) echo 'class="active"' ?>>Settings</a>
                            <ul class="sub">
                                <li <?php if ( $template->in_menu_item('website/settings/settings') ) echo 'class="active"' ?>><a href="/website/settings/">Website Settings</a></li>
                                <?php if ( $user->account->is_new_template() ): ?>
                                    <li <?php if ( $template->in_menu_item('website/settings/website-header') ) echo 'class="active"' ?>><a href="/website/header/">Website Header</a></li>
                                    <li <?php if ( $template->in_menu_item('website/settings/website-footer') ) echo 'class="active"' ?>><a href="/website/footer/">Website Footer</a></li>
                                    <li <?php if ( $template->in_menu_item('website/settings/html-head') ) echo 'class="active"' ?>><a href="/website/html-head/">HTML &lt;head&gt;</a></li>
                                    <li <?php if ( $template->in_menu_item('website/settings/custom-404') ) echo 'class="active"' ?>><a href="/website/custom-404/">Custom 404 Page</a></li>
                                    <li <?php if ( $template->in_menu_item('website/settings/home-page-layout') ) echo 'class="active"' ?>><a href="/website/home-page-layout/">Homepage Layout</a></li>
                                    <li <?php if ( $template->in_menu_item('website/settings/css') ) echo 'class="active"' ?>><a href="/website/stylesheet/">LESS/CSS</a></li>
                                    <li <?php if ( $template->in_menu_item('website/settings/favicon') ) echo 'class="active"' ?>><a href="/website/favicon/">Favicon</a></li>
                                <?php endif; ?>
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
                                <li <?php if ( $template->in_menu_item('products/products/block-products') ) echo 'class="active"' ?>><a href="/products/block-products/">Hidden Products</a></li>
                                <li <?php if ( $template->in_menu_item('products/products/hide-categories') ) echo 'class="active"' ?>><a href="/products/hide-categories/">Hide Categories</a></li>
                                <li <?php if ( $template->in_menu_item('products/products/manually-priced') ) echo 'class="active"' ?>><a href="/products/manually-priced/">Manually Priced</a></li>
                                <li <?php if ( $template->in_menu_item('products/products/auto-price') ) echo 'class="active"' ?>><a href="/products/auto-price/">Pricing Tools</a></li>
                                <li <?php if ( $template->in_menu_item('products/products/export') ) echo 'class="active"' ?>><a href="/products/export/">Export</a></li>
                                <li <?php if ( $template->in_menu_item('products/products/import') ) echo 'class="active"' ?>><a href="/products/import/">Import</a></li>
                            </ul>
                        </li>
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

            <?php if ( $user->account->product_catalog ): ?>
                <li class="sub-menu">
                    <a href="javascript:;" <?php if ( $template->in_menu_item('sales-desk') ) echo 'class="active"' ?>>
                        <i class="fa fa-thumbs-o-up"></i>
                        <span>Sales Desk</span>
                    </a>
                    <ul class="sub">
                        <li <?php if ( $template->in_menu_item('sales-desk/index') ) echo 'class="active"'?>><a href="/sales-desk/">Sales Desk</a></li>
                        <li <?php if ( $template->in_menu_item('sales-desk/settings') ) echo 'class="active"'?>><a href="/sales-desk/settings/">Settings</a></li>
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
                        <li <?php if ( $template->in_menu_item('analytics/settings') ) echo 'class="active"'?>><a href="/analytics/settings/">Settings</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if ( $user->account->blog == 1 ): ?>
                <form action="http://<?php echo $user->account->domain; ?>/blog/wp-login.php" target="_blank" method="post" id="fBlogForm" class="hidden">
                    <input type="hidden" name="log" value="<?php echo security::decrypt( base64_decode( $user->account->wordpress_username ), ENCRYPTION_KEY ); ?>" />
                    <input type="hidden" name="pwd" value="<?php echo security::decrypt( base64_decode( $user->account->wordpress_password ), ENCRYPTION_KEY ); ?>" />
                </form>
                <li>
                    <a href="javascript:document.getElementById('fBlogForm').submit();">
                        <i class="fa fa-wordpress"></i>
                        <span>Blog</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ( $user->account->email_marketing == 1 ): ?>
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
            <?php elseif ( $user->account->pages == 1 ): /* ONLY WITH USERS WITH A WEBSITE */ ?>
                <li class="sub-menu">
                    <a href="javascript:;" <?php if ( $template->in_menu_item('email-marketing') ) echo 'class="active"'?>>
                        <i class="fa fa-inbox"></i>
                        <span>Email Marketing</span>
                    </a>
                    <ul class="sub">
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
                            <a href="/email-marketing/autoresponders/" class="<?php if ( $template->in_menu_item('email-marketing/autoresponders') ) echo 'active'?>">Autoresponders</a>
                            <ul class="sub">
                                <li <?php if ( $template->in_menu_item('email-marketing/autoresponders/list') ) echo 'class="active"'?>><a href="/email-marketing/autoresponders/">List</a></li>
                                <li <?php if ( $template->in_menu_item('email-marketing/autoresponders/add') ) echo 'class="active"'?>><a href="/email-marketing/autoresponders/add-edit/">Add</a></li>
                            </ul>
                        </li>
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
                                <?php if ( $user->account->get_settings( 'ashley-express' ) ): ?>
                                    <li <?php if ( $template->in_menu_item('shopping-cart/shipping/add-ashley-express-ups') ) echo 'class="active"'?>><a href="/shopping-cart/shipping/add-edit-ashley-express-ups/">Add UPS (Ashley)</a></li>
                                    <li <?php if ( $template->in_menu_item('shopping-cart/shipping/add-ashley-express-fedex') ) echo 'class="active"'?>><a href="/shopping-cart/shipping/add-edit-ashley-express-fedex/">Add FedEx (Ashley)</a></li>
                                <?php endif; ?>
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
                        <?php if ( COMPANY_ID == 4 ): ?>
                            <li class="submenu">
                                <a href="/shopping-cart/remarketing/" class="<?php if ( $template->in_menu_item('shopping-cart/remarketing') ) echo 'active'?>">Remarketing</a>
                                <ul class="sub">
                                    <li <?php if ( $template->in_menu_item('shopping-cart/remarketing/list') ) echo 'class="active"'?>><a href="/shopping-cart/remarketing/">List</a></li>
                                    <li class="submenu <?php if ( $template->in_menu_item('shopping-cart/remarketing/settings') ) echo 'active'?>">
                                    <a href="/shopping-cart/remarketing/settings/"<?php if ( $template->in_menu_item('shopping-cart/remarketing/popup') || $template->in_menu_item('shopping-cart/remarketing/emails')  ) echo 'class="active"'?>  >Settings</a>
                                    <ul class="sub">
                                        <li <?php if ( $template->in_menu_item('shopping-cart/remarketing/list') ) echo 'class="active"'?>><a href="/shopping-cart/remarketing/popup">Popup &amp; Coupon </a></li>
                                        <li <?php if ( $template->in_menu_item('shopping-cart/remarketing/list') ) echo 'class="active"'?>><a href="/shopping-cart/remarketing/emails">Emails </a></li>                                    

                                    </ul>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
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

            <?php if ( $user->account->social_media ): ?>
                <li class="sub-menu">
                    <a href="javascript:;" <?php if ( $template->in_menu_item('social-media') || $template->in_menu_item('sm') ) echo 'class="active"'?>>
                        <i class="fa fa-facebook"></i>
                        <span>Social Media</span>
                    </a>
                    <ul class="sub">
                        <li <?php if ( $template->in_menu_item('sm/post') ) echo 'class="active"'?> ><a href="/sm/post/" <?php if ( $template->in_menu_item('sm/post') ) echo 'class="active"'?> >Posting</a></li>

                    </ul>
                </li>
            <?php endif; ?>
            <?php if ( $user->account->geo_marketing ): ?>
                <li class="sub-menu">
                    <a href="javascript:;" <?php if ( $template->in_menu_item('geo-marketing') ) echo 'class="active"' ?>>
                        <i class="fa fa-dot-circle-o"></i>
                        <span>GeoMarketing</span>
                    </a>
                    <ul class="sub">
                        <li <?php if ( $template->in_menu_item('geo-marketing/locations') ) echo 'class="active"' ?>><a href="/geo-marketing/locations/">Locations</a></li>
                        <li <?php if ( $template->in_menu_item('geo-marketing/bios') ) echo 'class="active"' ?>><a href="/geo-marketing/bios/">Bios</a></li>
                        <li <?php if ( $template->in_menu_item('geo-marketing/listings') ) echo 'class="active"' ?>><a href="/geo-marketing/listings/">Listings</a></li>
                        <li <?php if ( $template->in_menu_item('geo-marketing/analytics') ) echo 'class="active"' ?>><a href="/geo-marketing/analytics/">Analytics</a></li>
                        <?php if ( $user->account->get_settings( 'yext-customer-reviews' ) ): ?>
                            <li <?php if ( $template->in_menu_item('geo-marketing/reviews') ) echo 'class="active"' ?>><a href="/geo-marketing/reviews/">Customer Reviews</a></li>
                            <li <?php if ( $template->in_menu_item('geo-marketing/settings') ) echo 'class="active"' ?>><a href="/geo-marketing/settings/">Settings</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>


            <li class="sub-menu">
                <a href="javascript:;" <?php if ( stristr( $_SERVER['REQUEST_URI'], '/settings/' ) ) echo 'class="active"'?>>
                    <i class="fa fa-suitcase"></i>
                    <span>Settings</span>
                </a>
                <ul class="sub">
                    <li <?php if ( '/settings/' == $_SERVER['REQUEST_URI'] ) echo 'class="active"'?>><a href="/settings/">Settings</a></li>
                    <?php if ( $user->account->pages == 1 ): /* ONLY WITH USERS WITH A WEBSITE */ ?>
                        <li <?php if ( '/settings/authorized-users/' == $_SERVER['REQUEST_URI'] ) echo 'class="active"'?>><a href="/settings/authorized-users/">Authorized Users</a></li>
                        <li <?php if ( '/settings/logo-and-phone/' == $_SERVER['REQUEST_URI'] ) echo 'class="active"'?>><a href="/settings/logo-and-phone/">Logo &amp; Phone</a></li>
                    <?php endif; ?>
                    <?php if ( $user->has_permission( User::ROLE_SUPER_ADMIN ) ): ?>
                    <li <?php if ( '/settings/billing-information/' == $_SERVER['REQUEST_URI'] && $user->has_permission( User::ROLE_SUPER_ADMIN ) ) echo 'class="active"'?>><a href="/settings/billing-information/">Billing Information</a></li>
                    <?php endif; ?>
                    <?php if ( $user->account && $user->account->get_settings( 'cloudflare-zone-id' ) ): ?>
                    <li <?php if ( '/settings/domain/' == $_SERVER['REQUEST_URI'] ) echo 'class="active"'?>><a href="/settings/domain/">Domain</a></li>
                    <?php endif; ?>
                </ul>
            </li>

         </ul>

        </ul>
        <!-- sidebar menu end-->
    </div>
</aside>
<!--sidebar end-->
<!--main content start-->
<section id="main-content">
<section class="wrapper site-min-height">
