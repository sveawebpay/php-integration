<?php

namespace Svea\WebPay\HostedService\HostedAdminRequest;

use Svea\WebPay\Helper\Helper;
use Svea\WebPay\HostedService\HostedRequest;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\CancelRecurSubscriptionResponse;

/**
 * Inactivates an existing recur subscription so that no more recurs can be made on it.
 *
 * @author Fredrik Sundell
 */
class CancelRecurSubscription extends HostedRequest
{
    /*
     * @var string $conf ConfigurationProvider $conf
     */
    public $conf;

    /** 
     * @var string $subscriptionId Required. This is the subscription id returned with the initial transaction that set up the subscription response.
     */
    public $subscriptionId;

    /**
     * Usage: create an instance, set all required attributes, then call doRequest().
     * Required: $subscriptionId
     * @param ConfigurationProvider $config instance implementing Svea\WebPay\Config\ConfigurationProvider
     */
    function __construct($config)
    {
        $this->method = "cancelrecursubscription";
        parent::__construct($config);
    }

    protected function validateRequestAttributes()
    {
        $errors = array();
        $errors = $this->validateSubscriptionId($this, $errors);

        return $errors;
    }

    private function validateSubscriptionId($self, $errors)
    {
        if (isset($self->subscriptionId) == FALSE) {
            $errors['missing value'] = "subscriptionId is required. Use function setSubscriptionId() with the subscriptionId from the createOrder response.";
        }

        return $errors;
    }

    protected function createRequestXml()
    {
        $XMLWriter = new \XMLWriter();

        $XMLWriter->openMemory();
        $XMLWriter->setIndent(true);
        $XMLWriter->startDocument("1.0", "UTF-8");
        $XMLWriter->writeComment(Helper::getLibraryAndPlatformPropertiesAsJson($this->config));
        $XMLWriter->startElement($this->method);
        $XMLWriter->writeElement("subscriptionid", $this->subscriptionId);
        $XMLWriter->endElement();
        $XMLWriter->endDocument();

        return $XMLWriter->flush();
    }

    protected function parseResponse($message)
    {
        $countryCode = $this->countryCode;
        $config = $this->config;

        return new CancelRecurSubscriptionResponse($message, $countryCode, $config);
    }
}