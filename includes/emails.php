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
<p>Your credit card has been billed for the Total Monthly Cost indicated below. You will automatically be billed for this amount each month.</p>
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
</body>
</html>';
?>