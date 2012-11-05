<?php

/**
 * PHP class for interaction with Authorize.net Customer Information Manager (CIM)
 *
 * Developed using PHP 5 (http://www.php.net/) and cURL (http://www.php.net/curl)
 * Tested on PHP versions 4.3.11 and 5.2.5
 *
 * Version 1.3 June 6, 2008
 * Copyright (c) 2007-2008 Website Hosting & Development (http://www.bigdoghost.com)
 * Download Page: http://www.bigdoghost.com/blog/authorizenet-cim/
 * For assistance please contact: 
 * Ray Solomon <support(at)TrafficReGenerator.com> or Josh <josh(at)instantincrease.com>
 * License: http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public License (LGPL)
 *
 * Please keep this header information here
 *
 *
 *
 * Changelog:
 *
 * -- June 6, 2008 - Version 1.3
 * Some misc updates to the php class because of recent changes made to the Authorize.net CIM API.
 * The only change that will affect users of this class is the removal of the Wells Fargo SecureSource implementation.
 * Attached a README.txt file that explains various inconsistencies in the Authorize.net CIM API manual.
 * Added $version and $responseDelimiter variables in the class. Make sure the $responseDelimiter is correct(read below).
 * Updated regex for a few elements that required a dollar amount in the correct format.
 * Please check for updates on our website often.
 *
 * -- March 11, 2008
 * Changed license to GNU LGPL version 3 so this class can be used with proprietary applications.
 * The previous license was GNU GPL version 2.
 *
 * -- January 19, 2008 - Version 1.2
 * This mainly a bug fix release with minor feature enhancements
 * Error handling logic for some functions were incorrect and is now fixed.
 * refId element is now implemented.
 * Wells Fargo SecureSource eCheck.Net implementation was incorrect and is now fixed.
 * echeck (bankAccount element) implementation was incorrect and is now fixed.
 * getCustomerShippingAddress() was modified and renamed to getCustomerShippingAddressRequest().
 * order_purchaseOrderNumber() had and incorrect element name and is now fixed.
 * Some examples were incorrect and is now fixed.
 * Added better examples for each method with full details and all possible parameters.
 * Added Godaddy proxy option for curl. Uncomment the code in the process() function if needed.
 * 
 * -- January 03, 2008 - Version 1.1 
 * This is mainly a bug fix release
 * Some regular expression patterns were incorrect and is now fixed.
 * 
 * -- Dec 20, 2007 - Version 1.0
 * initial release
 *
 * -end of changelog-
 *
 * Notes:
 * 
 * To aid during testing and integration, I added basic error handling to this class so you 
 * can use print_r($cim->error_messages); to see what parameters are required for each method.
 * After you understand what is required and what is optional, then this will prove useful.
 * 
 * In case some of you don't understand why I made the billing and shipping required in
 * createCustomerProfileRequest(). If you don't include shipping information when using 
 * that function, then you will need to create it later anyway because some methods required a 
 * "customerAddressId". Therefore it is wise to just include shipping info in the beginning,
 * that way authorize.net will generate a "customerAddressId" for you to use.
 * 
 * 
 * merchantCustomerId or description is required in these following methods even though the manual 
 * states that each is optional: updateCustomerProfileRequest() and createCustomerProfileRequest().
 *
 */




/*error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_startup_errors','1');
ini_set('display_errors','1');
*/

class AuthNetCim {
	
	var $version = '1.3'; // the code revision number for this class
	var $params = array();
	var $LineItems = array();
	var $success = false;
	var $error = true;
	var $error_messages = array();
	var $response;
	var $xml;
	var $update = false;
	var $resultCode;
	var $code;
	var $text;
	var $refId;
	var $customerProfileId;
	var $customerPaymentProfileId;
	var $customerAddressId;

	var $directResponse;
	var $validationDirectResponse;
	var $responseDelimiter = '|'; // Direct Response Delimiter. 
        // Make sure this value is the same in your Authorize.net login area.
        // Account->Settings->Transaction Format Settings->Direct Response

	function AuthNetCim($login, $transkey, $test_mode)
	{
		$this->login = $login;
		$this->transkey = $transkey;
		$this->test_mode = $test_mode;
		
		$subdomain = ($this->test_mode) ? 'apitest' : 'api';
		$this->url = "https://" . $subdomain . ".authorize.net/xml/v1/request.api";
	}
	
	function process($retries = 3)
	{
		// before we make a connection, lets check if there are basic validation errors
		if (count($this->error_messages) == 0)
		{
			$count = 0;
			while ($count < $retries)
			{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $this->xml);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				// proxy option for godaddy hosted customers (required)
				//curl_setopt($ch, CURLOPT_PROXY,"http://proxy.shr.secureserver.net:3128");
				$this->response = curl_exec($ch);
				$this->parseResults();
				
				if ($this->resultCode == "Ok")
				{
					$this->success = true;
					$this->error = false;
					 break;
				}
				else
				{
					$this->success = false;
					$this->error = true;
					break;
				}
				
				$count++;
			}
		
			curl_close($ch);
		}
		else
		{
			$this->success = false;
			$this->error = true;
		}
	}
	
	
	// This function is used to create a new customer profile along with any 
	// customer payment profiles and customer shipping addresses for the customer profile.
	function createCustomerProfileRequest() {
	$this->xml = "<?xml version='1.0' encoding='utf-8'?>
	<createCustomerProfileRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
	<merchantAuthentication>
		<name>" . $this->login . "</name>
		<transactionKey>" . $this->transkey . "</transactionKey>
	</merchantAuthentication>
	" . $this->refId() . "
	<profile>
		" . $this->merchantCustomerId() . "
		" . $this->description() . "
		" . $this->email() . "
		<paymentProfiles>
			" . $this->customerType() . "
			<billTo>
				" . $this->billTo_firstName() . "
				" . $this->billTo_lastName() . "
				" . $this->billTo_company() . "
				" . $this->billTo_address() . "
				" . $this->billTo_city() . "
				" . $this->billTo_state() . "
				" . $this->billTo_zip() . "
				" . $this->billTo_country() . "
				" . $this->billTo_phoneNumber() . "
				" . $this->billTo_faxNumber() . "
			</billTo>
			<payment>
				" . $this->paymentType() . "
			</payment>
		</paymentProfiles>
		<shipToList>
			" . $this->shipTo_firstName() . "
			" . $this->shipTo_lastName() . "
			" . $this->shipTo_company() . "
			" . $this->shipTo_address() . "
			" . $this->shipTo_city() . "
			" . $this->shipTo_state() . "
			" . $this->shipTo_zip() . "
			" . $this->shipTo_country() . "
			" . $this->shipTo_phoneNumber() . "
			" . $this->shipTo_faxNumber() . "
		</shipToList>
	</profile>
	</createCustomerProfileRequest>";
	$this->process();
	}
  
	// This function is used to create a new customer payment profile for an existing customer profile
	function createCustomerPaymentProfileRequest() {
	$this->xml = "<?xml version='1.0' encoding='utf-8'?>
	<createCustomerPaymentProfileRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
	<merchantAuthentication>
		<name>" . $this->login . "</name>
		<transactionKey>" . $this->transkey . "</transactionKey>
	</merchantAuthentication>
	" . $this->refId() . "
	" . $this->customerProfileId() . "
	<paymentProfile>
		" . $this->customerType() . "
		<billTo>
			" . $this->billTo_firstName() . "
			" . $this->billTo_lastName() . "
			" . $this->billTo_company() . "
			" . $this->billTo_address() . "
			" . $this->billTo_city() . "
			" . $this->billTo_state() . "
			" . $this->billTo_zip() . "
			" . $this->billTo_country() . "
			" . $this->billTo_phoneNumber() . "
			" . $this->billTo_faxNumber() . "
		</billTo>
		<payment>
			" . $this->paymentType() . "
		</payment>
	</paymentProfile>
	" . $this->validationMode() . "
	</createCustomerPaymentProfileRequest>";
	$this->process();
	
	}
	
	// This function is used to create a new customer shipping address for an existing customer profile
	function createCustomerShippingAddressRequest() {
	$this->xml = "<?xml version='1.0' encoding='utf-8'?>
	<createCustomerShippingAddressRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
	<merchantAuthentication>
		<name>" . $this->login . "</name>
		<transactionKey>" . $this->transkey . "</transactionKey>
	</merchantAuthentication>
	" . $this->refId() . "
	" . $this->customerProfileId() . "
	<address>
		" . $this->shipTo_firstName() . "
		" . $this->shipTo_lastName() . "
		" . $this->shipTo_company() . "
		" . $this->shipTo_address() . "
		" . $this->shipTo_city() . "
		" . $this->shipTo_state() . "
		" . $this->shipTo_zip() . "
		" . $this->shipTo_country() . "
		" . $this->shipTo_phoneNumber() . "
		" . $this->shipTo_faxNumber() . "
	</address>
	</createCustomerShippingAddressRequest>";
	$this->process();
	}
  
	// This function is used to create a payment transaction from an existing customer profile
	function createCustomerProfileTransactionRequest() {
	$this->xml = "<?xml version='1.0' encoding='utf-8'?>
	<createCustomerProfileTransactionRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
	<merchantAuthentication>
		<name>" . $this->login . "</name>
		<transactionKey>" . $this->transkey . "</transactionKey>
	</merchantAuthentication>
	" . $this->refId() . "
	<transaction>
		<" . $this->transactionType() . ">
			" . $this->transaction_amount() . "
			" . $this->transactionTax() . "
			" . $this->transactionShipping() . "
			" . $this->transactionDuty() . "
			" . $this->transactionLineItems() . "
			" . $this->customerProfileId() . "
			" . $this->customerPaymentProfileId() . "
			" . $this->customerShippingAddressId() . "
			" . $this->transactionOrder() . "
			" . $this->transactionTaxExempt() . "
			" . $this->transactionRecurringBilling() . "
			" . $this->transactionCardCode() . "
			" . $this->transactionApprovalCode() . "
		</" . $this->transactionType() . ">
	</transaction>
	</createCustomerProfileTransactionRequest>";
	$this->process();
	}
	
	// This function is used to delete an existing customer profile along 
	// with all associated customer payment profiles and customer shipping addresses.
	function deleteCustomerProfileRequest() {
	$this->xml = "<?xml version='1.0' encoding='utf-8'?>
	<deleteCustomerProfileRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
	<merchantAuthentication>
		<name>" . $this->login . "</name>
		<transactionKey>" . $this->transkey . "</transactionKey>
	</merchantAuthentication>
	" . $this->refId() . "
	" . $this->customerProfileId() . "
	</deleteCustomerProfileRequest>";
	$this->process();
	}
	
	// This function is used to delete a customer payment profile from an existing customer profile.
	function deleteCustomerPaymentProfileRequest() {
	$this->xml = "<?xml version='1.0' encoding='utf-8'?>
	<deleteCustomerPaymentProfileRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
	<merchantAuthentication>
		<name>" . $this->login . "</name>
		<transactionKey>" . $this->transkey . "</transactionKey>
	</merchantAuthentication>
	" . $this->refId() . "
	" . $this->customerProfileId() . "
	" . $this->customerPaymentProfileId() . "
	</deleteCustomerPaymentProfileRequest>";
	$this->process();
	}
	
	// This function is used to delete a customer shipping address from an existing customer profile.
	function deleteCustomerShippingAddressRequest() {
	$this->xml = "<?xml version='1.0' encoding='utf-8'?>
	<deleteCustomerShippingAddressRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
	<merchantAuthentication>
		<name>" . $this->login . "</name>
		<transactionKey>" . $this->transkey . "</transactionKey>
	</merchantAuthentication>
	" . $this->refId() . "
	" . $this->customerProfileId() . "
	" . $this->customerAddressId() . "
	</deleteCustomerShippingAddressRequest>";
	$this->process();
	}
	
	// This function is used to retrieve an existing customer profile along 
	// with all the associated customer payment profiles and customer shipping addresses.
	function getCustomerProfileRequest() {
	$this->xml = "<?xml version='1.0' encoding='utf-8'?>
	<getCustomerProfileRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
	<merchantAuthentication>
		<name>" . $this->login . "</name>
		<transactionKey>" . $this->transkey . "</transactionKey>
	</merchantAuthentication>
	" . $this->customerProfileId() . "
	</getCustomerProfileRequest>";
	$this->process();
	}
	
	// This function is used to retrieve a customer payment profile for an existing customer profile.
	function getCustomerPaymentProfileRequest() {
	$this->xml = "<?xml version='1.0' encoding='utf-8'?>
	<getCustomerPaymentProfileRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
	<merchantAuthentication>
		<name>" . $this->login . "</name>
		<transactionKey>" . $this->transkey . "</transactionKey>
	</merchantAuthentication>
	" . $this->customerProfileId() . "
	" . $this->customerPaymentProfileId() . "
	</getCustomerPaymentProfileRequest>";
	$this->process();
	}
	
	// This function is used to retrieve a customer shipping address for an existing customer profile.
	function getCustomerShippingAddressRequest() {
	$this->xml = "<?xml version='1.0' encoding='utf-8'?>
	<getCustomerShippingAddressRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
	<merchantAuthentication>
		<name>" . $this->login . "</name>
		<transactionKey>" . $this->transkey . "</transactionKey>
	</merchantAuthentication>
	" . $this->customerProfileId() . "
	" . $this->customerAddressId() . "
	</getCustomerShippingAddressRequest>";
	$this->process();
	}
	
	// This function is used to update an existing customer profile.
	function updateCustomerProfileRequest() {
	$this->xml = "<?xml version='1.0' encoding='utf-8'?>
	<updateCustomerProfileRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
	<merchantAuthentication>
		<name>" . $this->login . "</name>
		<transactionKey>" . $this->transkey . "</transactionKey>
	</merchantAuthentication>
	" . $this->refId() . "
	<profile>
		" . $this->merchantCustomerId() . "
		" . $this->description() . "
		" . $this->email() . "
		" . $this->customerProfileId() . "
	</profile>
	</updateCustomerProfileRequest>";
	$this->process();
	}
	
	// This function is used to update a customer payment profile for an existing customer profile.
	function updateCustomerPaymentProfileRequest() {
	$this->update = false; // keep this false for now
	$this->xml = "<?xml version='1.0' encoding='utf-8'?>
	<updateCustomerPaymentProfileRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
	<merchantAuthentication>
		<name>" . $this->login . "</name>
		<transactionKey>" . $this->transkey . "</transactionKey>
	</merchantAuthentication>
	" . $this->refId() . "
	" . $this->customerProfileId() . "
	<paymentProfile>
		" . $this->customerType() . "
		<billTo>
			" . $this->billTo_firstName() . "
			" . $this->billTo_lastName() . "
			" . $this->billTo_company() . "
			" . $this->billTo_address() . "
			" . $this->billTo_city() . "
			" . $this->billTo_state() . "
			" . $this->billTo_zip() . "
			" . $this->billTo_country() . "
			" . $this->billTo_phoneNumber() . "
			" . $this->billTo_faxNumber() . "
		</billTo>
		<payment>
			" . $this->paymentType() . "
		</payment>
	" . $this->customerPaymentProfileId() . "	
	</paymentProfile>
	</updateCustomerPaymentProfileRequest>";
	$this->process();
	}
	
	// This function is used to update a shipping address for an existing customer profile.
	function updateCustomerShippingAddressRequest() {
	$this->xml = "<?xml version='1.0' encoding='utf-8'?>
	<updateCustomerShippingAddressRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
	<merchantAuthentication>
		<name>" . $this->login . "</name>
		<transactionKey>" . $this->transkey . "</transactionKey>
	</merchantAuthentication>
	" . $this->refId() . "
	" . $this->customerProfileId() . "
	<address>
		" . $this->shipTo_firstName() . "
		" . $this->shipTo_lastName() . "
		" . $this->shipTo_company() . "
		" . $this->shipTo_address() . "
		" . $this->shipTo_city() . "
		" . $this->shipTo_state() . "
		" . $this->shipTo_zip() . "
		" . $this->shipTo_country() . "
		" . $this->shipTo_phoneNumber() . "
		" . $this->shipTo_faxNumber() . "
		" . $this->customerAddressId() . "
	</address>
	</updateCustomerShippingAddressRequest>";
	$this->process();
	}
	
	// This function is used to verify an existing customer payment profile by generating a test transaction.
	function validateCustomerPaymentProfileRequest() {
	$this->xml = "<?xml version='1.0' encoding='utf-8'?>
	<validateCustomerPaymentProfileRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
	<merchantAuthentication>
		<name>" . $this->login . "</name>
		<transactionKey>" . $this->transkey . "</transactionKey>
	</merchantAuthentication>
	" . $this->customerProfileId() . "
	" . $this->customerPaymentProfileId() . "
	" . $this->customerShippingAddressId() . "
	" . $this->validationMode() . "
	</validateCustomerPaymentProfileRequest>";
	$this->process();
	}
	
	
	
	function parseResults()
	{
		$this->resultCode = $this->substring_between($this->response,'<resultCode>','</resultCode>');
		$this->code = $this->substring_between($this->response,'<code>','</code>');
		$this->text = $this->substring_between($this->response,'<text>','</text>');
		$this->refId = $this->substring_between($this->response,'<refId>','</refId>');
		$this->customerProfileId = $this->substring_between($this->response,'<customerProfileId>','</customerProfileId>');
		$this->customerPaymentProfileId = $this->substring_between($this->response,'<customerPaymentProfileIdList><numericString>','</numericString>');
		$this->customerAddressId = $this->substring_between($this->response,'<customerAddressId>','</customerAddressId>');
		$this->directResponse = $this->substring_between($this->response,'<directResponse>','</directResponse>');
		$this->validationDirectResponse = $this->substring_between($this->response,'<validationDirectResponse>','</validationDirectResponse>');
		
		if (!empty($this->directResponse))
		{
			$array = explode($this->responseDelimiter, $this->directResponse);
			$this->directResponse = $array[3];
		}
		if (!empty($this->validationDirectResponse))
		{
			$array = explode($this->responseDelimiter, $this->validationDirectResponse);
			$this->validationDirectResponse = $array[3];
		}
		
	}
	
	function substring_between($haystack,$start,$end)
	{
		if (strpos($haystack,$start) === false || strpos($haystack,$end) === false)
		{
			return false;
		}
		else
		{
			$start_position = strpos($haystack,$start)+strlen($start);
			$end_position = strpos($haystack,$end);
			return substr($haystack,$start_position,$end_position-$start_position);
		}
	}
  
	function setParameter($field = "", $value = NULL)
	{
		$this->params[$field] = $value;
	}
	
	function isSuccessful() 
	{
		return $this->success;
	}
	
	// This function will output the proper xml for a paymentType: (echeck or creditcard)
	// The elements within "bankAccount" is still incorrect in the manual. I fixed it here.
	function paymentType()
	{
		if (isset($this->params['paymentType']))
		{
			if (($this->params['paymentType'] == "echeck") 
			|| ($this->params['paymentType'] == "bankAccount"))
			{
				return "
				<bankAccount>
					" . $this->accountType() . "
					" . $this->routingNumber() . "
					" . $this->accountNumber() . "
					" . $this->nameOnAccount() . "
					" . $this->echeckType() . "
					" . $this->bankName() . "
				</bankAccount>";
			}
			elseif (($this->params['paymentType'] == "creditcard")
			|| ($this->params['paymentType'] == "creditCard"))
			{
				return "
				<creditCard>
					" . $this->cardNumber() . "
					" . $this->expirationDate() . "
					" . $this->transactionCardCode() . "
				</creditCard>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): paymentType is required and must be (bankAccount or creditCard)';
			}
		}
		else
		{
			$this->error_messages[] .= 'setParameter(): paymentType is required and must be (bankAccount or creditCard)';
		}
	}
	
	// Merchant-assigned reference ID for the request (optional)
	function refId()
	{
		if (isset($this->params['refId']))
		{
			if ((strlen($this->params['refId']) > 0) 
			&& (strlen($this->params['refId']) <= 20))
			{
				return "<refId>" . $this->params['refId'] . "</refId>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): refId must be up to 20 characters';
			}
		}
	}
	
	// Contains tax information for the transaction (optional)
	function transactionTax()
	{
		if ((isset($this->params['tax_amount'])) 
		|| (isset($this->params['tax_name'])) 
		|| (isset($this->params['tax_description'])))
		{
			return "
			<tax>
				" . $this->tax_amount() . "
				" . $this->tax_name() . "
				" . $this->tax_description() . "
			</tax>";
		}
	}
	
	// The tax amount for the transaction (optional)
	// This amount must be included in the total amount for the transaction. Ex. 12.99 or 12.9999
	function tax_amount()
	{
		if (isset($this->params['tax_amount']))
		{
			if (preg_match('/(^[0-9]+\.[0-9]{1,4}$)/', $this->params['tax_amount']))
			{
				return "<amount>" . $this->params['tax_amount'] . "</amount>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): tax_amount must be up to 4 digits with a decimal point (no dollar symbol)';
			}
		}
	}
	
	// The name of the tax for the transaction (optional)
	function tax_name()
	{
		if (isset($this->params['tax_name']))
		{
			if ((strlen($this->params['tax_name']) > 0) 
			&& (strlen($this->params['tax_name']) <= 31))
			{
				return "<name>" . $this->params['tax_name'] . "</name>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): tax_name must be up to 31 characters';
			}
		}
	}
	
	// The tax description for the transaction (optional)
	function tax_description()
	{
		if (isset($this->params['tax_description']))
		{
			if ((strlen($this->params['tax_description']) > 0) 
			&& (strlen($this->params['tax_description']) <= 255))
			{
				return "<description>" . $this->params['tax_description'] . "</description>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): tax_description must be up to 255 characters';
			}
		}
	}
	
	// Contains tax information for the transaction (optional)
	function transactionShipping()
	{
		if ((isset($this->params['shipping_amount'])) 
		|| (isset($this->params['shipping_name'])) 
		|| (isset($this->params['shipping_description'])))
		{
			return "
			<shipping>
				" . $this->shipping_amount() . "
				" . $this->shipping_name() . "
				" . $this->shipping_description() . "
			</shipping>";
		}
	}
	
	// The shipping amount for the transaction (optional)
	// This amount must be included in the total amount for the transaction. Ex. 12.99 or 12.9999
	function shipping_amount()
	{
		if (isset($this->params['shipping_amount']))
		{
			if (preg_match('/(^[0-9]+\.[0-9]{1,4}$)/', $this->params['shipping_amount']))
			{
				return "<amount>" . $this->params['shipping_amount'] . "</amount>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): shipping_amount must be up to 4 digits with a decimal point. (no dollar symbol)';
			}
		}
	}
	
	// The name of the shipping for the transaction (optional)
	function shipping_name()
	{
		if (isset($this->params['shipping_name']))
		{
			if ((strlen($this->params['shipping_name']) > 0) 
			&& (strlen($this->params['shipping_name']) <= 31))
			{
				return "<name>" . $this->params['shipping_name'] . "</name>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): shipping_name must be up to 31 characters';
			}
		}
	}
	
	// The shipping description for the transaction (optional)
	function shipping_description()
	{
		if (isset($this->params['shipping_description']))
		{
			if ((strlen($this->params['shipping_description']) > 0) 
			&& (strlen($this->params['shipping_description']) <= 255))
			{
				return "<description>" . $this->params['shipping_description'] . "</description>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): shipping_description must be up to 255 characters';
			}
		}
	}
	
	// Contains duty information for the transaction (optional)
	function transactionDuty()
	{
		if ((isset($this->params['duty_amount'])) 
		|| (isset($this->params['duty_name'])) 
		|| (isset($this->params['duty_description'])))
		{
			return "
			<duty>
				" . $this->duty_amount() . "
				" . $this->duty_name() . "
				" . $this->duty_description() . "
			</duty>";
		}
	}
	
	// The duty amount for the transaction (optional)
	// This amount must be included in the total amount for the transaction. Ex. 12.99 or 12.9999
	function duty_amount()
	{
		if (isset($this->params['duty_amount']))
		{
			if (preg_match('/(^[0-9]+\.[0-9]{1,4}$)/', $this->params['duty_amount']))
			{
				return "<amount>" . $this->params['duty_amount'] . "</amount>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): duty_amount must be up to 4 digits with a decimal point. (no dollar symbol)';
			}
		}
	}
	
	// The name of the duty for the transaction (optional)
	function duty_name()
	{
		if (isset($this->params['duty_name']))
		{
			if ((strlen($this->params['duty_name']) > 0) 
			&& (strlen($this->params['duty_name']) <= 31))
			{
				return "<name>" . $this->params['duty_name'] . "</name>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): duty_name must be up to 31 characters';
			}
		}
	}
	
	// The duty description for the transaction (optional)
	function duty_description()
	{
		if (isset($this->params['duty_description']))
		{
			if ((strlen($this->params['duty_description']) > 0) 
			&& (strlen($this->params['duty_description']) <= 255))
			{
				return "<description>" . $this->params['duty_description'] . "</description>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): duty_description must be up to 255 characters';
			}
		}
	}
	
	// Contains line item details about the order (optional)
	// Up to 30 distinct instances of this element may be included per transaction to describe items included in the order.
	// USAGE: see the example code for createCustomerProfileTransactionRequest() in the examples provided.
	function transactionLineItems()
	{
		if (count($this->LineItems) > 30)
		{
			$this->error_messages[] .= '$object->LineItems: (multidimensional array) Up to 30 distinct instances of this element may be included';
		}
		else
		{
			if (count($this->LineItems) > 0)
			{
				$xmlcode = '';
				foreach ($this->LineItems as $items)
				{
					$xmlcode .= "<lineItems>\n";
					foreach ($items as $key=>$value)
					{
						$xmlcode .= "<$key>$value</$key>\n";
					}
					$xmlcode .= "</lineItems>\n";
				}
				return $xmlcode;
			}
		}
	}
	
	// Contains duty information for the transaction (optional)
	function transactionOrder()
	{
		if ((isset($this->params['order_invoiceNumber'])) 
		|| (isset($this->params['order_description'])) 
		|| (isset($this->params['order_purchaseOrderNumber'])))
		{
			return "
			<order>
				" . $this->order_invoiceNumber() . "
				" . $this->order_description() . "
				" . $this->order_purchaseOrderNumber() . "
			</order>";
		}
	}
	
	// The merchant assigned invoice number for the transaction (optional)
	function order_invoiceNumber()
	{
		if (isset($this->params['order_invoiceNumber'])) 
		{
			if ((strlen($this->params['order_invoiceNumber']) > 0) 
			&& (strlen($this->params['order_invoiceNumber']) <= 20))
			{
				return "<invoiceNumber>" . $this->params['order_invoiceNumber'] . "</invoiceNumber>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): order_invoiceNumber must be up to 20 characters (no symbols)';
			}
		}
	}
	
	// The transaction description (optional)
	function order_description()
	{
		if (isset($this->params['order_description'])) 
		{
			if ((strlen($this->params['order_description']) > 0) 
			&& (strlen($this->params['order_description']) <= 255))
			{
				return "<description>" . $this->params['order_description'] . "</description>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): order_description must be up to 255 characters (no symbols)';
			}
		}
	}
	
	// The merchant assigned purchase order number (optional)
	function order_purchaseOrderNumber()
	{
		if (isset($this->params['order_purchaseOrderNumber'])) 
		{
			if ((strlen($this->params['order_purchaseOrderNumber']) > 0) 
			&& (strlen($this->params['order_purchaseOrderNumber']) <= 25))
			{
				return "<purchaseOrderNumber>" . $this->params['order_purchaseOrderNumber'] . "</purchaseOrderNumber>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): order_purchaseOrderNumber must be up to 25 characters (no symbols)';
			}
		}
	}
	
	/************************* Billing Functions *************************/
	
	// The customer's first name (optional)
	function billTo_firstName()
	{
		if (isset($this->params['billTo_firstName']))
		{
			if ($this->update === true)
			{
				return "<firstName>" . $this->params['billTo_firstName'] . "</firstName>";
			}
			else
			{
				if ((strlen($this->params['billTo_firstName']) > 0) && (strlen($this->params['billTo_firstName']) <= 50))
				{
					return "<firstName>" . $this->params['billTo_firstName'] . "</firstName>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): billTo_firstName must be up to 50 characters (no symbols)';
				}
			}
		}
	}
	
	// The customer's last name (optional)
	function billTo_lastName()
	{
		
		if (isset($this->params['billTo_lastName']))
		{
			if ($this->update === true)
			{
				return "<lastName>" . $this->params['billTo_lastName'] . "</lastName>";
			}
			else
			{
				if ((strlen($this->params['billTo_lastName']) > 0) && (strlen($this->params['billTo_lastName']) <= 50))
				{
					return "<lastName>" . $this->params['billTo_lastName'] . "</lastName>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): billTo_lastName must be up to 50 characters (no symbols)';
				}
			}
		}
	}
	
	// The name of the company associated with the customer, if applicable (optional)
	function billTo_company()
	{
		if (isset($this->params['billTo_company']))
		{
			if ($this->update === true)
			{
				return "<company>" . $this->params['billTo_company'] . "</company>";
			}
			else
			{
				if ((strlen($this->params['billTo_company']) > 0) && (strlen($this->params['billTo_company']) <= 50))
				{
					return "<company>" . $this->params['billTo_company'] . "</company>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): billTo_company must be up to 50 characters (no symbols)';
				}
			}
		}
	}
	
	// The customer's address (optional)
	function billTo_address()
	{
		if (isset($this->params['billTo_address']))
		{
			if ($this->update === true)
			{
				return "<address>" . $this->params['billTo_address'] . "</address>";
			}
			else
			{
				if ((strlen($this->params['billTo_address']) > 0) && (strlen($this->params['billTo_address']) <= 60))
				{
					return "<address>" . $this->params['billTo_address'] . "</address>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): billTo_address must be up to 60 characters (no symbols)';
				}
			}
		}
	}
	
	// The city of the customer's address (optional)
	function billTo_city()
	{
		if (isset($this->params['billTo_city']))
		{
			if ($this->update === true)
			{
				return "<city>" . $this->params['billTo_city'] . "</city>";
			}
			else
			{
				if ((strlen($this->params['billTo_city']) > 0) && (strlen($this->params['billTo_city']) <= 40))
				{
					return "<city>" . $this->params['billTo_city'] . "</city>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): billTo_city must be up to 40 characters (no symbols)';
				}
			}
		}
	}
	
	// The state of the customer's address (optional)
	// http://www.usps.com/ncsc/lookups/usps_abbreviations.html#states
	function billTo_state()
	{
		if (isset($this->params['billTo_state']))
		{
			if ($this->update === true)
			{
				return "<state>" . $this->params['billTo_state'] . "</state>";
			}
			else
			{
				if (preg_match('/^[a-z]{2}$/i', $this->params['billTo_state']))
				{
					return "<state>" . $this->params['billTo_state'] . "</state>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): billTo_state must be a valid two-character state code';
				}
			}
		}
	}
	
	// The ZIP code of the customer's address (optional)
	function billTo_zip()
	{
		if (isset($this->params['billTo_zip']))
		{
			if ($this->update === true)
			{
				return "<zip>" . $this->params['billTo_zip'] . "</zip>";
			}
			else
			{
				if ((strlen($this->params['billTo_zip']) > 0) && (strlen($this->params['billTo_zip']) <= 20))
				{
					return "<zip>" . $this->params['billTo_zip'] . "</zip>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): billTo_zip must be up to 20 characters (no symbols)';
				}
			}
		}
	}
	
	// This element is optional
	function billTo_country()
	{
		if (isset($this->params['billTo_country']))
		{
			if ($this->update === true)
			{
				return "<country>" . $this->params['billTo_country'] . "</country>";
			}
			else
			{
				if ((strlen($this->params['billTo_country']) > 0) && (strlen($this->params['billTo_country']) <= 60))
				{
					return "<country>" . $this->params['billTo_country'] . "</country>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): billTo_country must be up to 60 characters (no symbols)';
				}
			}
		}
	}
	
	// The phone number associated with the customer's address (optional)
	function billTo_phoneNumber()
	{
		if (isset($this->params['billTo_phoneNumber']))
		{
			if ($this->update === true)
			{
				return "<phoneNumber>" . $this->params['billTo_phoneNumber'] . "</phoneNumber>";
			}
			else
			{
				if ((strlen($this->params['billTo_phoneNumber']) > 0) && (strlen($this->params['billTo_phoneNumber']) <= 25))
				{
					return "<phoneNumber>" . $this->params['billTo_phoneNumber'] . "</phoneNumber>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): billTo_phoneNumber must be up to 25 digits (no letters). Ex. (123)123-1234';
				}
			}
		}
	}
	
	// This element is optional
	function billTo_faxNumber()
	{
		if (isset($this->params['billTo_faxNumber']))
		{
			if ($this->update === true)
			{
				return "<faxNumber>" . $this->params['billTo_faxNumber'] . "</faxNumber>";
			}
			else
			{
				if ((strlen($this->params['billTo_faxNumber']) > 0) && (strlen($this->params['billTo_faxNumber']) <= 25))
				{
					return "<faxNumber>" . $this->params['billTo_faxNumber'] . "</faxNumber>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): billTo_faxNumber must be up to 25 digits (no letters). Ex. (123)123-1234';
				}
			}
		}
	}
	
	/************************* Shipping Functions *************************/
	
	// The customer's first name (optional)
	function shipTo_firstName()
	{
		if (isset($this->params['shipTo_firstName']))
		{
			if ((strlen($this->params['shipTo_firstName']) > 0) && (strlen($this->params['shipTo_firstName']) <= 50))
			{
				return "<firstName>" . $this->params['shipTo_firstName'] . "</firstName>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): shipTo_firstName must be up to 50 characters (no symbols)';
			}
		}
	}
	
	// The customer's last name (optional)
	function shipTo_lastName()
	{
		if (isset($this->params['shipTo_lastName']))
		{
			if ((strlen($this->params['shipTo_lastName']) > 0) && (strlen($this->params['shipTo_lastName']) <= 50))
			{
				return "<lastName>" . $this->params['shipTo_lastName'] . "</lastName>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): shipTo_lastName must be up to 50 characters (no symbols)';
			}
		}
	}
	
	// The name of the company associated with the customer, if applicable (optional)
	function shipTo_company()
	{
		if (isset($this->params['shipTo_company']))
		{
			if ((strlen($this->params['shipTo_company']) > 0) && (strlen($this->params['shipTo_company']) <= 50))
			{
				return "<company>" . $this->params['shipTo_company'] . "</company>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): shipTo_company must be up to 50 characters (no symbols)';
			}
		}
	}
	
	// The customer's address (optional)
	function shipTo_address()
	{
		if (isset($this->params['shipTo_address']))
		{
			if ((strlen($this->params['shipTo_address']) > 0) && (strlen($this->params['shipTo_address']) <= 60))
			{
				return "<address>" . $this->params['shipTo_address'] . "</address>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): shipTo_address must be up to 60 characters (no symbols)';
			}
		}
	}
	
	// The city of the customer's address (optional)
	function shipTo_city()
	{
		if (isset($this->params['shipTo_city']))
		{
			if ((strlen($this->params['shipTo_city']) > 0) && (strlen($this->params['shipTo_city']) <= 40))
			{
				return "<city>" . $this->params['shipTo_city'] . "</city>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): shipTo_city must be up to 40 characters (no symbols)';
			}
		}
	}
	
	// The state of the customer's address (optional)
	// http://www.usps.com/ncsc/lookups/usps_abbreviations.html#states
	function shipTo_state()
	{
		if (isset($this->params['shipTo_state']))
		{
			if (preg_match('/^[a-z]{2}$/i', $this->params['shipTo_state']))
			{
				return "<state>" . $this->params['shipTo_state'] . "</state>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): shipTo_state must be a valid two-character state code';
			}
		}
	}
	
	// The ZIP code of the customer's address (optional)
	function shipTo_zip()
	{
		if (isset($this->params['shipTo_zip']))
		{
			if ((strlen($this->params['shipTo_zip']) > 0) && (strlen($this->params['shipTo_zip']) <= 20))
			{
				return "<zip>" . $this->params['shipTo_zip'] . "</zip>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): shipTo_zip must be up to 20 characters (no symbols)';
			}
		}
	}
	
	// The country of the customer's address (optional)
	function shipTo_country()
	{
		if (isset($this->params['shipTo_country']))
		{
			if ((strlen($this->params['shipTo_country']) > 0) && (strlen($this->params['shipTo_country']) <= 60))
			{
				return "<country>" . $this->params['shipTo_country'] . "</country>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): shipTo_country must be up to 60 characters (no symbols)';
			}
		}
	}
	
	// The phone number associated with the customer's address (optional)
	function shipTo_phoneNumber()
	{
		if (isset($this->params['shipTo_phoneNumber']))
		{
			if ((strlen($this->params['shipTo_phoneNumber']) > 0) && (strlen($this->params['shipTo_phoneNumber']) <= 25))
			{
				return "<phoneNumber>" . $this->params['shipTo_phoneNumber'] . "</phoneNumber>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): shipTo_phoneNumber must be up to 25 digits (no letters). Ex. (123)123-1234';
			}
		}
	}
	
	// The fax number associated with the customer's address (optional)
	function shipTo_faxNumber()
	{
		if (isset($this->params['shipTo_faxNumber']))
		{
			if ((strlen($this->params['shipTo_faxNumber']) > 0) && (strlen($this->params['shipTo_faxNumber']) <= 25))
			{
				return "<faxNumber>" . $this->params['shipTo_faxNumber'] . "</faxNumber>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): shipTo_faxNumber must be up to 25 digits (no letters). Ex. (123)123-1234';
			}
		}
	}
	
	/************************* Other Functions *************************/
	
	// This element is optional
	// Even though the manual states this is optional, it is actually conditional in a circumstance.
	// You must have either the merchantCustomerId and/or description defined for createCustomerProfileRequest()
	function merchantCustomerId()
	{
		if (isset($this->params['merchantCustomerId']))
		{
			if ((strlen($this->params['merchantCustomerId']) > 0) && (strlen($this->params['merchantCustomerId']) <= 20))
			{
				return "<merchantCustomerId>" . $this->params['merchantCustomerId'] . "</merchantCustomerId>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): merchantCustomerId must be up to 20 characters in length';
			}
		}
	}
	
	// This element is optional
	// Even though the manual states this is optional, it is actually conditional in a circumstance.
	// You must have either the description and/or merchantCustomerId defined for createCustomerProfileRequest()
	function description()
	{
		if (isset($this->params['description']))
		{
			if ((strlen($this->params['description']) > 0) && (strlen($this->params['description']) <= 255))
			{
				return "<description>" . $this->params['description'] . "</description>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): description must be up to 255 characters in length';
			}
		}
	}
	
	// This element is optional
	function email()
	{
		if (isset($this->params['email']))
		{
			if ((strlen($this->params['email']) > 0) && (strlen($this->params['email']) <= 255))
			{
				return "<email>" . $this->params['email'] . "</email>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): email must be up to 255 characters in length';
			}
		}
	}
	
	// This element is optional
	function customerType()
	{
		if (isset($this->params['customerType']))
		{
			if (preg_match('/^(individual|business)$/i', $this->params['customerType']))
			{
				return "<customerType>" . strtolower($this->params['customerType']) . "</customerType>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): customerType must be (individual or business)';
			}
		}
	}
	
	// This element is optional
	function accountType()
	{
		if (isset($this->params['accountType']))
		{
			if ($this->update === true)
			{
				return "<accountType>" . $this->params['accountType'] . "</accountType>";
			}
			else
			{
				if (preg_match('/^(checking|savings|businessChecking)$/', $this->params['accountType']))
				{
					return "<accountType>" . $this->params['accountType'] . "</accountType>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): accountType is required and must be (checking, savings or businessChecking)';
				}
			}
		}
		else
		{
			$this->error_messages[] .= 'setParameter(): accountType is required and must be (checking, savings or businessChecking)..';
		}
	}
	
	// This element is optional
	function nameOnAccount()
	{
		if (isset($this->params['nameOnAccount']))
		{
			if ($this->update === true)
			{
				return "<nameOnAccount>" . $this->params['nameOnAccount'] . "</nameOnAccount>";
			}
			else
			{
				if ((strlen($this->params['nameOnAccount']) > 0) && (strlen($this->params['nameOnAccount']) <= 22))
				{
					return "<nameOnAccount>" . $this->params['nameOnAccount'] . "</nameOnAccount>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): nameOnAccount is required and must be up to 22 characters in length';
				}
			}
		}
		else
		{
			$this->error_messages[] .= 'setParameter(): nameOnAccount is required and must be up to 22 characters in length..';
		}
	}
	
	// This element is optional
	function echeckType()
	{
		if (isset($this->params['echeckType']))
		{
			if ($this->update === true)
			{
				return "<echeckType>" . $this->params['echeckType'] . "</echeckType>";
			}
			else
			{
				if (preg_match('/^(CCD|PPD|TEL|WEB)$/', $this->params['echeckType']))
				{
					return "<echeckType>" . $this->params['echeckType'] . "</echeckType>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): echeckType is required and must be (CCD, PPD, TEL or WEB)';
				}
			}
		}
		else
		{
			$this->error_messages[] .= 'setParameter(): echeckType is required and must be (CCD, PPD, TEL or WEB)..';
		}
	}
	
	// This element is optional
	function bankName()
	{
		if (isset($this->params['bankName']))
		{
			if ($this->update === true)
			{
				return "<bankName>" . $this->params['bankName'] . "</bankName>";
			}
			else
			{
				if ((strlen($this->params['bankName']) > 0) && (strlen($this->params['bankName']) <= 60))
				{
					return "<bankName>" . $this->params['bankName'] . "</bankName>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): bankName is required and must be up to 50 characters in length';
				}
			}
		}
		else
		{
			$this->error_messages[] .= 'setParameter(): bankName is required and must be up to 50 characters in length..';
		}
	}
	
	// This element is required in some functions
	function routingNumber()
	{
		if (isset($this->params['routingNumber']))
		{
			if ($this->update === true)
			{
				return "<routingNumber>" . $this->params['routingNumber'] . "</routingNumber>";
			}
			else
			{
				if (preg_match('/^[0-9]{9}$/', $this->params['routingNumber']))
				{
					return "<routingNumber>" . $this->params['routingNumber'] . "</routingNumber>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): routingNumber is required and must be 9 digits';
				}
			}
		}
		else
		{
			$this->error_messages[] .= 'setParameter(): routingNumber is required and must be 9 digits..';
		}
	}
	
	// This element is required in some functions
	function accountNumber()
	{
		if (isset($this->params['accountNumber']))
		{
			if ($this->update === true)
			{
				return "<accountNumber>" . $this->params['accountNumber'] . "</accountNumber>";
			}
			else
			{
				if (preg_match('/^[0-9]{5,17}$/', $this->params['accountNumber']))
				{
					return "<accountNumber>" . $this->params['accountNumber'] . "</accountNumber>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): accountNumber is required and must be 5 to 17 digits';
				}
			}
		}
		else
		{
			$this->error_messages[] .= 'setParameter(): accountNumber is required and must be 5 to 17 digits..';
		}
	}
	
	// This element is required in some functions
	function cardNumber()
	{
		if (isset($this->params['cardNumber']))
		{
			if ($this->update === true)
			{
				return "<cardNumber>" . $this->params['cardNumber'] . "</cardNumber>";
			}
			else
			{
				if (preg_match('/^[0-9]{13,16}$/', $this->params['cardNumber']))
				{
					return "<cardNumber>" . $this->params['cardNumber'] . "</cardNumber>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): cardNumber is required and must be 13 to 16 digits';
				}
			}
		}
		else
		{
			$this->error_messages[] .= 'setParameter(): cardNumber is required and must be 13 to 16 digits..';
		}
	}
	
	// This element is required in some functions
	function expirationDate()
	{
		if (isset($this->params['expirationDate']))
		{
			if ($this->update === true)
			{
				return "<expirationDate>" . $this->params['expirationDate'] . "</expirationDate>";
			}
			else
			{
				if (preg_match('/^([0-9]{4})-([0-9]{2})$/', $this->params['expirationDate']))
				{
					return "<expirationDate>" . $this->params['expirationDate'] . "</expirationDate>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): expirationDate is required and must be YYYY-MM';
				}
			}
		}
		else
		{
			$this->error_messages[] .= 'setParameter(): expirationDate is required and must be YYYY-MM..';
		}
	}

	
	// This element is required in some functions
	// This amount should include all other amounts such as tax amount, shipping amount, etc. Ex. 12.99 or 12.9999
	function transaction_amount()
	{
		if (isset($this->params['transaction_amount']))
		{
			if (preg_match('/(^[0-9]+\.[0-9]{1,4}$)/', $this->params['transaction_amount']))
			{
				return "<amount>" . $this->params['transaction_amount'] . "</amount>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): transaction_amount is required and must be up to 4 digits with a decimal (no dollar symbol)';
			}
		}
		else
		{
			$this->error_messages[] .= 'setParameter(): transaction_amount is required and must be up to 4 digits with a decimal';
		}
	}
	
	// This element is required in some functions
	function transactionType()
	{
		if (isset($this->params['transactionType']))
		{
			if (preg_match('/^(profileTransCaptureOnly|profileTransAuthCapture|profileTransAuthOnly)$/', $this->params['transactionType']))
			{
				return $this->params['transactionType'];
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): transactionType must be (profileTransCaptureOnly, profileTransAuthCapture or profileTransAuthOnly)';
			}
		}
		else
		{
			$this->error_messages[] .= 'setParameter(): transactionType must be (profileTransCaptureOnly, profileTransAuthCapture or profileTransAuthOnly)';
		}
	}
	
	// This element is required in some functions
	// Payment gateway assigned ID associated with the customer profile
	function customerProfileId()
	{
		if (isset($this->params['customerProfileId']))
		{
			if (preg_match('/^[0-9]+$/', $this->params['customerProfileId']))
			{
				return "<customerProfileId>" . $this->params['customerProfileId'] . "</customerProfileId>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): customerProfileId is required and must be numeric';
			}
		}
		else
		{
			$this->error_messages[] .= 'setParameter(): customerProfileId is required and must be numeric';
		}
	}
	
	// This element is required in some functions
	// Payment gateway assigned ID associated with the customer payment profile
	function customerPaymentProfileId()
	{
		if (isset($this->params['customerPaymentProfileId']))
		{
			if ($this->update === true)
			{
				return "<customerPaymentProfileId>" . $this->params['customerPaymentProfileId'] . "</customerPaymentProfileId>";
			}
			else
			{
				if (preg_match('/^[0-9]+$/', $this->params['customerPaymentProfileId']))
				{
					return "<customerPaymentProfileId>" . $this->params['customerPaymentProfileId'] . "</customerPaymentProfileId>";
				}
				else
				{
					$this->error_messages[] .= 'setParameter(): customerPaymentProfileId is required and must be numeric';
				}
			}
		}
		else
		{
			$this->error_messages[] .= 'setParameter(): customerPaymentProfileId is required and must be numeric..';
		}
	}
	
	// This element is required in some functions, otherwise optional
	// Payment gateway assigned ID associated with the customer shipping address
	// Note: If the customer AddressId is not passed, shipping information will not be included with the transaction.
	function customerAddressId()
	{
		if (isset($this->params['customerAddressId']))
		{
			if (preg_match('/^[0-9]+$/', $this->params['customerAddressId']))
			{
				return "<customerAddressId>" . $this->params['customerAddressId'] . "</customerAddressId>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): customerAddressId is required and must be numeric';
			}
		}
		else
		{
			$this->error_messages[] .= 'setParameter(): customerAddressId is required and must be numeric';
		}
	}
	
	// In validateCustomerPaymentProfileRequest(), customerShippingAddressId() is used in place of customerAddressId().
	// The Authorize.net manual is still incorrect on this.
	// Payment gateway assigned ID associated with the customer shipping address
	// Note: If the customer Shipping AddressId is not passed, shipping information will not be included with the transaction.
	function customerShippingAddressId()
	{
		if (isset($this->params['customerShippingAddressId']))
		{
			if (preg_match('/^[0-9]+$/', $this->params['customerShippingAddressId']))
			{
				return "<customerShippingAddressId>" . $this->params['customerShippingAddressId'] . "</customerShippingAddressId>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): customerShippingAddressId is required and must be numeric';
			}
		}
	}
	
	// This element is optional
	function transactionTaxExempt()
	{
		if (isset($this->params['transactionTaxExempt']))
		{
			if (preg_match('/^(true|false)$/i', $this->params['transactionTaxExempt']))
			{
				return "<taxExempt>" . $this->params['transactionTaxExempt'] . "</taxExempt>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): transactionTaxExempt is required and must be (true or false)';
			}
		}
	}
	
	// This element is optional
	function transactionRecurringBilling()
	{
		if (isset($this->params['transactionRecurringBilling']))
		{
			if (preg_match('/^(true|false)$/i', $this->params['transactionRecurringBilling']))
			{
				return "<recurringBilling>" . $this->params['transactionRecurringBilling'] . "</recurringBilling>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): transactionRecurringBilling must be (true or false)';
			}
		}
	}
	
	// The customer's card code (the three or four-digit number on the back or front of a credit card)
	// Required only when the merchant would like to use the Card Code Verification (CCV) filter (conditional)
	// For more information, please see the Merchant Integration Guide.
	function transactionCardCode()
	{
		if (isset($this->params['transactionCardCode']))
		{
			if (preg_match('/^[0-9]{3,4}$/', $this->params['transactionCardCode']))
			{
				return "<cardCode>" . $this->params['transactionCardCode'] . "</cardCode>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): transactionCardCode must be 3 to 4 digits';
			}
		}
	}
	
	// The authorization code of an original transaction required for a Capture Only (conditional)
	// This element is only required for the Capture Only transaction type.
	function transactionApprovalCode()
	{
		if (isset($this->params['transactionApprovalCode'])) 
		{
			if (($this->transactionType() == "profileTransCaptureOnly") 
			&& (strlen($this->params['transactionApprovalCode']) == 6))
			{
				return "<approvalCode>" . $this->params['transactionApprovalCode'] . "</approvalCode>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): transactionApprovalCode must be 6 characters and transactionType value must be (profileTransCaptureOnly)';
			}
		}
	}
	// This element is required in some functions
	function validationMode()
	{
		if (isset($this->params['validationMode']))
		{
			if (preg_match('/^(none|testMode|liveMode)$/', $this->params['validationMode']))
			{
				return "<validationMode>" . $this->params['validationMode'] . "</validationMode>";
			}
			else
			{
				$this->error_messages[] .= 'setParameter(): validationMode must be (none, testMode or liveMode)';
			}
		}
		else
		{
			$this->error_messages[] .= 'setParameter(): validationMode is required';
		}
	}
}
?>