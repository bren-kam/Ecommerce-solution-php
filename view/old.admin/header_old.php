<?php
/**
 * @package Grey Suit Retail
 * @page Header
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var KnowledgeBaseArticle[] $kbh_articles
 */

$resources->css_before( 'labels/' . DOMAIN, 'redactor', 'style');
$resources->javascript( 'sparrow', 'jquery.notify', 'header' );

$template->set( 'section_' . format::slug( $template->v('section') ), ' selected');
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
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/headjs/1.0.2/head.load.min.js"></script>
    <script type="text/javascript" src="//code.jquery.com/jquery-1.9.1.min.js"></script>
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
		    <?php echo _('Welcome'), ' ', $user->contact_name; ?> | <a href="/logout/" title="<?php echo _('Log Out'); ?>"><?php echo _('Log out'); ?></a>
		<?php } ?>
        </div>
	</div>
	<div id="nav">
		<div id="nav-links">
            <a href="/accounts/" title="<?php echo _('Accounts'); ?>" class="nav-link<?php echo $template->v('section_accounts'); ?>"><?php echo _('Accounts'); ?></a>
            <?php if ( $user->has_permission( User::ROLE_ONLINE_SPECIALIST ) ) { ?>
                <a href="/products/" title="<?php echo _('Products'); ?>" class="nav-link<?php echo $template->v('section_products'); ?>"><?php echo _('Products'); ?></a>
                <?php if ( $user->has_permission( User::ROLE_ONLINE_SPECIALIST ) ) { ?>
                    <a href="/users/" title="<?php echo _('Users'); ?>" class="nav-link<?php echo $template->v('section_users'); ?>"><?php echo _('Users'); ?></a>
                <?php } ?>
                <a href="/checklists/" title="<?php echo _('Checklists'); ?>" class="nav-link<?php echo $template->v('section_checklists'); ?>"><?php echo _('Checklists'); ?></a>
                <a href="/tickets/" title="<?php echo _('Tickets'); ?>" class="nav-link<?php echo $template->v('section_tickets'); ?>"><?php echo _('Tickets'); ?></a>
                <?php if ( $user->has_permission( User::ROLE_ONLINE_SPECIALIST ) ) { ?>
                    <a href="/reports/" title="<?php echo _('Reports'); ?>" class="nav-link<?php echo $template->v('section_reports'); ?>"><?php echo _('Reports'); ?></a>
                <?php
                }
            }

            if ( $user->has_permission( User::ROLE_ADMIN ) ) {
            ?>
                <a href="/knowledge-base/" title="<?php echo _('Knowledge Base'); ?>" class="nav-link<?php echo $template->v('section_knowledge-base'); ?>"><?php echo _('Knowledge Base'); ?></a>
            <?php } ?>
            <div id="nav-right">
                <div id="support">
                    <a href="#" id="aSupport" title="<?php echo _('Support'); ?>" class="nav-link"><?php echo _('Support'); ?></a>
                    <div id="support-drop-down" class="hidden">
                        <a href="#" id="aTicket" title="<?php echo _('Support'); ?>" class="first top"><?php echo _('Support Request'); ?></a>
                        <a href="/kb/" title="<?php echo _('Knowledge Base'); ?>" class="top"><?php echo _('Knowledge Base'); ?></a>
                        <?php
                        if ( !empty( $kbh_articles ) ) {
                            $article_count = count( $kbh_articles );
                            $i = 0;

                            foreach ( $kbh_articles as $kbh_article ) {
                                $i++;
                                $class = '';

                                if ( 1 == $i ) {
                                    $class = ' first';
                                } elseif( $i == $article_count ) {
                                    $class = ' last';
                                }
                            ?>
                            <a href="<?php echo url::add_query_arg( 'aid', $kbh_article->id, '/kb/article/' ); ?>" title="<?php echo $kbh_article->title; ?>" class="article<?php echo $class; ?>" target="_blank"><?php echo $kbh_article->title; ?></a>
                            <?php
                            }
                        }
                        ?>
                        <a href="/kb/browser/" title="<?php echo _('Browser Support'); ?>" class="last top"><?php echo _('Browser Support'); ?></a>
                    </div>
                </div>
                </div>
            </div>
		</div>
	</div>