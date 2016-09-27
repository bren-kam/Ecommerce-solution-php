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

<section id="container">

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
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="sidebar-toggle-box">
                <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
            </div>
            <!--logo start-->
            <a href="/" class="logo"><img src="/images/logos/<?php echo DOMAIN; ?>.png" width="<?php echo LOGO_WIDTH; ?>" <?php if ( LOGO_HEIGHT < 60 ) echo 'style="margin-top: '. ( (60-LOGO_HEIGHT) / 2) .'px"' ?> alt="<?php echo TITLE, ' ', _('Logo'); ?>" /></a>
            <!--logo end-->
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12">
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
                            <span class="username"><?php echo $user->contact_name ?></span>
                            <b class="caret"></b>
                        </a>
                        <a data-toggle="dropdown" class="dropdown-toggle visible-xs" href="javascript:;">
                            <span class="visible-xs glyphicon glyphicon-user"></span>
                        </a>
                        <ul class="dropdown-menu extended">
                            <div class="log-arrow-up"></div>
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
                <a href="javascript:;" <?php if ( $template->v('accounts') ) echo 'class="active"'?>>
                    <i class="fa fa-laptop"></i>
                    <span>Accounts</span>
                </a>
                <ul class="sub">
                    <li <?php if ( $template->v('accounts/index') ) echo 'class="active"'?>><a href="/accounts/">View</a></li>
                    <li <?php if ( $template->v('accounts/add') ) echo 'class="active"'?>><a href="/accounts/add/">Add</a></li>
                    <li <?php if ( $template->v('companies/index') ) echo 'class="active"'?>><a href="/accounts/companies/">Companies</a></li>
                    <li <?php if ( $template->v('companies/add') ) echo 'class="active"'?>><a href="/accounts/companies/add-edit/">Add Company</a></li>
                </ul>
            </li>

            <li class="sub-menu">
                <a href="javascript:;" <?php if ( $template->v('products') ) echo 'class="active"'?>>
                    <i class="fa fa-cube"></i>
                    <span>Products</span>
                </a>
                <ul class="sub">
                    <li <?php if ( $template->v('products/index') ) echo 'class="active"'?>><a href="/products/">View</a></li>
                    <li <?php if ( $template->v('products/add-edit') ) echo 'class="active"'?>><a href="/products/add-edit/">Add</a></li>
                    <li <?php if ( $template->v('products/import') ) echo 'class="active"'?>><a href="/products/import/">Import</a></li>
                    <li <?php if ( $template->v('products/categories') ) echo 'class="active"'?>><a href="/products/categories/">Categories</a></li>
                    <li><a href="/products/categories/list-text/">List Categories</a></li>
                    <li <?php if ( $template->v('products/attributes') ) echo 'class="active"'?>><a href="/products/attributes/">Attributes</a></li>
                    <li <?php if ( $template->v('products/attributes/add') ) echo 'class="active"'?>><a href="/products/attributes/add-edit/">Add Attribute</a></li>
                    <li <?php if ( $template->v('products/brands') ) echo 'class="active"'?>><a href="/products/brands/">Brands</a></li>
                    <li <?php if ( $template->v('products/brands/add') ) echo 'class="active"'?>><a href="/products/brands/add-edit/">Add Brand</a></li>
                </ul>
            </li>

            <li class="sub-menu">
                <a href="javascript:;" <?php if ( $template->v('users') ) echo 'class="active"'?>>
                    <i class="fa fa-user"></i>
                    <span>Users</span>
                </a>
                <ul class="sub">
                    <li <?php if ( $template->v('users/index') ) echo 'class="active"'?>><a href="/users/">View</a></li>
                    <li <?php if ( $template->v('users/add') ) echo 'class="active"'?>><a href="/users/add-edit/">Add</a></li>
                </ul>
            </li>

            <li class="sub-menu">
                <a href="javascript:;" <?php if ( $template->v('checklists') ) echo 'class="active"'?>>
                    <i class="fa fa-check-square-o"></i>
                    <span>Checklists</span>
                </a>
                <ul class="sub">
                    <li <?php if ( $template->v('checklists/index') ) echo 'class="active"'?>><a href="/checklists/">View</a></li>
                    <li <?php if ( $template->v('checklists/manage') ) echo 'class="active"'?>><a href="/checklists/manage/">Manage Checklists</a></li>
                </ul>
            </li>

            <li>
                <a href="/customer-support/" <?php if ( $template->v('tickets') ) echo 'class="active"'?>>
                    <i class="fa fa-ticket"></i>
                    <span>Tickets</span>
                </a>
            </li>

            <li class="sub-menu">
                <a href="javascript:;" <?php if ( $template->v('reports') ) echo 'class="active"'?>>
                    <i class="fa fa-bar-chart-o"></i>
                    <span>Reports</span>
                </a>
                <ul class="sub">
                    <li <?php if ( $template->v('reports/index') ) echo 'class="active"'?>><a href="/reports/">Search</a></li>
                    <li <?php if ( $template->v('reports/custom') ) echo 'class="active"'?>><a href="/reports/custom/">Custom</a></li>
                </ul>
            </li>

            <li class="sub-menu">
                <a href="javascript:;" <?php if ( $template->v('knowledge-base') ) echo 'class="active"'?>>
                    <i class="fa fa-book"></i>
                    <span>Knowledge Base</span>
                </a>
                <ul class="sub">
                    <li <?php if ( $template->v('knowledge-base/articles/index') ) echo 'class="active"'?>><a href="/knowledge-base/articles/?s=admin">View Articles</a></li>
                    <li <?php if ( $template->v('knowledge-base/articles/add') ) echo 'class="active"'?>><a href="/knowledge-base/articles/add-edit/?s=admin">Add Article</a></li>
                    <li <?php if ( $template->v('knowledge-base/pages/index') ) echo 'class="active"'?>><a href="/knowledge-base/pages/?s=admin">View Pages</a></li>
                    <li <?php if ( $template->v('knowledge-base/pages/add') ) echo 'class="active"'?>><a href="/knowledge-base/pages/add-edit/?s=admin">Add Page</a></li>
                    <li <?php if ( $template->v('knowledge-base/categories') ) echo 'class="active"'?>><a href="/knowledge-base/categories/?s=admin">Categories</a></li>
                </ul>
            </li>

            <?php if ( $user->has_permission( User::ROLE_ADMIN ) ): ?>
                <li class="sub-menu">
                    <a href="/api-keys/" <?php if ( $template->in_menu_item('api-keys') ) echo 'class="active"'?>>
                        <i class="fa fa-key"></i>
                        <span>API Keys</span>
                    </a>
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

