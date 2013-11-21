<?php
/**
 * @package Grey Suit Retail
 * @page Login
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

if ( $errs ) {
?>
<div class="notification sticky hidden error">
    <p><?php echo $errs; ?></p>
</div>
<?php } ?>
<div id="login-logo"<?php if ( !empty( $margin_bottom ) ) echo ' style="margin-bottom: ' . $margin_bottom . ' "'; ?>><img src="/images/logos/login/<?php echo DOMAIN; ?>.png" alt="<?php echo TITLE; ?>" /></div>
<div id="login">
    <form action="" name="fLogin" method="post">
        <input type="text" class="tb" name="email" placeholder="<?php echo _('Email'); ?>" value="<?php echo strip_tags( $template->v('email') ); ?>" maxlength="200" />
        <input type="password" class="tb" name="password" placeholder="<?php echo _('Password'); ?>" autocomplete="off" maxlength="30" />
        <input type="submit" class="login-button float-right" value="<?php echo _('Login'); ?>" />
        <p id="remember-me"><input type="checkbox" class="cb" name="remember-me" id="cbRememberMe" value="1"<?php if ( '1' == $template->v('remember-me') ) echo ' checked="checked"'; ?> /> <label for="cbRememberMe"><?php echo _('Remember Me'); ?></label></p>
        <br clear="both" />
        <?php nonce::field('index'); ?>
    </form>
    <?php echo $template->v('validation'); ?>
</div>
<div id="lost-your-password">
    <?php /*<p class="center"><a href="/lost-your-password/" title="<?php echo _('Lost your password?'); ?>"><?php echo _('Lost your password?'); ?></a></p>*/ ?>
</div>
<!-- End: Footer -->
<script type="text/javascript">head.load( 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js', '/resources/js/?f=<?php echo $resources->get_javascript_file(); ?>');</script>
<?php $template->get_footer(); ?>
</body>
</html>