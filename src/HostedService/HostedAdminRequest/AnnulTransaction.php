<?php

namespace Svea\WebPay\HostedService\HostedAdminRequest;

use Svea\WebPay\Helper\Helper;
use Svea\WebPay\HostedService\HostedRequest;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\AnnulTransactionResponse;

/**
 * AnnulTransaction is used to cancel (annul) a card transaction.
 * The transaction must have status AUTHORIZED or CONFIRMED at Svea.
 * After a successful request the transaction will get the status ANNULLED.
 *
 * @author Kristian Grossman-Madsen
 */
class AnnulTransaction extends HostedRequest
{
    /**
     * @var string $transactionid
     */
    public $transactionId;

    /**
     * Usage: create an instance, set all required attributes, then call doRequest().
     * Required: $transactionId
     * @param ConfigurationProvider $config instance implementing Svea\WebPay\Config\ConfigurationProvider
     */
    function __construct($config)
    {
        $this->method = "annul";
        parent::__construct($config);
    }

    protected function validateRequestAttributes()
    {
        $errors = array();
        $errors = $this->validateTransactionId($this, $errors);

        return $errors;
    }

    private function validateTransactionId($self, $errors)
    {
        if (isset($self->transactionId) == FALSE) {
            $errors['missing value'] = "transactionId is required. Use function setTransactionId() with the SveaOrderId from the createOrder response.";
        }

        return $errors;
    }

    /** returns xml for hosted webservice "annul" request */
    protected function createRequestXml()
    {
        $XMLWriter = new \XMLWriter();

        $XMLWriter->openMemory();
        $XMLWriter->setIndent(true);
        $XMLWriter->startDocument("1.0", "UTF-8");
        $XMLWriter->writeComment(Helper::getLibraryAndPlatformPropertiesAsJson($this->config));
        $XMLWriter->startElement($this->method);
        $XMLWriter->writeElement("transactionid", $this->transactionId);
        $XMLWriter->endElement();
        $XMLWriter->endDocument();

        return $XMLWriter->flush();
    }

    protected function parseResponse($message)
    {
        $countryCode = $this->countryCode;
        $config = $this->config;

        return new AnnulTransactionResponse($message, $countryCode, $config);
    }
}
