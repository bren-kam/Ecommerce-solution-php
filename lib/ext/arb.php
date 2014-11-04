<?php

/************************************************************************************************************
 ************************************************************************************************************
 **                                                                                                        **
 ** Copyright (c) 2008, Joshua Bettigole                                                                   **
 ** All rights reserved.                                                                                   **
 **                                                                                                        **
 ** Redistribution and use in source and binary forms, with or without modification, are permitted         **
 ** provided that the following conditions are met:                                                        **
 **                                                                                                        **
 ** - Redistributions of source code must retain the above copyright notice, this list of conditions       **
 **   and the following disclaimer.                                                                        **
 ** - Redistributions in binary form must reproduce the above copyright notice, this list of               **
 **   conditions and the following disclaimer in the documentation and/or other materials provided         **
 **   with the distribution.                                                                               **
 ** - The names of its contributors may not be used to endorse or promote products derived from this       **
 **   software without specific prior written permission.                                                  **
 **                                                                                                        **
 ** THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR         **
 ** IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND       **
 ** FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR              **
 ** CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL      **
 ** DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,      **
 ** DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER     **
 ** IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT      **
 ** OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                        **
 **                                                                                                        **
 ************************************************************************************************************
 ************************************************************************************************************/

class arb
{

/**
	* Static Variables for this class
	* Set Login ID and Transaction Key accordingly
	* Leave Namespace and URL alone unless you know what you're doing
	*/
	const auth_net_login_id  = "3FpJ4368uH";
	const auth_net_tran_key  = "3H468AhaTR3pzq3W";
	const auth_net_namespace = "AnetApi/xml/v1/schema/AnetApiSchema.xsd";
	const auth_net_URL			 = "https://api.authorize.net/xml/v1/request.api";

	
/**
	* DEBUG
	* used to keep the form from processing and to display more information
	*/
	public $DEBUG=0;
	
/**
	* success
	* true if request is successful
	*/
	public $success;
	
/**
	* results array
	* used to hold results
	*/
	public $results=array();
	
/**
	* error array
	* used to hold error messages
	*/
	public  $error=array();

/**
	* request
	* used to hold the request XML
	*/
	public $request;
	
/**
	* response
	* used to hold response message
	*/
	public $response;

	
	
/**
	* Document Object Model
	* This is the root of the request
	*/
	private $dom;

/**
	* Document Object Model
	* This is the return DOM
	*/
	private $rdom;
	
/**
	* ARBCreateSubscriptionRequest
	* Root Node required to complete a new subscription
	*/
	private $ARBCreateSubscriptionRequest;

/**
	* ARBUpdateSubscriptionRequest
	* Root Node required to update a subscription
	*/
	private $ARBUpdateSubscriptionRequest;

/**
	* ARBCancelSubscriptionRequest
	* Root Node required to cancel a subscription
	*/
	private $ARBCancelSubscriptionRequest;

/**
	* ARBGetSubscriptionListRequest
	* Root Node required to cancel a subscription
	*/
	private $ARBGetSubscriptionListRequest;

/**
	* /merchantAuthentication
	* Contains the merchant's payment gateway account authentication information
	*/
	private $merchantAuthentication;

/**
	* /refId (optional)
	* Merchant-assigned reference ID for the request
	*/
	private $refId;

/**
	* /subscription
	* Contains information about the subscription
	*/
	private $subscription;

/**
	* /subscription/name (optional)
	* Merchant-assigned name for the subscription
	*/
	private $name;

/**
	* /subscription/paymentSchedule
	* Contains information about the payment schedule
	*/
	private $paymentSchedule;

/**
	* /subscription/paymentSchedule/interval
	* Contains information about the interval of time between payments
	*/
	private $interval;

/**
	*	/subscription/paymentSchedule/interval/length
	* The measurment of time, in association with the Interval Unit, that is
	* used to define the frequency of the billing occurences
	*/
	private $length;

/**
	* /subscription/paymentSchedule/interval/unit
	* The unit of time, in association with the Interval Length, between each
	* billing occurence
	*/
	private $unit;

/**
	* /subscription/paymentSchedule/startDate
	* The date the subscription begins (also the date the initial billing occurs)
	*/
	private $startDate;

/**
	* /subscription/paymentSchedule/totalOccurrences
	* Number of billing occurrences or payments for the subscription
	*/
	private $totalOccurrences;

/**
	* totalOccurrencesChanged
	* Used to only in Update, to see if we have to send a new value
	*/
	private $totalOccurrencesChanged;
	
/**
	* /subscription/paymentSchedule/trialOccurrences (optional)
	* Number of billing occurrences or payments in the trial period
	*/
	private $trialOccurrences;

/**
	* /subscription/amount
	* The amount to be billed to the customer for each payment in the subscription
	*/
	private $amount;

/**
	* /subscription/trialAmount (conditional)
	* The amount to be charged for each payment during a trial period
	*/
	private $trialAmount;

/**
	* /subscription/payment
	* Contains either the customer's credit card or bank account payment information
	*/
	private $payment;

/**
	* /subscription/payment/creditCard
	*	Contains the customer's credit card information
	*/
	private $creditCard;

/**
	* /subscription/payment/creditCard/cardNumber
	* The credit card number used for payment of the subscription
	*/
	private $cardNumber;

/**
	* /subscription/payment/creditCard/expirationDate
	* The expiration date of the credit card used for the subscription
	*/
	private $expirationDate;

/**
	* /subscription/payment/bankAccount
	* Contains the customer's bank account information
	*/
	private $bankAccount;

/**
	* /subscription/payment/bankAccount/accountType
	* The type of bank account used for payment of the subscription
	*/
	private $accountType;

/**
	* /subscription/payment/bankAccount/routingNumber
	* The routing number of the customer's bank
	*/
	private $routingNumber;

/**
	* /subscription/payment/bankAccount/accountNumber
	* The bank account number used for payment of the subscription
	*/
	private $accountNumber;

/**
	* /subscription/payment/bankAccount/nameOnAccount
	* The full name of the individual associated with the bank
	* account number
	*/
	private $nameOnAccount;

/**
	* /subscription/payment/bankAccount/bankName
	* The name of the bank associated with the bank account number
	*/
	private $bankName;

/**
	* /subscription/payment/bankAccount/echeckType
	* The type of electronic check transaction used for the subscription
	*/
	private $echeckType;

/**
	* /subscription/order (optional)
	* Contains optional order information
	*/
	private $order;

/**
	* /subscription/order/invoiceNumber (optional)
	* Merchant-assigned invoice number for the subscription
	*/
	private $invoiceNumber;

/**
	* /subscription/order/description (optional)
	* Description of the subscription
	*/
	private $description;

/**
	* /subscription/customer (optional)
	* Contains information about the customer
	*/
	private $customer;

/**
	* /subscription/customer/id (optional)
	* Merchant-assigned identifier for the customer
	*/
	private $id;

/**
	* /subscription/customer/email (optional)
	* The customer's email address
	*/
	private $email;

/**
	* /subscription/customer/phoneNumber (optional)
	* The customer's phone number
	*/
	private $phoneNumber;

/**
	* /subscription/customer/faxNumber (optional)
	* The customer's fax number
	*/
	private $faxNumber;

/**
	* /subscription/billTo
	* Contains the customer's billing address information
	*/
	private $billTo;
	
/**
	* /subscription/billTo/firstName
	* The first name associated with the customer's billing address
	*/
	private $billingFirstName;
	
/**
	* /subscription/billTo/lastName
	* The last name associated with the customer's billing address
	*/
	private $billingLastName;
	
/**
	* /subscription/billTo/company (optional)
	* The company associated with the customer's billing address
	*/
	private $billingCompany;
	
/**
	* /subscription/billTo/address (optional)
	* The customer's billing address
	*/
	private $billingAddress;
	
/**
	* /subscription/billTo/city (optional)
	* The city of the customer's billing address
	*/
	private $billingCity;
	
/**
	* /subscription/billTo/state (optional)
	* The state of the customer's billing address
	*/
	private $billingState;
	
/**
	* /subscription/billTo/zip (optional)
	* The ZIP code of the customer's billing address
	*/
	private $billingZip;
	
/**
	* /subscription/billTo/country (optional)
	* The country of the customer's billing address
	*/
	private $billingCountry;

/**
	* /subscription/shipTo/firstName
	* The first name associated with the customer's shipping address
	*/
	private $shippingFirstName;
	
/**
	* /subscription/shipTo/lastName
	* The last name associated with the customer's shipping address
	*/
	private $shippingLastName;
	
/**
	* /subscription/shipTo/company (optional)
	* The company associated with the customer's shipping address
	*/
	private $shippingCompany;
	
/**
	* /subscription/shipTo/address (optional)
	* The customer's shipping address
	*/
	private $shippingAddress;
	
/**
	* /subscription/shipTo/city (optional)
	* The city of the customer's shipping address
	*/
	private $shippingCity;
	
/**
	* /subscription/shipTo/state (optional)
	* The state of the customer's shipping address
	*/
	private $shippingState;
	
/**
	* /subscription/shipTo/zip (optional)
	* The ZIP code of the customer's shipping address
	*/
	private $shippingZip;
	
/**
	* /subscription/shipTo/country (optional)
	* The country of the customer's shipping address
	*/
	private $shippingCountry;

/**
	* /subscriptionId
	* The payment gateway assigned identification number
	* for the subscription
	*/
	private $subscriptionId;


    /**
	 * /searchType
	 * The searchType for getSubscriptionList
	 */
	private $searchType;

    /**
	 * /orderBy
	 * The orderBy for getSubscriptionList
	 */
	private $orderBy;

    /**
	 * /orderDescending
	 * The orderDescending for getSubscriptionList
	 */
	private $orderDescending;

    /**
	 * /limit
	 * The limit for getSubscriptionList
	 */
	private $limit;

    /**
	 * /offset
	 * The offset for getSubscriptionList
	 */
	private $offset;


/**
	* __construct()
	*	Initializes DOM
	@var string Optional Subscription Name
	*/
	public function __construct()
	{
		// create document
		$this->dom = new DomDocument('1.0', 'utf-8');
		$this->setDefaults();

		// if we get an argument, make it the name of the subscription
		if (func_num_args()>0)
		{
			$name_value=func_get_arg(0);
			$this->name = $this->dom->createElement('name', substr($name_value,0,20));
		}
		return;
	}

/**
	* setAmount()
	* Set dollar amount of recurring transaction
	*	@var float Amount
	*/
	public function setAmount()
	{
		// Ensure we have an argument to work with
		if (func_num_args() != 1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}

		// Add it to the DOM
		$amount_value = func_get_arg(0);
		if (!is_numeric($amount_value) || strlen($amount_value)>15)
			$this->error[]="Invalid date type for argument 1 in function '".__FUNCTION__."()'";
		else
			$this->amount = $this->dom->createElement('amount', $amount_value);
		return;
	}

/**
	* setTotalOccurrences()
	* Set total occurrences of recurring transaction
	*	@var int Occurrences
	*/
	public function setTotalOccurrences()
	{
		// Ensure we have an argument to work with
		if (func_num_args() != 1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}

		// Add it
		$occurrences_value = func_get_arg(0);
		if (!is_numeric($occurrences_value) || strlen($occurrences_value)>4)
			$this->error[]="Invalid date type for argument 1 in function '".__FUNCTION__."()'";
		else
		{
			$this->totalOccurrences = intval($occurrences_value);
			$this->totalOccurrencesChanged=true;
		}
		return;
	}

/**
	* setTrial()
	* Set dollar amount and number of occurences during a trial period
	* @var int Occurrences
	*	@var float Amount
	*/
	public function setTrial()
	{
		// Ensure we have an argument to work with
		if (func_num_args() != 2)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}

		// Add it to the DOM
		$occurrences_value = func_get_arg(0);
		$amount_value = func_get_arg(1);
		if (strlen($occurrences_value)>2 || strlen($amount_value)>15)
			$this->error[]="Invalid argument length in function '".__FUNCTION__."()'";
		else
		{
			$this->trialOccurrences = $this->dom->createElement('trialOccurrences', $occurrences_value);
			$this->trialAmount = $this->dom->createElement('trialAmount', $amount_value);
		}
		return;
	}

/**
	* Merchant-assigned reference ID for the request (Optional)
	*	@var string Reference ID
	*/
	public function setReferenceID()
	{
		// Make sure we got an ID
		if (func_num_args()!=1)
		{
			$this->error[]="Invalid arguments in function '".__FUNCTION__."()'";
			return;
		}

		// Add it to the DOM
		$refId_value = func_get_arg(0);
		$this->refId = $this->dom->createElement('refId', substr($refId_value,0,20));
		return;
	}

/**
	* Set the start date of the subscription (Optional, default = today)
	* @var string Representation of a date
	*/
	public function setStartDate()
	{
		// Ensure we have an argument to work with
		if (func_num_args() != 1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}

		// Add it
		$date_value = func_get_arg(0);
		if (date('Ymd',strtotime($date_value)) < date('Ymd'))
			$this->error[]="Start date can not occur in the past in function '".__FUNCTION__."()'";
		else
			$this->startDate = date('Y-m-d',strtotime($date_value));
		return;
	}

/**
	* Set the recurrence interval
	*	@var int Length
	* @var string Unit
	*/
	public function setInterval()
	{

		// Ensure we have 2 arguments to work with
		if (func_num_args() < 2)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}

		// Figure out which argument is which
		if (is_numeric(func_get_arg(0)))
		{
			$length_value = func_get_arg(0);
			$unit_value = strtolower(func_get_arg(1));
		}
		else if (is_numeric(func_get_arg(1)))
		{
			$length_value = func_get_arg(1);
			$unit_value = strtolower(func_get_arg(0));
		}
		else
		{
			$this->error[]="Invalid interval length in function '".__FUNCTION__."()'";
			return;
		}

		// Make sure we have the right unit
		if ($unit_value!='days' && $unit_value!='months')
		{
			$this->error[]="Invalid interval unit in function '".__FUNCTION__."()'";
			return;
		}

		// Make sure the number of days is within range
		if ($unit_value=='days' && ($length_value > 365 || $length_value < 7))
		{
			$this->error[]="Invalid interval length in function '".__FUNCTION__."()'. For daily recurrence, value must fall between 7 and 365";
			return;
		}
		elseif ($unit_value=='months' && ($length_value > 12 || $length_value < 1))
		{
			$this->error[]="Invalid interval length in function '".__FUNCTION__."()'. For monthly recurrence, value must fall between 1 and 12";
			return;
		}

		// Add the values
		$this->length = $length_value;
		$this->unit = $unit_value;
		return;
	}

	public function setPaymentDetails()
	{
		// Ensure we have arguments to work with
		if (func_num_args() == 2 || func_num_args() == 4)
		{
			if (func_num_args() == 2)
			{
				$arg1=func_get_arg(0);
				$arg2=func_get_arg(1);
				if (strlen($arg1)>=13 && strlen($arg1)<=16 && is_numeric($arg1))
				{
					$cc=$arg1;
					$exp=strtotime($arg2);
				}
				else if (strlen($arg2)>=13 && strlen($arg2)<=16 && is_numeric($arg2))
				{
					$cc=$arg2;
					$exp=strtotime($arg1);
				}
				else
				{
					$this->error[]="Credit Card number invalid in function '".__FUNCTION__."()'";
					return;
				}
				if (!$exp)
				{
					$this->error[]="Expiration Date invalid in function '".__FUNCTION__."()'";
					return;
				}
				$this->cardNumber = $this->dom->createElement('cardNumber',$cc);
				$this->expirationDate = $this->dom->createElement('expirationDate',date('Y-m',$exp));
			}
			else
			{
				$accountType=strtolower(func_get_arg(0));
				$routingNumber=func_get_arg(1);
				$accountNumber=func_get_arg(2);
				$nameOnAccount=func_get_arg(3);
				if ($accountType=='checking' || $accountType=='businesschecking' || $accountType=='savings')
				{
					if ($accountType=='businesschecking')
					{
						$accountType='businessChecking';
						$echeckType='CCD';
					}
					else
					{
						$echeckType='WEB';
					}
					$this->accountType = $this->dom->createElement('accountType',$accountType);
					$this->echeckType = $this->dom->createElement('echeckType',$echeckType);
				}
				else
				{
					$this->error[]="Invalid account type in function '".__FUNCTION__."()'";
				}
				if (strlen($routingNumber)==9 && is_numeric($routingNumber))
					$this->routingNumber = $this->dom->createElement('routingNumber',$routingNumber);
				else
					$this->error[]="Invalid routing number in function '".__FUNCTION__."()'";
				if (strlen($accountNumber)>=5 && strlen($accountNumber) <=17 && is_numeric($accountNumber))
					$this->accountNumber = $this->dom->createElement('accountNumber',$accountNumber);
				else
					$this->error[]="Invalid account number in function '".__FUNCTION__."()'";
				$this->nameOnAccount = $this->dom->createElement('nameOnAccount', substr($this->xmlEncode($nameOnAccount),0,22));
			}
		}
		else
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}

	}

/**
	* setOrderDetails() (optional)
	* @var string invoice number
	* @var string description
	*/
	public function setOrderDetails()
	{
		// Ensure we have some arguments to work with
		if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		
		$arg1=func_get_arg(0);
		if (func_num_args()==2)
			$arg2=func_get_arg(1);
		if (is_numeric($arg1))
		{
			$invoiceNumber=$arg1;
			$description=$arg2;
		}
		else
		{
			$invoiceNumber=$arg2;
			$description=$arg1;
		}
		if ($invoiceNumber)
			$this->invoiceNumber = $this->dom->createElement('invoiceNumber',substr($invoiceNumber,0,20));
		if ($description)
			$this->description = $this->dom->createElement('description',substr($this->xmlEncode($description),0,255));
		return;
	}

/**
	* setCustomerId()
	* Sets the customer ID
	*/
	public function setCustomerId()
	{
		// Ensure we have an argument to work with
		if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->id = $this->dom->createElement('id',substr($value,0,20));
		return;
	}

/**
	* setCustomerEmail()
	* Sets the customer's email address
	*/
	public function setCustomerEmail()
	{
		// Ensure we have an argument to work with
		if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->email = $this->dom->createElement('email',substr($this->xmlEncode($value),0,255));
		return;
	}

/**
	* setCustomerPhone()
	* Sets the customer's phone number
	*/
	public function setCustomerPhone()
	{
		// Ensure we have an argument to work with
		if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->phoneNumber = $this->dom->createElement('phoneNumber',substr($value,0,25));
		return;
	}

/**
	* setCustomerFax()
	* Sets the customer's fax number
	*/
	public function setCustomerFax()
	{
		// Ensure we have an argument to work with
		if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->faxNumber = $this->dom->createElement('faxNumber',substr($value,0,25));
		return;
	}

/**
	* setBillingName()
	* Sets the billing name
	*/
	public function setBillingName()
	{
		// Ensure we have an argument to work with
				if (func_num_args()!=2)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$first=func_get_arg(0);
		$last=func_get_arg(1);
		$this->setBillingFirstName($first);
		$this->setBillingLastName($last);
		return;
	}

/**
	* setBillingFirstName()
	* Sets the billing first name
	*/
	public function setBillingFirstName()
	{
		// Ensure we have an argument to work with
		if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->billingFirstName = $this->dom->createElement('firstName',substr($value,0,50));
		return;
	}

/**
	* setBillingLastName()
	* Sets the billing last name
	*/
	public function setBillingLastName()
	{
		// Ensure we have an argument to work with
		if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->billingLastName = $this->dom->createElement('lastName',substr($value,0,50));
		return;
	}

/**
	* setBillingCompany()
	* Sets the billing company
	*/
	public function setBillingCompany()
	{
		// Ensure we have an argument to work with
		if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->billingCompany = $this->dom->createElement('company',substr($value,0,50));
		return;
	}

/**
	* setBillingAddress()
	* Sets the billing address
	*/
	public function setBillingAddress()
	{
		// Ensure we have an argument to work with
		if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->billingAddress = $this->dom->createElement('address',substr($value,0,60));
		if (func_num_args()==4)
		{
			$city=func_get_arg(1);
			$state=func_get_arg(2);
			$zip=func_get_arg(3);
			$this->setBillingCity($city);
			$this->setBillingState($state);
			$this->setBillingZip($zip);
		}
		return;
	}

/**
	* setBillingCity()
	* Sets the billing city
	*/
	public function setBillingCity()
	{
		// Ensure we have an argument to work with
		if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->billingCity = $this->dom->createElement('city',substr($value,0,40));
		return;
	}

/**
	* setBillingState()
	* Sets the billing state
	*/
	public function setBillingState()
	{
		// Ensure we have an argument to work with
		if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		if (strlen($value)>2)
			$value=$this->convertState($value);
		$this->billingState = $this->dom->createElement('state',substr($value,0,2));
		return;
	}

/**
	* setBillingZip()
	* Sets the billing zip
	*/
	public function setBillingZip()
	{
		// Ensure we have an argument to work with
		if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		if (!is_numeric($value) || !(strlen($value)==5 || strlen($value)==10))
			$this->error[]="Invalid value for zip in function '".__FUNCTION__."()'";
		else
			$this->billingZip = $this->dom->createElement('zip',substr($value,0,20));
		return;
	}

/**
	* setBillingCountry()
	* Sets the billing country
	*/
	public function setBillingCountry()
	{
		// Ensure we have an argument to work with
		if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->billingCountry = $this->dom->createElement('country',substr($value,0,60));
		return;
	}

/**
	* setShippingName()
	* Sets the shipping name
	*/
	public function setShippingName()
	{
		// Ensure we have an argument to work with
				if (func_num_args()!=2)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$first=func_get_arg(0);
		$last=func_get_arg(1);
		$this->setShippingFirstName($first);
		$this->setShippingLastName($last);
		return;
	}

/**
	* setShippingFirstName()
	* Sets the shipping first name
	*/
	public function setShippingFirstName()
	{
		// Ensure we have an argument to work with
				if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->shippingFirstName = $this->dom->createElement('firstName',substr($value,0,50));
		return;
	}

/**
	* setShippingLastName()
	* Sets the shipping last name
	*/
	public function setShippingLastName()
	{
		// Ensure we have an argument to work with
				if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->shippingLastName = $this->dom->createElement('lastName',substr($value,0,50));
		return;
	}

/**
	* setShippingCompany()
	* Sets the shipping company
	*/
	public function setShippingCompany()
	{
		// Ensure we have an argument to work with
				if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->shippingCompany = $this->dom->createElement('company',substr($value,0,50));
		return;
	}

/**
	* setShippingAddress()
	* Sets the shipping address
	*/
	public function setShippingAddress()
	{
		// Ensure we have an argument to work with
				if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->shippingAddress = $this->dom->createElement('address',substr($value,0,60));
		if (func_num_args()==4)
		{
			$city=func_get_arg(1);
			$state=func_get_arg(2);
			$zip=func_get_arg(3);
			$this->setShippingCity($city);
			$this->setShippingState($state);
			$this->setShippingZip($zip);
		}
		return;
	}

/**
	* setShippingCity()
	* Sets the shipping city
	*/
	public function setShippingCity()
	{
		// Ensure we have an argument to work with
				if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->shippingCity = $this->dom->createElement('city',substr($value,0,40));
		return;
	}

/**
	* setShippingState()
	* Sets the shipping state
	*/
	public function setShippingState()
	{
		// Ensure we have an argument to work with
				if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		if (strlen($value)>2)
			$value=$this->convertState($value);
		$this->shippingState = $this->dom->createElement('state',substr($value,0,2));
		return;
	}

/**
	* setShippingZip()
	* Sets the shipping zip
	*/
	public function setShippingZip()
	{
		// Ensure we have an argument to work with
				if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		if (!is_numeric($value) || !(strlen($value)==5 || strlen($value)==10))
			$this->error[]="Invalid value for zip in function '".__FUNCTION__."()'";
		else
			$this->shippingZip = $this->dom->createElement('zip',substr($value,0,20));
		return;
	}

/**
	* setShippingCountry()
	* Sets the shipping country
	*/
	public function setShippingCountry()
	{
		// Ensure we have an argument to work with
				if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->shippingCountry = $this->dom->createElement('country',substr($value,0,60));
		return;
	}

/**
	* setSubscriptionId()
	* Sets the subscription ID for Update and Cancel
	*/
	public function setSubscriptionId()
	{
		// Ensure we have an argument to work with
		if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->subscriptionId = $this->dom->createElement('subscriptionId',substr($value,0,13));
		return;
	}

    /**
	 * setSearchType()
	 * Sets the searchType for GetSubscriptionList
	 */
	public function setSearchType()
	{
		// Ensure we have an argument to work with
		if (func_num_args()<1)
		{
			$this->error[]="Invalid number of arguments in function '".__FUNCTION__."()'";
			return;
		}
		$value=func_get_arg(0);
		$this->searchType = $this->dom->createElement('searchType',substr($value,0,40));
		return;
	}

    /**
	 * setOrderBy()
	 *
     * id
     * name
     * status
     * createTimeStampUTC
     * lastName
     * firstName
     * accountNumber (ordered by last 4 digits only)
     * amount
     * pastOccurences
     *
     * @param string $orderBy
	 */
	protected function setOrderBy($orderBy) {
		$this->orderBy = $this->dom->createElement('orderBy',$orderBy);
		return;
	}

    /**
	 * setOrderDescending()
	 * Sets the sorting for GetSubscriptionList
     *
     * @param bool $descending
	 */
	protected function setOrderDescending($descending) {
		$this->orderDescending = $this->dom->createElement('orderDescending',$descending);
		return;
	}

    /**
	 * setLimit()
	 * Sets the sorting for GetSubscriptionList
     *
     * @param int $limit 1-1000
	 */
	protected function setLimit($limit) {
		$this->limit = $this->dom->createElement('limit',$limit);
		return;
	}

    /**
	 * setOffset()
	 * Sets the sorting for GetSubscriptionList
     *
     * @param int $offset 1-10000
	 */
	protected function setOffset($offset) {
		$this->offset = $this->dom->createElement('offset',$offset);
		return;
	}
	
	public function UpdateSubscriptionRequest()
	{
		if (count($this->error)>0)
		{
			$this->error[]="Can not proceed in function '".__FUNCTION__."()' with errors present";
			return;
		}
		// Create and add parent node
		$this->ARBUpdateSubscriptionRequest = $this->dom->createElementNS(self::auth_net_namespace, 'ARBUpdateSubscriptionRequest');
		$this->dom->appendChild($this->ARBUpdateSubscriptionRequest);

		// Add the merchant authentication info
		$this->buildMerchantAuthentication();
		$this->ARBUpdateSubscriptionRequest->appendChild($this->merchantAuthentication);

		// If we have a reference ID, add it
		if ($this->refId)
				$this->ARBUpdateSubscriptionRequest->appendChild($this->refId);
		
		// And, most required
		if (!$this->subscriptionId)
			$this->error[]="A subscription ID is required to in function '".__FUNCTION__."()'";
		else
			$this->ARBUpdateSubscriptionRequest->appendChild($this->subscriptionId);
		
		// Create and add the subscription info
		$this->subscription = $this->dom->createElement('subscription');
		$this->ARBUpdateSubscriptionRequest->appendChild($this->subscription);	
		if ($this->totalOccurrencesChanged)
		{
			$this->paymentSchedule=$this->dom->createElement('paymentSchedule');
			$this->subscription->appendChild($this->paymentSchedule);
			$this->paymentSchedule->appendChild($this->dom->createElement('totalOccurrences',$this->totalOccurrences));
		}
		if ($this->cardNumber || $this->accountNumber)
		{
			$this->payment=$this->dom->createElement('payment');
			$this->subscription->appendChild($this->payment);
			if ($this->cardNumber)
			{
				$this->creditCard = $this->dom->createElement('creditCard');
				$this->payment->appendChild($this->creditCard);
				$this->creditCard->appendChild($this->cardNumber);
				$this->creditCard->appendChild($this->expirationDate);
			}
			else if ($this->accountNumber)
			{
				$this->bankAccount = $this->dom->createElement('bankAccount');
				$this->payment->appendChild($this->bankAccount);
				$this->bankAccount->appendChild($this->accountType);
				$this->bankAccount->appendChild($this->routingNumber);
				$this->bankAccount->appendChild($this->accountNumber);
				$this->bankAccount->appendChild($this->nameOnAccount);
				$this->bankAccount->appendChild($this->echeckType);
			}
		}
		if ($this->invoiceNumber || $this->description)
		{
			$this->order = $this->dom->createElement('order');
			$this->subscription->appendChild($this->order);
			if ($this->invoiceNumber)
				$this->order->appendChild($this->invoiceNumber);
			if ($this->description)
				$this->order->appendChild($this->description);
		}
		if ($this->id || $this->email || $this->phoneNumber || $this->faxNumber)
		{
			$this->customer = $this->dom->createElement('customer');
			$this->subscription->appendChild($this->customer);
			if ($this->id)
				$this->customer->appendChild($this->id);
			if ($this->email)
				$this->customer->appendChild($this->email);
			if ($this->phoneNumber)
				$this->customer->appendChild($this->phoneNumber);
			if ($this->faxNumber)
				$this->customer->appendChild($this->faxNumber);
		}
		// Set Billing Info
		if ($this->billingFirstName || $this->billingLastName || $this->billingCity || $this->billingState || $this->billingZip || $this->billingCountry)
		{
		$this->billTo = $this->dom->createElement('billTo');
		$this->subscription->appendChild($this->billTo);
		if ($this->billingFirstName)
			$this->billTo->appendChild($this->billingFirstName);
		if ($this->billingLastName)
			$this->billTo->appendChild($this->billingLastName);
		if ($this->billingCompany)
			$this->billTo->appendChild($this->billingCompany);
		if ($this->billingAddress)
			$this->billTo->appendChild($this->billingAddress);
		if ($this->billingCity)
			$this->billTo->appendChild($this->billingCity);
		if ($this->billingState)
			$this->billTo->appendChild($this->billingState);
		if ($this->billingZip)
			$this->billTo->appendChild($this->billingZip);
		if ($this->billingCountry)
			$this->billTo->appendChild($this->billingCountry);
		}
		
		// Set Shipping Info
		if ($this->shippingFirstName || $this->shippingLastName || $this->shippingCity || $this->shippingState || $this->shippingZip || $this->shippingCountry)
		{
			$this->shipTo = $this->dom->createElement('shipTo');
			$this->subscription->appendChild($this->shipTo);
			if ($this->shippingFirstName)
				$this->shipTo->appendChild($this->shippingFirstName);
			if ($this->shippingLastName)
				$this->shipTo->appendChild($this->shippingLastName);
			if ($this->shippingCompany)
				$this->shipTo->appendChild($this->shippingCompany);
			if ($this->shippingAddress)
				$this->shipTo->appendChild($this->shippingAddress);
			if ($this->shippingCity)
				$this->shipTo->appendChild($this->shippingCity);
			if ($this->shippingState)
				$this->shipTo->appendChild($this->shippingState);
			if ($this->shippingZip)
				$this->shipTo->appendChild($this->shippingZip);
			if ($this->shippingCountry)
				$this->shipTo->appendChild($this->shippingCountry);
		}
		
		if (count($this->error)==0)
			$this->submit();
		return;
		
	}
	
	public function CancelSubscriptionRequest()
	{
		if (count($this->error)>0)
		{
			$this->error[]="Can not proceed in function '".__FUNCTION__."()' with errors present";
			return;
		}
		// Create and add parent node
		$this->ARBCancelSubscriptionRequest = $this->dom->createElementNS(self::auth_net_namespace, 'ARBCancelSubscriptionRequest');
		$this->dom->appendChild($this->ARBCancelSubscriptionRequest);

		// Add the merchant authentication info
		$this->buildMerchantAuthentication();
		$this->ARBCancelSubscriptionRequest->appendChild($this->merchantAuthentication);

		// If we have a reference ID, add it
		if ($this->refId)
				$this->ARBCancelSubscriptionRequest->appendChild($this->refId);
		
		// And, last but most required
		if (!$this->subscriptionId)
			$this->error[]="A subscription ID is required to in function '".__FUNCTION__."()'";
		else
			$this->ARBCancelSubscriptionRequest->appendChild($this->subscriptionId);
		
		if (count($this->error)==0)
			$this->submit();
		return;
	}

    /**
     * GetSubscriptionListRequest()
     *
     * @param string $searchType (cardExpiringThisMonth, subscriptionActive, subscriptionInactive, subscriptionExpiringThisMonth)
     * @param string $orderBy (id, name, status, createTimeStampUTC, lastName, firstName, accountNumber (ordered by last 4 digits only), amount, pastOccurences)
     * @param bool $orderDescending
     * @param int $limit 1-1000
     * @param int $offset 1-10000
     */
    public function GetSubscriptionListRequest( $searchType, $orderBy = null, $orderDescending = null, $limit = null, $offset = null )
    {
        if (count($this->error)>0)
        {
            $this->error[]="Can not proceed in function '".__FUNCTION__."()' with errors present";
            return;
        }
        // Create and add parent node
        $this->ARBGetSubscriptionListRequest = $this->dom->createElementNS(self::auth_net_namespace, 'ARBGetSubscriptionListRequest');
        $this->dom->appendChild($this->ARBGetSubscriptionListRequest);

        // Add the merchant authentication info
        $this->buildMerchantAuthentication();
        $this->ARBGetSubscriptionListRequest->appendChild($this->merchantAuthentication);

        // If we have a reference ID, add it
        $this->ARBGetSubscriptionListRequest->appendChild($this->dom->createElement('searchType', $searchType));

        // If we have sorting
        if ($orderBy && $orderDescending) {
            $sorting = $this->ARBGetSubscriptionListRequest->appendChild($this->dom->createElement('sorting'));
            $sorting->appendChild($this->dom->createElement('orderBy', $orderBy));
            $sorting->appendChild($this->dom->createElement('orderDescending', $orderDescending));
        }

        // If we have paging
        if ($limit && $offset) {
            $paging = $this->ARBGetSubscriptionListRequest->appendChild($this->dom->createElement('paging'));
            $paging->appendChild($this->dom->createElement('limit', $limit));
            $paging->appendChild($this->dom->createElement('offset', $offset));
        }

        if (count($this->error)==0)
            $this->submit();
        return;
    }

	public function CreateSubscriptionRequest()
	{
		if (count($this->error)>0)
		{
			$this->error[]="Can not proceed in function '".__FUNCTION__."()' with errors present";
			return;
		}
		// Create and add parent node
		$this->ARBCreateSubscriptionRequest = $this->dom->createElementNS(self::auth_net_namespace, 'ARBCreateSubscriptionRequest');
		$this->dom->appendChild($this->ARBCreateSubscriptionRequest);

		// Add the merchant authentication info
		$this->buildMerchantAuthentication();
		$this->ARBCreateSubscriptionRequest->appendChild($this->merchantAuthentication);

		// If we have a reference ID, add it
		if ($this->refId)
			$this->ARBCreateSubscriptionRequest->appendChild($this->refId);

		// Create and add the subscription info
		$this->subscription = $this->dom->createElement('subscription');
		$this->ARBCreateSubscriptionRequest->appendChild($this->subscription);
		if ($this->name)
			$this->subscription->appendChild($this->name);
		$this->paymentSchedule=$this->dom->createElement('paymentSchedule');
		$this->subscription->appendChild($this->paymentSchedule);
		$this->interval=$this->dom->createElement('interval');
		$this->paymentSchedule->appendChild($this->interval);
		$this->interval->appendChild($this->dom->createElement('length',$this->length));
		$this->interval->appendChild($this->dom->createElement('unit',$this->unit));
		$this->paymentSchedule->appendChild($this->dom->createElement('startDate',$this->startDate));
		$this->paymentSchedule->appendChild($this->dom->createElement('totalOccurrences',$this->totalOccurrences));
		if (!$this->amount)
			$this->error[]="An amount is required to in function '".__FUNCTION__."()'";
		else
			$this->subscription->appendChild($this->amount);
		if ($this->trialOccurrences)
		{
			$this->paymentSchedule->appendChild($this->trialOccurrences);
			$this->subscription->appendChild($this->trialAmount);
		}
		$this->payment=$this->dom->createElement('payment');
		$this->subscription->appendChild($this->payment);
		if ($this->cardNumber)
		{
			$this->creditCard = $this->dom->createElement('creditCard');
			$this->payment->appendChild($this->creditCard);
			$this->creditCard->appendChild($this->cardNumber);
			$this->creditCard->appendChild($this->expirationDate);
		}
		else if ($this->accountNumber)
		{
			$this->bankAccount = $this->dom->createElement('bankAccount');
			$this->payment->appendChild($this->bankAccount);
			$this->bankAccount->appendChild($this->accountType);
			$this->bankAccount->appendChild($this->routingNumber);
			$this->bankAccount->appendChild($this->accountNumber);
			$this->bankAccount->appendChild($this->nameOnAccount);
			$this->bankAccount->appendChild($this->echeckType);
		}
		else
		{
			$this->error[]="A payment method is required in function '".__FUNCTION__."()'";
		}
		if ($this->invoiceNumber || $this->description)
		{
			$this->order = $this->dom->createElement('order');
			$this->subscription->appendChild($this->order);
			if ($this->invoiceNumber)
				$this->order->appendChild($this->invoiceNumber);
			if ($this->description)
				$this->order->appendChild($this->description);
		}
		if ($this->id || $this->email || $this->phoneNumber || $this->faxNumber)
		{
			$this->customer = $this->dom->createElement('customer');
			$this->subscription->appendChild($this->customer);
			if ($this->id)
				$this->customer->appendChild($this->id);
			if ($this->email)
				$this->customer->appendChild($this->email);
			if ($this->phoneNumber)
				$this->customer->appendChild($this->phoneNumber);
			if ($this->faxNumber)
				$this->customer->appendChild($this->faxNumber);
		}
		
		// Set Billing Info
		$this->billTo = $this->dom->createElement('billTo');
		$this->subscription->appendChild($this->billTo);
		if (!$this->billingFirstName || !$this->billingLastName)
			$this->error[]="A name is required in function '".__FUNCTION__."()'";
		else
		{
			$this->billTo->appendChild($this->billingFirstName);
			$this->billTo->appendChild($this->billingLastName);
		}
		if ($this->billingCompany)
			$this->billTo->appendChild($this->billingCompany);
		if ($this->billingAddress)
			$this->billTo->appendChild($this->billingAddress);
		if ($this->billingCity)
			$this->billTo->appendChild($this->billingCity);
		if ($this->billingState)
			$this->billTo->appendChild($this->billingState);
		if ($this->billingZip)
			$this->billTo->appendChild($this->billingZip);
		if ($this->billingCountry)
			$this->billTo->appendChild($this->billingCountry);

		// Set Shipping Info
		if ($this->shippingFirstName || $this->shippingLastName || $this->shippingCity || $this->shippingState || $this->shippingZip || $this->shippingCountry)
		{
			$this->shipTo = $this->dom->createElement('shipTo');
			$this->subscription->appendChild($this->shipTo);
			if ($this->shippingFirstName)
				$this->shipTo->appendChild($this->shippingFirstName);
			if ($this->shippingLastName)
				$this->shipTo->appendChild($this->shippingLastName);
			if ($this->shippingCompany)
				$this->shipTo->appendChild($this->shippingCompany);
			if ($this->shippingAddress)
				$this->shipTo->appendChild($this->shippingAddress);
			if ($this->shippingCity)
				$this->shipTo->appendChild($this->shippingCity);
			if ($this->shippingState)
				$this->shipTo->appendChild($this->shippingState);
			if ($this->shippingZip)
				$this->shipTo->appendChild($this->shippingZip);
			if ($this->shippingCountry)
				$this->shipTo->appendChild($this->shippingCountry);
		}
		if (count($this->error)==0)
			$this->submit();
		return;
	}












//-------------------------------------------------------------------------------------------------------
// Private Functions
//-------------------------------------------------------------------------------------------------------

/**
	* submit()
	* performs curl submission to authorize
	*/
	private function submit()
	{
		if ($this->DEBUG)
			print $this->dom->saveXML();
		else
		{
			$ch = curl_init(self::auth_net_URL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->dom->saveXML());
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			$this->response = curl_exec($ch);
			$this->request = $this->dom->saveXML();
			curl_close ($ch);
			$this->processResponse();
		}
		return;
	}

/**
	* processResponse()
	* parses response into an array for user output
	*/
	private function processResponse()
	{
		$this->success=false;
		$rdom = new DomDocument();
		$rdom->loadXML($this->response);
		$parent=$rdom->firstChild;
		switch ($parent->nodeName)
		{
			case 'ARBCreateSubscriptionResponse':
				$xml = @simplexml_load_string( $this->response );
				$this->results['refId'] 			= $xml->ARBCreateSubscriptionResponse->refId;
				$this->results['result']			= $xml->messages->resultCode;
				$this->results['code']				= $xml->messages->message->code;
				$this->results['text']				= $xml->messages->message->text;
				$this->results['subscriptionId'] 	= $xml->subscriptionId;
				
				if (strtolower($this->results['result'])=='ok')
					$this->success=true;
				else
				{
					$this->error[$this->results['code']] = $this->results['text'];
					//foreach ($this->results as $key=>$val) unset($this->results[$key]);
				}
			break;
			
			case 'ARBUpdateSubscriptionResponse':
			case 'ARBCancelSubscriptionResponse':
				
				$refId = $parent->firstChild;
				$messages = $refId->nextSibling;
				$subscriptionId = $messages->nextSibling;
				if ($refId)
					$this->results['refId']=$refId->nodeValue;
				$this->results['result']=$messages->firstChild->nodeValue;
				$this->results['code']=$messages->firstChild->nextSibling->firstChild->nodeValue;
				$this->results['text']=$messages->firstChild->nextSibling->firstChild->nextSibling->nodeValue;
				if ($subscriptionId)
					$this->results['subscriptionId']=$subscriptionId->nodeValue;
				if (strtolower($this->results['result'])=='ok')
					$this->success=true;
				else
				{
					$this->error[$this->results['code']] = $this->results['text'];
					foreach ($this->results as $key=>$val) unset($this->results[$key]);
				}
				break;
			case 'ErrorResponse':
				$this->error[$parent->firstChild->firstChild->nextSibling->firstChild->nodeValue]=$parent->firstChild->firstChild->nextSibling->firstChild->nextSibling->nodeValue;
				break;
		}
		return;
	}

/**
	* buildMerchantAuthentication()
	*	Initializes the merchantAuthentication node
	*/
	private function buildMerchantAuthentication()
	{
		$this->merchantAuthentication = $this->dom->createElement('merchantAuthentication');
		$this->merchantAuthentication->appendChild($this->dom->createElement('name', self::auth_net_login_id));
		$this->merchantAuthentication->appendChild($this->dom->createElement('transactionKey', self::auth_net_tran_key));
		return;
	}

/**
	* setDefaults()
	* Initializes certain optional values;
	*/
	private function setDefaults()
	{
		$this->unit='months';
		$this->length='1';
		$this->startDate=date('Y-m-d');
		$this->totalOccurrences='9999';
		$this->totalOccurrencesChanged=false;
	}

/**
	* xmlEncode()
	*	@var string
	*/
	private function xmlEncode($v)
	{
		$v = str_replace("&", "&amp;", $v);
		$v = str_replace("<", "&lt;", $v);
		$v = str_replace(">", "&gt;", $v);
		$v = str_replace("'", "&quot;", $v);
		$v = str_replace("\"", "&quote;", $v);
		return $v;
	}
	
/**
	* convertState()
	* @var state
	*/
	private function convertState($state)
	{
		$states = array(
										'AL'=>'ALABAMA',
										'AK'=>'ALASKA',
		 								'AZ'=>'ARIZONA',
	 									'AR'=>'ARKANSAS',
										'CA'=>'CALIFORNIA',
										'CO'=>'COLORADO',
										'CT'=>'CONNECTICUT',
										'DE'=>'DELAWARE',
										'DC'=>'DISTRICT OF COLUMBIA',
										'FL'=>'FLORIDA',
										'GA'=>'GEORGIA',
										'HA'=>'HAWAII',
										'ID'=>'IDAHO',
										'IL'=>'ILLINOIS',
										'IN'=>'INDIANA',
										'IA'=>'IOWA',
										'KS'=>'KANSAS',
										'KY'=>'KENTUCKY',
										'LA'=>'LOUISIANA',
										'ME'=>'MAINE',
										'MD'=>'MARYLAND',
										'MA'=>'MASSACHUSETTS',
										'MI'=>'MICHIGAN',
										'MN'=>'MINNESOTA',
										'MS'=>'MISSISSIPPI',
										'MO'=>'MISSOURI',
										'MT'=>'MONTANA',
										'NE'=>'NEBRASKA',
										'NV'=>'NEVADA',
										'NH'=>'NEW HAMPSHIRE',
										'NJ'=>'NEW JERSEY',
										'NM'=>'NEW MEXICO',
										'NY'=>'NEW YORK',
										'NC'=>'NORTH CAROLINA',
										'ND'=>'NORTH DAKOTA',
										'OH'=>'OHIO',
										'OK'=>'OKLAHOMA',
										'OR'=>'OREGON',
										'PA'=>'PENNSYLVANIA',
										'RI'=>'RHODE ISLAND',
										'SC'=>'SOUTH CAROLINA',
										'SD'=>'SOUTH DAKOTA',
										'TN'=>'TENNESSEE',
										'TX'=>'TEXAS',
										'UT'=>'UTAH',
										'VT'=>'VERMONT',
										'VA'=>'VIRGINIA',
										'WA'=>'WASHINGTON',
										'WV'=>'WEST VIRGINIA',
										'WI'=>'WISCONSIN',
										'WY'=>'WYOMING');
		return array_search(strtoupper($state),$states);
	}

}