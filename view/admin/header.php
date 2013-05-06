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

$resources->css_before( 'labels/' . DOMAIN, 'style' );
$resources->javascript( 'sparrow', 'jquery.notify', 'header' );

$template->set( 'section_' . format::slug( $template->v('section') ), ' class="selected"');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
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
		    <?php echo _('Welcome'), ' ', $user->contact_name; ?> | <a href="/logout/" title="<?php echo _('Log Out'); ?>"><?php echo _('Log out'); ?></a>
		<?php } ?>
        </div>
	</div>
	<div id="nav">
		<div id="nav-links">
            <a href="/accounts/" title="<?php echo _('Accounts'); ?>"<?php echo $template->v('section_accounts'); ?>><?php echo _('Accounts'); ?></a>
            <a href="/products/" title="<?php echo _('Products'); ?>"<?php echo $template->v('section_products'); ?>><?php echo _('Products'); ?></a>
            <?php if ( $user->has_permission( User::ROLE_ONLINE_SPECIALIST ) ) { ?>
                <a href="/users/" title="<?php echo _('Users'); ?>"<?php echo $template->v('section_users'); ?>><?php echo _('Users'); ?></a>
            <?php } ?>
            <a href="/checklists/" title="<?php echo _('Checklists'); ?>"<?php echo $template->v('section_checklists'); ?>><?php echo _('Checklists'); ?></a>
            <a href="/tickets/" title="<?php echo _('Tickets'); ?>"<?php echo $template->v('section_tickets'); ?>><?php echo _('Tickets'); ?></a>
            <?php if ( $user->has_permission( User::ROLE_ONLINE_SPECIALIST ) ) { ?>
                <a href="/reports/" title="<?php echo _('Reports'); ?>"<?php echo $template->v('section_reports'); ?>><?php echo _('Reports'); ?></a>
            <?php
            }

            if ( $user->has_permission( User::ROLE_ADMIN ) ) {
            ?>
                <a href="/knowledge-base/" title="<?php echo _('Knowledge Base'); ?>"<?php echo $template->v('section_knowledge-base'); ?>><?php echo _('Knowledge Base'); ?></a>
            <?php } ?>
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
