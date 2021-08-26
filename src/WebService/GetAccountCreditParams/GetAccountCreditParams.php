<?php

namespace Svea\WebPay\WebService\GetAccountCreditParams;

use Svea\WebPay\Helper\Helper;
use Svea\WebPay\Response\SveaResponse;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\WebService\SveaSoap\SveaAuth;
use Svea\WebPay\WebService\SveaSoap\SveaDoRequest;
use Svea\WebPay\WebService\SveaSoap\SveaRequest;


/**
 * Use getAccountCreditParams() to fetch all campaigns associated with a given client number.
 *
 * Retrieves information about all the campaigns that are associated with the
 * current Client. Use this information to display information about the possible
 * AccountCredit options to customers. The returned CampaignCode is used when
 * creating a AccountCredit order.
 *
 * @author Svea Webpay
 */
class GetAccountCreditParams
{
	public $testmode = false;
	public $object;
	public $conf;
	public $countryCode;
	public $logging = false;

	function __construct($config)
	{
		$this->conf = $config;
	}

	/**
	 * Required
	 *
	 * @param string $countryCodeAsString
	 * @return $this
	 */
	public function setCountryCode($countryCodeAsString)
	{
		$this->countryCode = $countryCodeAsString;

		return $this;
	}

	/**
	 * Optional
	 *
	 * @param bool $logging
	 * @return $this
	 */
	public function enableLogging($logging)
	{
		$this->logging = $logging;

		return $this;
	}

	/**
	 * Prepares and sends request
	 *
	 * @return \Svea\WebPay\WebService\WebServiceResponse\PaymentPlanParamsResponse
	 */
	public function doRequest()
	{
		$requestObject = $this->prepareRequest();
		$request = new SveaDoRequest($this->conf, ConfigurationProvider::ACCOUNTCREDIT_TYPE, "GetAccountCreditParamsEu", $requestObject, $this->logging);

		$responseObject = new SveaResponse($request->result['requestResult'], "", NULL, NULL,isset($request->result['logs']) ? $request->result['logs'] : NULL);

		return $responseObject->response;
	}

	/**
	 * @return SveaRequest
	 */
	public function prepareRequest()
	{
		$auth = new SveaAuth(
			$this->conf->getUsername(ConfigurationProvider::ACCOUNTCREDIT_TYPE, $this->countryCode),
			$this->conf->getPassword(ConfigurationProvider::ACCOUNTCREDIT_TYPE, $this->countryCode),
			$this->conf->getClientNumber(ConfigurationProvider::ACCOUNTCREDIT_TYPE, $this->countryCode)
		);

		$object = new SveaRequest();
		$object->request = (object)["Auth" => $auth];

		return $object;
	}
}
