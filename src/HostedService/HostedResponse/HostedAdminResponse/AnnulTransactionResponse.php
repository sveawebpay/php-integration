<?php

namespace Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse;

/**
 * AnnulTransactionResponse handles the annul transaction response
 *
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class AnnulTransactionResponse extends HostedAdminResponse
{
	/**
	 * @var string $transactionId transaction id that uniquely identifies the order at Svea
	 */
	public $transactionId;

	/**
	 * @var string $clientOrderNumber
	 */
	public $clientOrderNumber;

	/**
	 * AnnulTransactionResponse constructor.
	 * @param \SimpleXMLElement $message
	 * @param string $countryCode
	 * @param \Svea\WebPay\Config\SveaConfigurationProvider $config
	 */
	function __construct($message, $countryCode, $config)
	{
		parent::__construct($message, $countryCode, $config);
	}

	/**
	 * formatXml() parses the annul transaction response xml into an object, and
	 * then sets the response attributes accordingly.
	 *
	 * @param string $hostedAdminResponseXML hostedAdminResponse as xml
	 */
	protected function formatXml($hostedAdminResponseXML)
	{
		$hostedAdminResponse = new \SimpleXMLElement($hostedAdminResponseXML);

		if ((string)$hostedAdminResponse->statuscode == '0') {
			$this->accepted = 1;
			$this->resultcode = '0';
		} else {
			$this->accepted = 0;
			$this->setErrorParams((string)$hostedAdminResponse->statuscode);
		}
		$this->transactionId = (string)$hostedAdminResponse->transaction['id'];

		$this->clientOrderNumber = (string)$hostedAdminResponse->transaction->customerrefno;
	}
}
