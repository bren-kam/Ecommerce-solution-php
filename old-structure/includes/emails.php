<?php
$email['sign_up'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Imagine Retailer Signup Confirmation</title>
<style type="text/css">
body { width: 800px; font-family:Arial, Helvetica, sans-serif; color:#616268; font-size:13px; margin: 15px auto; }
p { line-height: 21px; padding-bottom: 7px; }
h2{ padding:0; margin:0; }
td{ font-size: 13px; padding-right: 10px; }
li { padding-top: 7px; }
</style>
</head>
<body>
<img src="http://www.imagineretailer.com/images/imagine-retailer.png" width="314" height="48" alt="Imagine Retailer" />

<h1 align="center">Imagine Retailer Receipt</h1>
<p>Thank you for your order from Imagine Retailer! You have qualified for FREE Sign Up so your Total Setup Cost has been waived.</p>
<p style="margin-bottom:0;padding-bottom:0">You will be contacted by a Online Solution Specialist when construction begins on your new website. In the meantime, please contact us if you have any questions:</p>
<ul style="margin-top:0;padding-top:0">
	<li>Call: (800) 549-9206</li>
	<li>Email: <a href="mailto:info@imagineretailer.com" title="Email info@imagineretailer.com">info@imagineretailer.com</a></li>
	<li>Web: <a href="http://www.imagineretailer.com/" title="www.ImagineRetailer.com">www.imagineretailer.com</a></li>
</ul>
<br />
<p style="color:#CC2222;font-size:1.5em;font-weight:bold">Your credit card has been billed for the Total Monthly Cost &amp; Set Up Cost indicated below. You will automatically be billed the Monthly Cost each month.</p>
<br />

<h2>Order Information</h2>
<p style="margin-top:0;padding-top:0">
	<strong>Order Number:</strong> #[order_number]<br />
	<strong>Total Setup Price:</strong> $[setup_price]<br />
	<strong>Total Monthly Price:</strong> $[monthly_price]
</p>
<br />

<h2>Order Details</h2>
[order_information]
<br /><BR />

<h2>Account Information</h2>
<p style="margin-top:0;padding-top:0">[account_information]</p>
<br />

<h2>Billing Address</h2>
<p style="margin-top:0;padding-top:0">[billing_address]</p>
<br />

<p>
	Thank you again!<br />
	ImagineRetailer.com
</p>

<br />
<p><strong><small>Return/Refund/Cancellation Policy</small></strong></p>
<p><small>If you are unhappy with ImagineRetailer.com for any reason with at least 90 days written notice you can opt out of our agreement and there will be no further billing for services.</small></p>
<p><small>The service is billed in advance on a monthly basis and is non-refundable. There will be no refunds or credits for partial months of service, upgrade/downgrade refunds, or refunds for months unused with an open account. In order to treat everyone equally, no exceptions will be made.</small></p>
<br />

<p><strong><small>Delivery of Service</small></strong></p>
<p><small>Imagine Retailer, in its sole discretion, has the right to suspend or terminate your account and refuse any and all current or future use of the platform, or any other Imagine Retailer service, for any reason at any time. Such termination of the service will result in the deactivation or deletion of your Account or your access to your Account, and the forfeiture and relinquishment of all content in your Account. Imagine Retailer reserves the right to refuse service to anyone for any reason at any time.</small></p>
</body>
</html>';

// Reset Password emails
$email['reset-password']['text'] = "Hi {name},\n\nWe received a request to change your password. Use the link below to reset your password:\n\n{token_link}\n\n\n-- " . TITLE . " Team\n\n\nDidn't send this request? Your password has not been changed; please ignore this email.";
$email['reset-password']['subject'] = 'Reset Password Request';
$email['reset-password']['variables'] = array( '{name}', '{email}', '{token_link}' );
