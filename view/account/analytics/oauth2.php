<?php
/**
 * Created by PhpStorm.
 * User: gbrunacci
 * Date: 14/10/14
 * Time: 15:36
 * @var string $login_url
 */
?>

<div class="row-fluid">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                Log In into Google Analytics
            </header>

            <div class="panel-body">

                <p>Your Website’s analytics are directly integrated with Google Analytics.  To view your analytics directly within your dashboard, you will need to login to Google with the Google email address and password shown below.</p>

                <ul>
                    <li>Email Address: <?php echo $ga_username ?></li>
                    <li>Password: <?php echo $ga_password ?></li>
                </ul>

                <p><strong>Note, please ensure you have logged out of your personal Google account before proceeding with the below steps</strong></p>

                <p>1. Click the "Log in to Google" button shown below.</p>

                <p>
                    <a href="<?php echo $login_url ?>" class="btn btn-primary" id="login-popup-link">Log in to Google</a>
                </p>

                <div id="step-2-short">
                    <p>2. A popup box will appear prompting you to log in to Google. You will need to login with the email address and password provided on this page: <a href="javascript:;" id="show-step-2-long">(Already logged in?)</a></p>
                    <p class="text-center"><img src="/images/analytics/oauth_2c.png" /></p>
                </div>

                <div id="step-2-long" class="hidden">
                    <p>2a. A popup box will appear . Click the email address in the top right and then "Add Account", as shown below:</p>
                    <p class="text-center"><img src="/images/analytics/oauth_2a.png" /></p>

                    <p>2b. You will then see a screen that says "Add Account", click the add account:</p>
                    <p class="text-center"><img src="/images/analytics/oauth_2b.jpg" /></p>

                    <p>2c. A popup box will appear prompting you to log in to Google. You will need to login with the email address and password provided on this page: <a href="javascript:;" id="show-step-2-short">(Not logged in?)</a></p>
                    <p class="text-center"><img src="/images/analytics/oauth_2c.png" /></p>
                </div>

                <p>3. After entering the email address and password into the popup – to login to Google – you will see a screen asking to grant access to your analytics. Click “Accept” in the bottom right:</p>
                <p class="text-center"><img src="/images/analytics/oauth_3.png" /></p>

                <p><strong>Note, in some rare instances you may be required to verify the account by phone.  In this case Google will display a page asking for your phone number.  After you’ve submitted your phone number you will have the option to select ‘Text’ or ‘Voice’ call and they will send you a code to enter in to verify your identity. After these steps are complete, Google Analytics will be accessible from your dashboard.</strong></p>
            </div>
        </section>
    </div>
</div>