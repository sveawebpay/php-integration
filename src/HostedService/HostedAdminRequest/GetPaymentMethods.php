<?php

namespace Svea\WebPay\HostedService\HostedAdminRequest;

/**
 * Returns an array of SystemPaymentMethods with all paymentmethods
 * conected to the merchantId and/or ClientId
 *
 * @author anne-hal
 */
class GetPaymentMethods
{
	private $method = "getpaymentmethods";
	private $config;
	private $countryCode = "SE";	//Default SE

	/**
	 * @deprecated 2.0 Use class ListPaymentMethods instead.
	 */
	function __construct($config)
	{
		$this->config = $config;
	}

	public function setContryCode($countryCodeAsString)
	{
		$this->countryCode = $countryCodeAsString;

		return $this;
	}

	/**
	 * Wrapper for ListPaymentMethods->doRequest
	 * @deprecated 2.0 Use class ListPaymentMethods instead.
	 */
	public function doRequest()
	{
		$request = new ListPaymentMethods($this->config);
		$request->countryCode = $this->countryCode;
		$response = $request->doRequest();

		return $response->paymentmethods;
	}
}