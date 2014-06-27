<?php
/**
 * @package Grey Suit Retail
 * @page Login
 *
 * Declare the variables we have available from other sources
 * @var Resources $resources
 * @var Template $template
 * @var User $user
 * @var string $errs
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
    <link href="/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="/public/css/bootstrap-reset.css" rel="stylesheet">
    <!--external css-->
    <link href="/public/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="/public/css/style.css" rel="stylesheet">
    <link href="/public/css/style-responsive.css" rel="stylesheet" />

    <!-- Bootstrap Validator -->
    <link href="/public/assets/bootstrap-validator/css/bootstrapValidator.min.css" rel="stylesheet" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="/public/js/html5shiv.js"></script>
    <script src="/public/js/respond.min.js"></script>
    <![endif]-->
</head>

<body class="login-body">

<div class="container">

    <?php if ( $errs ) { ?>
        <div class="alert alert-danger">
            <?php echo $errs; ?>
        </div>
    <?php } ?>

    <form id="form-signin" class="form-signin" action="" method="post">

        <h2 class="form-signin-heading">sign in now</h2>
        <img src="/images/logos/login/<?php echo DOMAIN; ?>.png" alt="<?php echo TITLE; ?>" />
        <div class="login-wrap">
            <div class="form-group">
                <input type="text" name="email" class="form-control" placeholder="Email" autofocus data-bv-notempty data-bv-notempty-message="Email is required" data-bv-emailaddress data-bv-emailaddress-message="A valid email address is required">
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password" data-bv-notempty data-bv-notempty-message="Password is required">
            </div>
            <label class="checkbox">
                <input type="checkbox" value="remember-me"> Remember me
            </label>
            <button class="btn btn-lg btn-login btn-block" type="submit">Sign in</button>
        </div>
        <?php nonce::field('index'); ?>
    </form>

</div>


<!-- js placed at the end of the document so the pages load faster -->
<script src="/public/js/jquery.js"></script>
<script src="/public/js/bootstrap.min.js"></script>
<script src="/public/assets/bootstrap-validator/js/bootstrapValidator.min.js"></script>
<script>
    jQuery(function(){
        $('#form-signin').bootstrapValidator();
    });
</script>

</body>
</html>
