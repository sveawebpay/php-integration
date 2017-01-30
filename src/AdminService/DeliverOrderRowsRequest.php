<?php

namespace Svea\WebPay\AdminService;

use SoapVar;
use Svea\WebPay\BuildOrder\DeliverOrderRowsBuilder;
use Svea\WebPay\AdminService\AdminSoap\Authentication;
use Svea\WebPay\AdminService\AdminSoap\OrderToDeliver;
use Svea\WebPay\BuildOrder\Validator\ValidationException;
use Svea\WebPay\AdminService\AdminSoap\DeliverPartialRequest;
use Svea\WebPay\Helper\Helper;

/**
 * Admin Service DeliverOrderRowsRequest class
 *
 * @author Kristian Grossman-Madsen
 */
class DeliverOrderRowsRequest extends AdminServiceRequest
{
    /**
     * @var DeliverOrderRowsBuilder $orderBuilder
     */
    public $orderBuilder;

    /**
     * @param DeliverOrderRowsBuilder $deliverOrderRowsBuilder
     */
    public function __construct($deliverOrderRowsBuilder)
    {
        $this->action = "DeliverPartial";
        $this->orderBuilder = $deliverOrderRowsBuilder;
    }

    /**
     * populate and return soap request contents using AdminSoap helper classes to get the correct data format
     * @return DeliverOrderRowsRequest
     * @throws ValidationException
     */
    public function prepareRequest()
    {
        $this->validateRequest();

        foreach ($this->orderBuilder->rowsToDeliver as $rowToDeliver) {
            $this->rowNumbers[] = new SoapVar($rowToDeliver, XSD_LONG, null, null, 'long', "http://schemas.microsoft.com/2003/10/Serialization/Arrays");
        }

        $soapRequest = new DeliverPartialRequest(
            new Authentication(
                $this->orderBuilder->conf->getUsername(($this->orderBuilder->orderType), $this->orderBuilder->countryCode),
                $this->orderBuilder->conf->getPassword(($this->orderBuilder->orderType), $this->orderBuilder->countryCode)
            ),
            $this->orderBuilder->distributionType,

            new OrderToDeliver(
                $this->orderBuilder->conf->getClientNumber(($this->orderBuilder->orderType), $this->orderBuilder->countryCode),
                AdminServiceRequest::CamelCaseOrderType($this->orderBuilder->orderType),
                $this->orderBuilder->orderId
            ),
            $this->rowNumbers
        );

        return $soapRequest;
    }

    public function validate()
    {
        $errors = array();
        $errors = $this->validateOrderId($errors);
        $errors = $this->validateOrderType($errors);
        $errors = $this->validateCountryCode($errors);
        $errors = $this->validateRowsToDeliver($errors);

        return $errors;
    }

    private function validateOrderId($errors)
    {
        if (isset($this->orderBuilder->orderId) == FALSE) {
            $errors[] = array('missing value' => "orderId is required.");
        }

        return $errors;
    }

    private function validateOrderType($errors)
    {
        if (isset($this->orderBuilder->orderType) == FALSE) {
            $errors[] = array('missing value' => "orderType is required.");
        }

        return $errors;
    }

    private function validateCountryCode($errors)
    {
        if (isset($this->orderBuilder->countryCode) == FALSE) {
            $errors[] = array('missing value' => "countryCode is required.");
        }

        return $errors;
    }

    private function validateRowsToDeliver($errors)
    {
        if (isset($this->orderBuilder->rowsToDeliver) == FALSE) {
            $errors[] = array('missing value' => "rowsToDeliver is required.");
        }

        return $errors;
    }
}        
