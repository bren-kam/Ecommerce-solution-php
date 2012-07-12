<?php

//include the class file
require('class.arb.php');




//---------------------------------------------------------------------------------
// ARBCreateSubscriptionRequest
//---------------------------------------------------------------------------------

// create instance of ARB
	$arb = new auth_arb();
// or can be passed with optional argument, to be used as the subscription name
	$arb = new auth_arb('Subscription Name');

// set the reference ID (optional)
	$arb->setReferenceID('sample'); // up to 20 characters

// set the recurrence interval
	$arb->setInterval(1,'Months'); // if omitted, default is 1 month

// set the amount of the recurring charge (required)
	$arb->setAmount("10.29");

// set the start date
	$arb->setStartDate('2008-07-01'); // if omitted, default is "today"

// set the number of occurrences
	$arb->setTotalOccurrences('12'); // if omitted, default is 9999(forever)

// set trial occurrences and value (optional)
	$arb->setTrial('1','0.00');

// set order details
// the second argument is optional.
// arguments are interchangable
// the numeric argument is used for the invoice number
// the text argument is used for the description
	$arb->setOrderDetails('description','1234');

// set customer ID (optional)
	$arb->setCustomerId('12354');

// set customer Phone (optional)
	$arb->setCustomerPhone('123-456-7890');

// set customer Fax (optional)
	$arb->setCustomerFax('123-456-7890');

// set customer Email (optional)
	$arb->setCustomerEmail('email@domain.com');

// set the billing name (required)
	$arb->setBillingName('John','Smith');
// or
	$arb->setBillingFirstName('John');
	$arb->setBillingLastName('Smith');

// set the billing address (optional)
	$arb->setBillingAddress('123 E Sesame St', 'Boston', 'MA', '12342');
// or
	$arb->setBillingAddress('123 E Sesame St');
	$arb->setBillingCity('Boston');
	$arb->setBillingState('MA'); //full state name can be used (i.e. Massachusetts)
	$arb->setBillingZip('12345');

// set the billing company (optional)
	$arb->setBillingCompany('My Company');

// set the billing country (optional)
	$arb->setBillingCountry('United States');

// set the shipping name (optional)
	$arb->setShippingName('John','Smith');
// or
	$arb->setShippingFirstName('John');
	$arb->setShippingLastName('Smith');

// set the shipping address (optional)
	$arb->setShippingAddress('123 E Sesame St', 'Boston', 'MA', '12342');
// or
	$arb->setShippingAddress('123 E Sesame St');
	$arb->setShippingCity('Boston');
	$arb->setShippingState('MA'); //full state name can be used (i.e. Massachusetts);
	$arb->setShippingZip('12345');

// set the shipping company (optional)
	$arb->setShippingCompany('My Company');

// set the shipping country (optional)
	$arb->setShippingCountry('United States');

// set the payment details (one of the two options is required)
// credit card
	$arb->setPaymentDetails('4242424242424242','2012-08');
// or echeck (account type, routing number, account number, name on account)
	$arb->setPaymentDetails('checking','123456789', '12345678901234567', 'John Smith');

// Submit the subscription request
	$arb->CreateSubscriptionRequest();

// Test and print results
if ($arb->success)
	print_r($arb->results);
else
	print_r($arb->error);











//---------------------------------------------------------------------------------
// ARBUpdateSubscriptionRequest
//---------------------------------------------------------------------------------

// create instance of ARB
	$arb = new auth_arb();
// or can be passed with optional argument, to be used as the subscription name
	$arb = new auth_arb('Subscription Name');

// set the reference ID (optional)
	$arb->setReferenceID('sample'); // up to 20 characters

// set the Subscription ID (required)
	//$arb->setSubscriptionId("123456");

// set the number of occurrences (optional)
	$arb->setTotalOccurrences('24'); // if omitted, no value will be passed to authorize

// set order details
// the second argument is optional.
// arguments are interchangable
// the numeric argument is used for the invoice number
// the text argument is used for the description
	$arb->setOrderDetails('description','1234');

// set customer ID (optional)
	$arb->setCustomerId('12354');

// set customer Phone (optional)
	$arb->setCustomerPhone('123-456-7890');

// set customer Fax (optional)
	$arb->setCustomerFax('123-456-7890');

// set customer Email (optional)
	$arb->setCustomerEmail('email@domain.com');

// set the billing name (optional)
	$arb->setBillingName('John','Smith');
// or
	$arb->setBillingFirstName('John');
	$arb->setBillingLastName('Smith');

// set the billing address (optional)
	$arb->setBillingAddress('123 E Sesame St', 'Boston', 'MA', '12342');
// or
	$arb->setBillingAddress('123 E Sesame St');
	$arb->setBillingCity('Boston');
	$arb->setBillingState('MA'); //full state name can be used (i.e. Massachusetts)
	$arb->setBillingZip('12345');

// set the billing company (optional)
	$arb->setBillingCompany('My Company');

// set the billing country (optional)
	$arb->setBillingCountry('United States');

// set the shipping name (optional)
	$arb->setShippingName('John','Smith');
// or
	$arb->setShippingFirstName('John');
	$arb->setShippingLastName('Smith');

// set the shipping address (optional)
	$arb->setShippingAddress('123 E Sesame St', 'Boston', 'MA', '12342');
// or
	$arb->setShippingAddress('123 E Sesame St');
	$arb->setShippingCity('Boston');
	$arb->setShippingState('MA'); //full state name can be used (i.e. Massachusetts)
	$arb->setShippingZip('12345');

// set the shipping company (optional)
	$arb->setShippingCompany('My Company');

// set the shipping country (optional)
	$arb->setShippingCountry('United States');

// set the payment details (optional)
// credit card
	$arb->setPaymentDetails('4242424242424242','2012-08');
// or echeck (account type, routing number, account number, name on account)
	$arb->setPaymentDetails('checking','123456789', '12345678901234567', 'John Smith');

// Submit the subscription request
	$arb->UpdateSubscriptionRequest();

// Test and print results
if ($arb->success)
	print_r($arb->results);
else
	print_r($arb->error);











//---------------------------------------------------------------------------------
// ARBCancelSubscriptionRequest
//---------------------------------------------------------------------------------

// create instance of ARB
	$arb = new auth_arb();

// set the reference ID (optional)
	$arb->setReferenceID('sample'); // up to 20 characters

// set the Subscription ID (required)
	//$arb->setSubscriptionId("123456");

// Submit the subscription request
	$arb->CancelSubscriptionRequest();

// Test and print results
if ($arb->success)
	print_r($arb->results);
else
	print_r($arb->error);










//---------------------------------------------------------------------------------
// Notes
//---------------------------------------------------------------------------------

// you must set your login id and transaction key in the head of the class
	const auth_net_login_id  = "your login id";
	const auth_net_tran_key  = "your transaction key";

// debugging can be enabled.  Debugging will print the XML request, and will not submit
// the request to authorize
	$arb->DEBUG=1;

// to retrieve the raw XML request and response use the following
	print $arb->request;
	print $arb->response;

?>