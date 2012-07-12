<?php
	
	// include the class file
	require('authorizenet.cim.class.php');
	
	
	
	/* 
	   Just copy and paste each example below here to test.
	   Start with createCustomerProfileRequest() and don't forget to save
	   any ID's that are outputed. You will need them for other methods.
	*/
	/////////////////////////////////////////////////////////////////////
	
	

	
	$cim = new AuthNetCim('54PB5egZ', '48V258vr55AE8tcg', true);
	
	// Merchant-assigned reference ID for the request
	$cim->setParameter('refId', 'my unique ref id'); // Up to 20 characters (optional)
	
	// Total Amount: This amount should include all other amounts such as tax amount, shipping amount, etc.
	$cim->setParameter('transaction_amount', '1.00'); // Up to 4 digits with a decimal (required)
	
	// This amount must be included in the total amount for the transaction. (optional)
	$cim->setParameter('tax_amount', '0.00'); // Up to 4 digits with a decimal point (no dollar symbol) (optional)
	$cim->setParameter('tax_name', 'my custom name'); // Up to 31 characters (optional)
	$cim->setParameter('tax_description', 'my custom description'); // Up to 255 characters (optional)
	
	// This amount must be included in the total amount for the transaction. (optional)
	$cim->setParameter('shipping_amount', '0.00'); // Up to 4 digits with a decimal point (no dollar symbol) (optional)
	$cim->setParameter('shipping_name', 'my custom name'); // Up to 31 characters (optional)
	$cim->setParameter('shipping_description', 'my custom description'); // Up to 255 characters (optional)
	
	// This amount must be included in the total amount for the transaction. (optional)
	$cim->setParameter('duty_amount', '0.00'); // Up to 4 digits with a decimal point (no dollar symbol) (optional)
	$cim->setParameter('duty_name', 'my custom name'); // Up to 31 characters (optional)
	$cim->setParameter('duty_description', 'my custom description'); // Up to 255 characters (optional)
	
	// LineItems: (Contains line item details about the order.) (optional)
	// Up to 30 distinct instances of this element may be included per transaction to describe items included in the order.
	// Below is an example of adding LineItems into a multidimensional array during a loop
	$LineItem = array();
	for ($i = 1; $i <= 2; $i++)
	{
		// The ID assigned to the item 
		$LineItem[$i]['itemId'] = '123456'; // Up to 31 characters
		// A short description of an item 
		$LineItem[$i]['name'] = 'custom item name'; // Up to 31 characters
		// A detailed description of an item 
		$LineItem[$i]['description'] = 'my custom description'; // Up to 255 characters
		// The quantity of an item 
		$LineItem[$i]['quantity'] = '1'; // Up to 4 digits (up to two decimal places)
		// Cost of an item per unit excluding tax, freight, and duty 
		$LineItem[$i]['unitPrice'] = '1.00'; // Up to 4 digits with a decimal point (no dollar symbol)
		// Indicates whether the item is subject to tax
		$LineItem[$i]['taxable'] = '0'; // Standard Boolean logic, 0=FALSE and 1=TRUE
	}
	$cim->LineItems = $LineItem;
	
	// transactionType = (profileTransCaptureOnly, profileTransAuthCapture or profileTransAuthOnly)
	$cim->setParameter('transactionType', 'profileTransAuthOnly'); // see options above
	
	// Payment gateway assigned ID associated with the customer profile
	$cim->setParameter('customerProfileId', '201196'); // Numeric (required)
	
	// Payment gateway assigned ID associated with the customer payment profile
	$cim->setParameter('customerPaymentProfileId', '204249'); // Numeric (required)
	
	// Payment gateway assigned ID associated with the customer shipping address (optional)
	// If the customer AddressId is not passed, shipping information will not be included with the transaction.
	$cim->setParameter('customerShippingAddressId', '98934'); // Numeric (optional)
	

	// Up to 20 characters (no symbols) (optional)
	$cim->setParameter('order_invoiceNumber', 'my order invoice id'); 
	// Up to 255 characters (no symbols) (optional)
	$cim->setParameter('order_description', 'my order description'); 
	// Up to 25 characters (no symbols) (optional)
	$cim->setParameter('order_purchaseOrderNumber', '1234'); 
	
	// The tax exempt status
	$cim->setParameter('transactionTaxExempt', 'false');
	
	// The recurring billing status
	$cim->setParameter('transactionRecurringBilling', 'false');
	
	// The customer's card code (the three- or four-digit number on the back or front of a credit card)
	// Required only when the merchant would like to use the Card Code Verification (CCV) filter
	$cim->setParameter('transactionCardCode', '123'); // (conditional)
	
	// The authorization code of an original transaction required for a Capture Only
	// This element is only required for the Capture Only transaction type.
	//$cim->setParameter('transactionApprovalCode', 'abc123'); // 6 characters only (conditional)
	
	$cim->createCustomerProfileTransactionRequest();
	
	
	
	
	/////////////////////////////////////////////////////////////////////
	
	/* This below will echo the responses for each method */
	
	if ($cim->isSuccessful())
	{
		echo "<br>".$cim->response;
		echo "YES<br>".$cim->directResponse;
		echo "<br>".$cim->validationDirectResponse;
		echo "<br>".$cim->resultCode;
		echo "<br>".$cim->code;
		echo "<br>".$cim->text;
		echo "<br>".$cim->refId;
		echo "<br>".$cim->customerProfileId;
		echo "<br>".$cim->customerPaymentProfileId;
		echo "<br>".$cim->customerAddressId;
	}
	else
	{
		echo "NO<br>".$cim->directResponse;
		echo "<br>".$cim->validationDirectResponse;
		echo "<br>".$cim->resultCode;
		echo "<br>".$cim->code;
		echo "<br>".$cim->text;
		echo "<br><pre>";
		print_r($cim->error_messages);
		echo "</pre>";
		
	}
	
	
	
	
	/*  USAGE OF ALL CLASS METHODS ARE BELOW  
	
		Below are all examples of how to use each method in this class.
		There can be more or less setParameter() options for some methods. 
		All of the possible parameters are included in each example. Your welcome :)
		Some parameters you may want and others not, it depends on how you are implementing it.
		
		I also built in error handling (which is about 70% of the code) to help catch required 
		elements for developers during testing and integration so you can see what is
		required when enabling some parameters. 
		
		When using createCustomerProfileTransactionRequest(),
		Personally, I recommend setting transactionType to "profileTransAuthOnly" and keep
		the test mode off (false) during testing, this way you can test how the api will truly react 
		during a real transaction without actually charging you. If done this way, then try to keep the
		authorization-only-transactions (testing) to a minimum to avoid getting your card automatically turned off,
		else, you will need to call in to get it turned back on (fraud detection). That was my case.
		Example:
		$cim->setParameter('transactionType', 'profileTransAuthOnly');
		
		Any suggestions or bug fixes, email: support(at)TrafficReGenerator.com

		Have fun coding!
		
		Read the manual for better understanding if needed:
		However, don't rely entirely on the manual for accuracy. 
                I have attached a README.txt to explain why.
		http://developer.authorize.net/guides/
		http://www.authorize.net/support/CIM_XML_guide.pdf
	*/
	
	
	
	/*
	
	// createCustomerProfileRequest()
	
	
	
	$cim = new AuthNetCim('54PB5egZ', '48V258vr55AE8tcg', false);
	
	// Choose a payment type - (creditCard or bankAccount) REQUIRED
	
	// creditCard payment method - (aka creditcard)
	//$cim->setParameter('paymentType', 'creditCard');
	//$cim->setParameter('cardNumber', '0000000000000000');
	//$cim->setParameter('expirationDate', '2010-01'); // (YYYY-MM)
	
	// bankAccount payment method - (aka echeck) 
	//$cim->setParameter('paymentType', 'bankAccount');
	//$cim->setParameter('accountType', 'checking'); // (checking, savings or businessChecking)
	//$cim->setParameter('nameOnAccount', 'Ray Solomon');
	//$cim->setParameter('echeckType', 'WEB'); // (CCD, PPD, TEL or WEB)
	//$cim->setParameter('bankName', 'Bank of America');
	//$cim->setParameter('routingNumber', '000000000');
	//$cim->setParameter('accountNumber', '0000000000000');
	
	// Some Billing address information is required and some is optional 
	// depending on your Address Verification Service (AVS) settings 
	$cim->setParameter('billTo_firstName', 'Ray'); // Up to 50 characters (no symbols)
	$cim->setParameter('billTo_lastName', 'Solomon'); // Up to 50 characters (no symbols)
	//$cim->setParameter('billTo_company', 'Acme, Inc.'); // Up to 50 characters (no symbols) (optional)
	$cim->setParameter('billTo_address', 'My Address'); // Up to 60 characters (no symbols)
	$cim->setParameter('billTo_city', 'My City'); // Up to 40 characters (no symbols)
	$cim->setParameter('billTo_state', 'AZ'); // A valid two-character state code (US only) (optional)
	$cim->setParameter('billTo_zip', '85282'); // Up to 20 characters (no symbols)
	$cim->setParameter('billTo_country', 'US'); // Up to 60 characters (no symbols) (optional)
	//$cim->setParameter('billTo_phoneNumber', '555-555-5555'); // Up to 25 digits (no letters) (optional)
	//$cim->setParameter('billTo_faxNumber', '444-444-4444'); // Up to 25 digits (no letters) (optional)
	
	// In this method, shipping information is required because it reduces an extra
	// step from having to create a shipping address in the future, therefore you can simply update it when needed.
	// You can populate it with the billing info if you don't have an order form with shipping details.
	$cim->setParameter('shipTo_firstName', 'James'); // Up to 50 characters (no symbols)
	$cim->setParameter('shipTo_lastName', 'Beistle'); // Up to 50 characters (no symbols)
	//$cim->setParameter('shipTo_company', 'Acme, Inc.'); // Up to 50 characters (no symbols) (optional)
	$cim->setParameter('shipTo_address', 'My Address'); // Up to 60 characters (no symbols)
	$cim->setParameter('shipTo_city', 'My City'); // Up to 40 characters (no symbols)
	$cim->setParameter('shipTo_state', 'AZ'); // A valid two-character state code (US only) (optional)
	$cim->setParameter('shipTo_zip', '85282'); // Up to 20 characters (no symbols)
	$cim->setParameter('shipTo_country', 'US'); // Up to 60 characters (no symbols) (optional)
	//$cim->setParameter('shipTo_phoneNumber', '555-555-5555'); // Up to 25 digits (no letters) (optional)
	//$cim->setParameter('shipTo_faxNumber', '444-444-4444'); // Up to 25 digits (no letters) (optional)
	
	// Merchant-assigned reference ID for the request
	//$cim->setParameter('refId', 'my unique ref id'); // Up to 20 characters (optional)
	
	// merchantCustomerId must be unique across all profiles
	$cim->setParameter('merchantCustomerId', 'my unique custid2'); // Up to 20 characters (optional)
	
	// description must be unique across all profiles, if defined
	//$cim->setParameter('description', 'My description'); // Up to 255 characters (optional)
	
	// A receipt from authorize.net will be sent to the email address defined here
	$cim->setParameter('email', 'support@trafficregenerator.com'); // Up to 255 characters (optional)
	
	$cim->setParameter('customerType', 'individual'); // individual or business (optional)
	
	$cim->createCustomerProfileRequest();
	
	
	*/
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	// createCustomerPaymentProfileRequest()
	
	
	
	$cim = new AuthNetCim('54PB5egZ', '48V258vr55AE8tcg', false);
	
	// Choose a payment type - (creditCard or bankAccount)
	
	// creditCard payment method - (aka creditcard) 
	//$cim->setParameter('paymentType', 'creditCard');
	//$cim->setParameter('cardNumber', '0000000000000000');
	//$cim->setParameter('expirationDate', '2010-01'); // (YYYY-MM)
	
	// bankAccount payment method - (aka echeck) 
	//$cim->setParameter('paymentType', 'bankAccount');
	//$cim->setParameter('accountType', 'checking'); // (checking, savings or businessChecking)
	//$cim->setParameter('nameOnAccount', 'Ray Solomon');
	//$cim->setParameter('echeckType', 'WEB'); // (CCD, PPD, TEL or WEB)
	//$cim->setParameter('bankName', 'Bank of America');
	//$cim->setParameter('routingNumber', '000000000');
	//$cim->setParameter('accountNumber', '0000000000000');
	
	// Some Billing address information is required and some is optional 
	// depending on your Address Verification Service (AVS) settings 
	$cim->setParameter('billTo_firstName', 'Ray'); // Up to 50 characters (no symbols)
	$cim->setParameter('billTo_lastName', 'Solomon'); // Up to 50 characters (no symbols)
	//$cim->setParameter('billTo_company', 'Acme, Inc.'); // Up to 50 characters (no symbols) (optional)
	$cim->setParameter('billTo_address', 'My Address'); // Up to 60 characters (no symbols)
	$cim->setParameter('billTo_city', 'My City'); // Up to 40 characters (no symbols)
	$cim->setParameter('billTo_state', 'AZ'); // A valid two-character state code (US only) (optional)
	$cim->setParameter('billTo_zip', '85282'); // Up to 20 characters (no symbols)
	$cim->setParameter('billTo_country', 'US'); // Up to 60 characters (no symbols) (optional)
	$cim->setParameter('billTo_phoneNumber', '666-666-6666'); // Up to 25 digits (no letters) (optional)
	$cim->setParameter('billTo_faxNumber', '555-555-5555'); // Up to 25 digits (no letters) (optional)
	
	$cim->setParameter('customerType', 'individual'); // individual or business (optional)
	
	// Payment gateway assigned ID associated with the customer profile
	$cim->setParameter('customerProfileId', '201196'); // Numeric (required)
	
	// Merchant-assigned reference ID for the request
	$cim->setParameter('refId', 'my unique ref id'); // Up to 20 characters (optional)
	
	//  if liveMode, the billing address gets verified according to AVS settings on your Authorize.net account
	$cim->setParameter('validationMode', 'liveMode'); // required (none, testMode or liveMode)
	
	$cim->createCustomerPaymentProfileRequest();
	
	*/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	// createCustomerShippingAddressRequest()
	
	
	
	$cim = new AuthNetCim('54PB5egZ', '48V258vr55AE8tcg', false);
	
	$cim->setParameter('shipTo_firstName', 'Ray'); // Up to 50 characters (no symbols)
	$cim->setParameter('shipTo_lastName', 'Solomon'); // Up to 50 characters (no symbols)
	$cim->setParameter('shipTo_company', 'Acme, Inc.'); // Up to 50 characters (no symbols) (optional)
	$cim->setParameter('shipTo_address', 'My Address'); // Up to 60 characters (no symbols)
	$cim->setParameter('shipTo_city', 'My City'); // Up to 40 characters (no symbols)
	$cim->setParameter('shipTo_state', 'AZ'); // A valid two-character state code (US only) (optional)
	$cim->setParameter('shipTo_zip', '85282'); // Up to 20 characters (no symbols)
	$cim->setParameter('shipTo_country', 'US'); // Up to 60 characters (no symbols) (optional)
	$cim->setParameter('shipTo_phoneNumber', '666-666-6666'); // Up to 25 digits (no letters) (optional)
	$cim->setParameter('shipTo_faxNumber', '555-555-5555'); // Up to 25 digits (no letters) (optional)
	
	// Payment gateway assigned ID associated with the customer profile
	$cim->setParameter('customerProfileId', '629'); // Numeric (required)
	
	// Merchant-assigned reference ID for the request
	$cim->setParameter('refId', 'my unique ref id'); // Up to 20 characters (optional)
	
	$cim->createCustomerShippingAddressRequest();
	
	*/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	// createCustomerProfileTransactionRequest()
	
	
	
	$cim = new AuthNetCim('54PB5egZ', '48V258vr55AE8tcg', false);
	
	// Merchant-assigned reference ID for the request
	$cim->setParameter('refId', 'my unique ref id'); // Up to 20 characters (optional)
	
	// Total Amount: This amount should include all other amounts such as tax amount, shipping amount, etc.
	$cim->setParameter('transaction_amount', '1.00'); // Up to 4 digits with a decimal (required)
	
	// This amount must be included in the total amount for the transaction. (optional)
	$cim->setParameter('tax_amount', '0.00'); // Up to 4 digits with a decimal point (no dollar symbol) (optional)
	$cim->setParameter('tax_name', 'my custom name'); // Up to 31 characters (optional)
	$cim->setParameter('tax_description', 'my custom description'); // Up to 255 characters (optional)
	
	// This amount must be included in the total amount for the transaction. (optional)
	$cim->setParameter('shipping_amount', '0.00'); // Up to 4 digits with a decimal point (no dollar symbol) (optional)
	$cim->setParameter('shipping_name', 'my custom name'); // Up to 31 characters (optional)
	$cim->setParameter('shipping_description', 'my custom description'); // Up to 255 characters (optional)
	
	// This amount must be included in the total amount for the transaction. (optional)
	$cim->setParameter('duty_amount', '0.00'); // Up to 4 digits with a decimal point (no dollar symbol) (optional)
	$cim->setParameter('duty_name', 'my custom name'); // Up to 31 characters (optional)
	$cim->setParameter('duty_description', 'my custom description'); // Up to 255 characters (optional)
	
	// LineItems: (Contains line item details about the order.) (optional)
	// Up to 30 distinct instances of this element may be included per transaction to describe items included in the order.
	// Below is an example of adding LineItems into a multidimensional array during a loop
	$LineItem = array();
	for ($i = 1; $i <= 2; $i++)
	{
		// The ID assigned to the item 
		$LineItem[$i]['itemId'] = '123456'; // Up to 31 characters
		// A short description of an item 
		$LineItem[$i]['name'] = 'custom item name'; // Up to 31 characters
		// A detailed description of an item 
		$LineItem[$i]['description'] = 'my custom description'; // Up to 255 characters
		// The quantity of an item 
		$LineItem[$i]['quantity'] = '1'; // Up to 4 digits (up to two decimal places)
		// Cost of an item per unit excluding tax, freight, and duty 
		$LineItem[$i]['unitPrice'] = '1.00'; // Up to 4 digits with a decimal point (no dollar symbol)
		// Indicates whether the item is subject to tax
		$LineItem[$i]['taxable'] = '0'; // Standard Boolean logic, 0=FALSE and 1=TRUE
	}
	$cim->LineItems = $LineItem;
	
	// transactionType = (profileTransCaptureOnly, profileTransAuthCapture or profileTransAuthOnly)
	$cim->setParameter('transactionType', 'profileTransAuthOnly'); // see options above
	
	// Payment gateway assigned ID associated with the customer profile
	$cim->setParameter('customerProfileId', '201196'); // Numeric (required)
	
	// Payment gateway assigned ID associated with the customer payment profile
	$cim->setParameter('customerPaymentProfileId', '204249'); // Numeric (required)
	
	// Payment gateway assigned ID associated with the customer shipping address (optional)
	// If the customer AddressId is not passed, shipping information will not be included with the transaction.
	$cim->setParameter('customerShippingAddressId', '98934'); // Numeric (optional)
	

	// Up to 20 characters (no symbols) (optional)
	$cim->setParameter('order_invoiceNumber', 'my order invoice id'); 
	// Up to 255 characters (no symbols) (optional)
	$cim->setParameter('order_description', 'my order description'); 
	// Up to 25 characters (no symbols) (optional)
	$cim->setParameter('order_purchaseOrderNumber', '1234'); 
	
	// The tax exempt status
	$cim->setParameter('transactionTaxExempt', 'false');
	
	// The recurring billing status
	$cim->setParameter('transactionRecurringBilling', 'false');
	
	// The customer's card code (the three- or four-digit number on the back or front of a credit card)
	// Required only when the merchant would like to use the Card Code Verification (CCV) filter
	$cim->setParameter('transactionCardCode', '123'); // (conditional)
	
	// The authorization code of an original transaction required for a Capture Only
	// This element is only required for the Capture Only transaction type.
	//$cim->setParameter('transactionApprovalCode', 'abc123'); // 6 characters only (conditional)
	
	$cim->createCustomerProfileTransactionRequest();
	
	*/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	// deleteCustomerProfileRequest()
	
	
	
	$cim = new AuthNetCim('54PB5egZ', '48V258vr55AE8tcg', false);
	
	// Merchant-assigned reference ID for the request
	$cim->setParameter('refId', 'my unique ref id'); // Up to 20 characters (optional)
	
	// Payment gateway assigned ID associated with the customer profile
	$cim->setParameter('customerProfileId', '626'); // Numeric (required)
	
	$cim->deleteCustomerProfileRequest();
	
	*/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	// deleteCustomerPaymentProfileRequest()
	
	
	
	$cim = new AuthNetCim('54PB5egZ', '48V258vr55AE8tcg', false);
	
	// Merchant-assigned reference ID for the request
	$cim->setParameter('refId', 'my unique ref id'); // Up to 20 characters (optional)
	
	// Payment gateway assigned ID associated with the customer profile
	$cim->setParameter('customerProfileId', '626'); // Numeric (required)
	
	// Payment gateway assigned ID associated with the customer payment profile
	$cim->setParameter('customerPaymentProfileId', '126'); // Numeric (required)
	
	$cim->deleteCustomerPaymentProfileRequest();
	
	*/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	// deleteCustomerShippingAddressRequest()
	
	
	
	$cim = new AuthNetCim('54PB5egZ', '48V258vr55AE8tcg', false);
	
	// Merchant-assigned reference ID for the request
	$cim->setParameter('refId', 'my unique ref id'); // Up to 20 characters (optional)
	
	// Payment gateway assigned ID associated with the customer profile
	$cim->setParameter('customerProfileId', '626'); // Numeric (required)
	
	// Payment gateway assigned ID associated with the customer shipping address
	$cim->setParameter('customerAddressId', '564'); // Numeric (required)
	
	$cim->deleteCustomerShippingAddressRequest();
	
	*/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	// getCustomerProfileRequest()
	
	
	
	$cim = new AuthNetCim('54PB5egZ', '48V258vr55AE8tcg', false);
	
	// Payment gateway assigned ID associated with the customer profile
	$cim->setParameter('customerProfileId', '626'); // Numeric (required)
	
	$cim->getCustomerProfileRequest();
	
	
	
	*/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	// getCustomerPaymentProfileRequest()
	
	
	
	// many more elements are returned as defined in the manual, just parse the xml response
	
	$cim = new AuthNetCim('54PB5egZ', '48V258vr55AE8tcg', false);
	

	// Payment gateway assigned ID associated with the customer profile
	$cim->setParameter('customerProfileId', '626'); // Numeric (required)
	
	// Payment gateway assigned ID associated with the customer payment profile
	$cim->setParameter('customerPaymentProfileId', '458'); // Numeric (required)
	
	$cim->getCustomerPaymentProfileRequest();
	
	*/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	// getCustomerShippingAddressRequest()
	
	
	
	$cim = new AuthNetCim('54PB5egZ', '48V258vr55AE8tcg', false);
	
	// Payment gateway assigned ID associated with the customer profile
	$cim->setParameter('customerProfileId', '626'); // Numeric (required)
	
	// Payment gateway assigned ID associated with the customer shipping address
	$cim->setParameter('customerAddressId', '564'); // Numeric (required)
	
	$cim->getCustomerShippingAddressRequest();
	
	*/
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	// updateCustomerProfileRequest()
	
	
	
	$cim = new AuthNetCim('54PB5egZ', '48V258vr55AE8tcg', false);
	
	// Merchant-assigned reference ID for the request
	$cim->setParameter('refId', 'my unique ref id'); // Up to 20 characters (optional)
	
	// Merchant assigned ID for the customer
	$cim->setParameter('merchantCustomerId', 'cust_username'); // (optional)
	
	// Description of the customer or customer profile
	$cim->setParameter('description', 'This is my new description'); // (optional)
	
	// Email address associated with the customer profile
	$cim->setParameter('email', 'email@ddress.com'); // (optional)
	
	// Payment gateway assigned ID associated with the customer profile
	$cim->setParameter('customerProfileId', '626'); // Numeric (required)
	
	$cim->updateCustomerProfileRequest();
	
	*/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	// updateCustomerPaymentProfileRequest()
	
	
	
	$cim = new AuthNetCim('54PB5egZ', '48V258vr55AE8tcg', false);
	
	// Choose a payment type - (creditCard or bankAccount) REQUIRED
	
	// creditCard payment method - (aka creditcard) 
	$cim->setParameter('paymentType', 'creditCard');
	$cim->setParameter('cardNumber', '0000000000000000');
	$cim->setParameter('expirationDate', '2010-01'); // (YYYY-MM)
	
	// bankAccount payment method - (aka echeck) 
	//$cim->setParameter('paymentType', 'bankAccount');
	//$cim->setParameter('accountType', 'checking'); // (checking, savings or businessChecking)
	//$cim->setParameter('nameOnAccount', 'Ray Solomon');
	//$cim->setParameter('echeckType', 'WEB'); // (CCD, PPD, TEL or WEB)
	//$cim->setParameter('bankName', 'Bank of America');
	//$cim->setParameter('routingNumber', '000000000');
	//$cim->setParameter('accountNumber', '0000000000000');
	
	
	// Some Billing address information is required and some is optional 
	// depending on your Address Verification Service (AVS) settings 
	$cim->setParameter('billTo_firstName', 'Ray'); // Up to 50 characters (no symbols)
	$cim->setParameter('billTo_lastName', 'Solomon'); // Up to 50 characters (no symbols)
	$cim->setParameter('billTo_company', 'Acme, Inc.'); // Up to 50 characters (no symbols) (optional)
	$cim->setParameter('billTo_address', 'My Address'); // Up to 60 characters (no symbols)
	$cim->setParameter('billTo_city', 'My City'); // Up to 40 characters (no symbols)
	$cim->setParameter('billTo_state', 'AZ'); // A valid two-character state code (US only) (optional)
	$cim->setParameter('billTo_zip', '85282'); // Up to 20 characters (no symbols)
	$cim->setParameter('billTo_country', 'US'); // Up to 60 characters (no symbols) (optional)
	$cim->setParameter('billTo_phoneNumber', '666-666-6666'); // Up to 25 digits (no letters) (optional)
	$cim->setParameter('billTo_faxNumber', '555-555-5555'); // Up to 25 digits (no letters) (optional)
	
	// Merchant-assigned reference ID for the request
	$cim->setParameter('refId', 'my unique ref id'); // Up to 20 characters (optional)
	
	//$cim->setParameter('customerType', 'individual'); // individual or business (optional)
	
	// Payment gateway assigned ID associated with the customer profile
	$cim->setParameter('customerProfileId', '201196'); // Numeric (required)
	
	// Payment gateway assigned ID associated with the customer payment profile
	$cim->setParameter('customerPaymentProfileId', '204249'); // Numeric (required)
	
	$cim->updateCustomerPaymentProfileRequest();
	
	*/
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	// updateCustomerShippingAddressRequest()
	
	
	
	$cim = new AuthNetCim('54PB5egZ', '48V258vr55AE8tcg', false);
	
	$cim->setParameter('shipTo_firstName', 'Ray'); // Up to 50 characters (no symbols)
	$cim->setParameter('shipTo_lastName', 'Solomon'); // Up to 50 characters (no symbols)
	$cim->setParameter('shipTo_company', 'Acme, Inc.'); // Up to 50 characters (no symbols) (optional)
	$cim->setParameter('shipTo_address', 'My Address'); // Up to 60 characters (no symbols)
	$cim->setParameter('shipTo_city', 'My City'); // Up to 40 characters (no symbols)
	$cim->setParameter('shipTo_state', 'AZ'); // A valid two-character state code (US only) (optional)
	$cim->setParameter('shipTo_zip', '85282'); // Up to 20 characters (no symbols)
	$cim->setParameter('shipTo_country', 'US'); // Up to 60 characters (no symbols) (optional)
	$cim->setParameter('shipTo_phoneNumber', '666-666-6666'); // Up to 25 digits (no letters) (optional)
	$cim->setParameter('shipTo_faxNumber', '555-555-5555'); // Up to 25 digits (no letters) (optional)
	
	// Merchant-assigned reference ID for the request
	$cim->setParameter('refId', 'my unique ref id'); // Up to 20 characters (optional)
	
	// Payment gateway assigned ID associated with the customer profile
	$cim->setParameter('customerProfileId', '201196'); // Numeric (required)
	
	// Payment gateway assigned ID associated with the customer shipping address
	$cim->setParameter('customerAddressId', '98934'); // Numeric (required)
	
	$cim->updateCustomerShippingAddressRequest();
	
	*/
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/*
	// validateCustomerPaymentProfileRequest()
	
	
	
	$cim = new AuthNetCim('54PB5egZ', '48V258vr55AE8tcg', false);
	
	// Payment gateway assigned ID associated with the customer profile
	$cim->setParameter('customerProfileId', '626'); // Numeric (required)
	
	// Payment gateway assigned ID associated with the customer payment profile
	$cim->setParameter('customerPaymentProfileId', '458'); // Numeric (required)
	
	// Payment gateway assigned ID associated with the customer shipping address
	// If the customer AddressId is not passed, shipping information will not be included with the transaction.
	// customerShippingAddressId used the same value of customerAddressId (not specified in the manual)
	$cim->setParameter('customerShippingAddressId', '564'); // Numeric (optional)
	
	// Indicates the processing mode for the request (if live, the billing/shipping address gets verified)
	$cim->setParameter('validationMode', 'liveMode'); // testMode or liveMode
	
	$cim->validateCustomerPaymentProfileRequest();
	
	*/

?>