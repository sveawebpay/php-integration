<?php

namespace Svea\WebPay\WebService\WebServiceResponse;

/**
 * Handles the Svea Webservice CloseOrder request response.
 *
 * @author anne-hal, Kristian Grossman-Madsen
 */
class CloseOrderResult extends WebServiceResponse
{
	/**
	 * CloseOrderResult constructor.
	 * @param $response
	 * @param $logs
	 */
	public function __construct($response, $logs)
	{
		// was request accepted?
		$this->accepted = $response->CloseOrderEuResult->Accepted; // false or 1
		$this->errormessage = isset($response->CloseOrderEuResult->ErrorMessage) ? $response->CloseOrderEuResult->ErrorMessage : "";

		if(isset($logs))
		{
			$this->logs = $logs;
		}

		// set response resultcode
		$this->resultcode = $response->CloseOrderEuResult->ResultCode;
	}
}
