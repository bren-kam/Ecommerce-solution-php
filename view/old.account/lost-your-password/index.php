<?php
/**
 * @package Grey Suit Retail
 * @page Lost your Password
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 */
$resources->css_before( 'labels/' . 'greysuitretail.com', 'login' );
$resources->javascript( 'sparrow' );

$margin_bottom = ( 'greysuitretail' == DOMAIN ) ? '' : '20px';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo $template->v('title') . ' | ' . TITLE; ?></title>
<link type="text/css" rel="stylesheet" href="/resources/css/?f=<?php echo $resources->get_css_file(); ?>" />
<script type="text/javascript" src="/resources/js/?f=<?php echo $resources->get_javascript_file( 'head' ); ?>"></script>
<link rel="icon" href="<?php echo '/images/favicons/' . DOMAIN . '.ico'; ?>" type="image/x-icon" />
<?php $template->get_head(); ?>
</head>
<body>
<?php
$template->get_top();

if ( $success ) {
    ?>
    <div class="notification sticky hidden success">
        <p><?php echo $success; ?></p>
    </div>
<?php }

if ( $errs ) {
?>
<div class="notification sticky hidden error">
    <p><?php echo $errs; ?></p>
</div>
<?php } ?>
<div id="login-logo"<?php if ( !empty( $margin_bottom ) ) echo ' style="margin-bottom: ' . $margin_bottom . ' "'; ?>><img src="/images/logos/login/<?php echo DOMAIN; ?>.png" alt="<?php echo TITLE; ?>" /></div>
<div id="lost-your-password-wrapper">
    <form action="" name="fLostYourPassword" method="post">
        <input type="text" class="tb" name="email" placeholder="<?php echo _('Email'); ?>" value="<?php echo strip_tags( $template->v('email') ); ?>" maxlength="200" />
        <input type="submit" class="login-button float-right" value="<?php echo _('Recover'); ?>" />
        <br clear="both" />
        <?php nonce::field('index'); ?>
    </form>
    <?php echo $template->v('validation'); ?>
</div>
<div id="back-to-login">
    <p class="center"><a href="/login/" title="<?php echo _('Login'); ?>"><?php echo _('Login'); ?></a></p>
</div>
<!-- End: Footer -->
<script type="text/javascript">head.load( 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js', '/resources/js/?f=<?php echo $resources->get_javascript_file(); ?>');</script>
<?php $template->get_footer(); ?>
</body>
</html>