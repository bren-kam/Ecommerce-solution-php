<?php
  //Desciption:
  //This is a SOAP client written in php to help you
  //consume the AvidMobile Marketing Center 2.1 SOAP API
  //
  //Comments:
  //This client was based off of
  //php PEAR SOAP
  //http://pear.php.net

//Your php PEAR SOAP client library
set_include_path("/usr/local/cpanel/base/horde/pear/php/");
require_once('SOAP/Client.php');

class AvidMobileSOAPClient
{
	private $params = null;
	private $response = null;
	private $client = null;  
	private $debug = true;
	
	private $username = null;
	private $password = null;
	private $customerid = null;
	private $credentiallevel = 0;
	private $source = null;
	public $retResellerID = 0;
	
	private function aTrace($message)
	{
		if ($this->debug)
		{
			//By default, we will echo
			//the output. Feel free to change 
			//this to suit your needs.
			//echo $message . "\n";
			//$retResellerID = substr($message, strstr($message, "ErrorDetails"))
			//echo $retResellerID;
		}
	}
	
	public function CreateGetResponseResultArray($result)
	{
		//Creates an easy-to-browse result array
		//
		//Example: 
		//Say your webservice returned 3 of these:
		//
		//array("setting1" => "value",
		//"setting2" => "value2",
		//"setting3" => "value3")
		//
		//After this method processes the result, you
		//would be able to do the following:
		//
		//Get "setting2" value for the second result
		//$result[1]["settings2"]
		//
		//Get "setting1" value for the first result
		//$result[0]["setting1"]
		
		$result_array = array();
		
		$i = 0;
		foreach ($result->Data as $k => $v)
		{
			foreach ($v as $_k => $_v)
			{	  
				$result_array[$i][$_v->Key] = $_v->Value;
			}
			
			$i++;
		}
		
		return $result_array;
	}
		
	public function CreateWebServiceParams($method,
										   $arguments)
	{						
		return array("Authentication" => array("Username" => $this->username,
											   "Password" => $this->password,
											   "CustomerId" => $this->customerid,
											   "Level" => $this->credentiallevel,
											   "Source" => $this->source),
					 "Method" => $method,
					 "Arguments" => $arguments);
	}
	
	public function __construct($wsdl_url,
								$username = null,
								$password = null,
								$customerid = null,
								$credentiallevel = 0,
								$source = null,
								$debug = true) 
	{
		$this->client = new SOAP_Client($wsdl_url);
		
		$this->username = $username;
		$this->password = $password;
		$this->customerid = $customerid;
		$this->credentiallevel = $credentiallevel;
		
		if (empty($source))
		{
			$this->source = "hostname: " . php_uname("n");
		}
		else
		{
			$this->source = $source;
		}

		$this->debug = $debug;

		//For purposes of this sample client,
		//we will skip the SSL certificate validation.
		//Depending on your security needs, you may want to
		//enforce SSL certificate validation.
		$this->client->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
		$this->client->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);
		
		if ($this->debug)
		{
			$this->client->setTrace(true);
		}
	}
	
	public function DoWebService($name, 
								 $params)
	{
		$ret = $this->client->call($name, 
								   $params);
		if (PEAR::isError($ret))
		{
			error_log("Error: " . $ret->getMessage());
			$ret = null;
		}
		
		$this->aTrace($this->client->wire);
		
		return $ret;
	}
	
	public function __destruct() {}
	
}
