<?php

namespace Svea\WebPay\AdminService;

use Svea\WebPay\AdminService\AdminSoap\Authentication;
use Svea\WebPay\AdminService\AdminSoap\SearchOrdersRequest as SearchOrdersSoapRequest;

class SearchOrdersRequest extends AdminServiceRequest
{
    /**
     * @var QueryOrderBuilder $orderBuilder
     */
    public $orderBuilder;

    /**
     * @param QueryOrderBuilder $builder
     */
    public function __construct($builder)
    {
        $this->action = "GetAccountCredits";
        $this->orderBuilder = $builder;
    }


    function prepareRequest()
    {
        $this->validateRequest();

        $soapRequest = array();
        $soapRequest = new SearchOrdersSoapRequest(
            new Authentication(
                $this->orderBuilder->conf->getUsername(($this->orderBuilder->orderType), $this->orderBuilder->countryCode),
                $this->orderBuilder->conf->getPassword(($this->orderBuilder->orderType), $this->orderBuilder->countryCode)
            ),
            $this->orderBuilder->getClientAccountCreditInformation()
        );

        return $soapRequest;
    }

    public function validate()
    {
        $errors = array();
       // $errors = $this->validateOrderId($errors);
       // $errors = $this->validateOrderType($errors);
       // $errors = $this->validateCountryCode($errors);

        return $errors;
    }

    private function validateOrderId($errors)
    {
        if (isset($this->orderBuilder->orderId) == FALSE) {
            $errors[] = array('missing value' => "orderId is required.");
        }

        return $errors;
    }
}