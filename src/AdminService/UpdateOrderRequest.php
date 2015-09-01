<?php
namespace Svea\AdminService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Admin Service UpdateOrderRequest class
 */
class UpdateOrderRequest extends AdminServiceRequest {

    /** @var UpdateOrderRowBuilder $orderBuilder */
    public $orderBuilder;

    /**
     * @param updateOrderBuilder $orderBuilder
     */
    public function __construct($updateOrderBuilder) {
        $this->action = "UpdateOrder";
        $this->orderBuilder = $updateOrderBuilder;
    }

    /**
     * populate and return soap request contents using AdminSoap helper classes to get the correct data format
     * @return Svea\AdminSoap\UpdateOrderRequest
     * @throws Svea\ValidationException
     */
    public function prepareRequest() {
        $this->validateRequest();
        $soapRequest = new AdminSoap\UpdateOrderRequest(
            new AdminSoap\Authentication(
                $this->orderBuilder->conf->getUsername( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ),
                $this->orderBuilder->conf->getPassword( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode )
            ),
            $this->orderBuilder->conf->getClientNumber( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ),
            AdminServiceRequest::CamelCaseOrderType( $this->orderBuilder->orderType ),
            $this->orderBuilder->orderId,
            $this->orderBuilder->clientOrderNumber,
            $this->orderBuilder->notes

        );

        return $soapRequest;
    }

    public function validate() {
        $errors = array();
        $errors = $this->validateOrderId($errors);
        $errors = $this->validateOrderType($errors);
        $errors = $this->validateCountryCode($errors);
        $errors = $this->validateStringLength($errors);
        return $errors;
    }

    private function validateOrderId($errors) {
        if (isset($this->orderBuilder->orderId) == FALSE) {
            $errors[] = array('missing value' => "orderId is required.");
        }
        return $errors;
    }


    private function validateOrderType($errors) {
        if (isset($this->orderBuilder->orderType) == FALSE) {
            $errors[] = array('missing value' => "orderType is required.");
        }
        return $errors;
    }

    private function validateCountryCode($errors) {
        if (isset($this->orderBuilder->countryCode) == FALSE) {
            $errors[] = array('missing value' => "countryCode is required.");
        }
        return $errors;
    }

    private function validateStringLength($errors) {
          if (strlen($this->orderBuilder->notes) > 200) {
            $errors[] = array('String length' => "The field Notes must be a string with a maximum length of 200.");
        }
        return $errors;
    }

}
