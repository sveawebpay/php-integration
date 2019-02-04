<?php

namespace Svea\WebPay\HostedService\HostedAdminRequest;

use Svea\WebPay\Helper\Helper;
use Svea\WebPay\HostedService\HostedRequest;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\QueryTransactionResponse;

/**
 * Query information about an existing card or direct bank transaction.
 *
 * Note that this only supports queries based on ClientOrderNumber
 *
 * @author Fredrik Sundell
 */
class QueryTransactionByCustomerRefNo extends HostedRequest
{
    /**
     * @var string $customerRefNo Required.
     */
    public $customerRefNo;

    /**
     * Usage: create an instance, set all required attributes, then call doRequest().
     * Required: $customerRefNo
     * @param ConfigurationProvider $config instance implementing Svea\WebPay\Config\ConfigurationProvider
     */
    function __construct($config)
    {
        $this->method = "querycustomerrefno";
        parent::__construct($config);
    }

    protected function validateRequestAttributes()
    {
        $errors = array();
        $errors = $this->validateClientOrderNumber($this, $errors);

        return $errors;
    }

    private function validateClientOrderNumber($self, $errors)
    {
        if (isset($self->customerRefNo) == FALSE) {
            $errors['missing value'] = "customerRefNo is required. Use function setClientOrderNumber() with the order number you used when creating the transaction.";
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
        $XMLWriter->startElement("query");  // note, not the same as $this->method above
        $XMLWriter->writeElement("customerrefno", $this->customerRefNo);
        $XMLWriter->endElement();
        $XMLWriter->endDocument();

        return $XMLWriter->flush();
    }

    protected function parseResponse($message)
    {
        $countryCode = $this->countryCode;
        $config = $this->config;

        return new QueryTransactionResponse($message, $countryCode, $config);
    }
}
