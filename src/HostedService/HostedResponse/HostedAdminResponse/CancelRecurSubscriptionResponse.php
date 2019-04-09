<?php

namespace Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse;

use SimpleXMLElement;

/**
 * CancelRecurSubscriptionResponse handles the cancel recur subscription response
 *
 * @author Fredrik Sundell for Svea WebPay
 */
class CancelRecurSubscriptionResponse extends HostedAdminResponse
{
    /**
     * CancelRecurSubscriptionResponse constructor.
     * @param \SimpleXMLElement $message
     * @param string $countryCode
     * @param \Svea\WebPay\Config\SveaConfigurationProvider $config
     */
    function __construct($message, $countryCode, $config)
    {
        parent::__construct($message, $countryCode, $config);
    }

    /**
     * formatXml() parses the lower transaction response xml into an object, and
     * then sets the response attributes accordingly.
     *
     * @param string $hostedAdminResponseXML hostedAdminResponse as xml
     */
    protected function formatXml($hostedAdminResponseXML)
    {
        $hostedAdminResponse = new SimpleXMLElement($hostedAdminResponseXML);

        if ((string)$hostedAdminResponse->statuscode == '0') {
            $this->accepted = 1;
            $this->resultcode = '0';
        } else {
            $this->accepted = 0;
            $this->setErrorParams((string)$hostedAdminResponse->statuscode);
        }
    }
}
