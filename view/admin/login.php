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
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="/resources/css_single/?f=bootstrap-reset" />

    <!--external css-->
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet" />

    <!-- Custom styles for this template -->
    <link type="text/css" rel="stylesheet" href="/resources/css_single/?f=style" />
    <link type="text/css" rel="stylesheet" href="/resources/css_single/?f=style-responsive" />

    <!-- Bootstrap Validator -->
    <link type="text/css" rel="stylesheet" href="/resources/css_single/?f=bootstrapValidator" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
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
        <div class="login-wrap">
            <div class="form-group">
                <input type="text" name="email" class="form-control" placeholder="Email" autofocus data-bv-notempty data-bv-notempty-message="Email is required" data-bv-emailaddress data-bv-emailaddress-message="A valid email address is required">
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password" data-bv-notempty data-bv-notempty-message="Password is required">
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" value="remember-me"> Remember me
                </label>
            </div>
            <button class="btn btn-lg btn-login btn-block" type="submit">Sign in</button>
        </div>
        <?php nonce::field('index'); ?>
    </form>

</div>


<!-- js placed at the end of the document so the pages load faster -->
<script src="//code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="//cdn.jsdelivr.net/jquery.bootstrapvalidator/0.4.5/js/bootstrapValidator.min.js"></script>
<script src="/resources/js/?f=<?php echo $resources->get_javascript_file(); ?>"></script>
</body>
</html>
