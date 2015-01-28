<?php
namespace Svea\AdminService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Admin Service UpdateOrderRowsRequest class
 *
 * @author Kristian Grossman-Madsen
 */
class UpdateOrderRowsRequest extends AdminServiceRequest {

    /** @var UpdateOrderRowBuilder $orderBuilder */
    public $orderBuilder;
    private $amount;

    /**
     * @param updateOrderRowsBuilder $orderBuilder
     */
    public function __construct($updateOrderRowsBuilder) {
        $this->action = "UpdateOrderRows";
        $this->orderBuilder = $updateOrderRowsBuilder;
    }

    /**
     * populate and return soap request contents using AdminSoap helper classes to get the correct data format
     * @return Svea\AdminSoap\UpdateOrderRowsRequest
     * @throws Svea\ValidationException
     */    
    public function prepareRequest( $resendOrderWithFlippedPriceIncludingVat = false) {
        $this->validateRequest();        
        $this->priceIncludingVat = $this->determineVatFlag( $this->orderBuilder->numberedOrderRows, $resendOrderWithFlippedPriceIncludingVat);
        $updatedOrderRows = 
            $this->getAdminSoapNumberedOrderRowsFromBuilderOrderRowsUsingVatFlag( $this->orderBuilder->numberedOrderRows, $this->priceIncludingVat );

        $soapRequest = new AdminSoap\UpdateOrderRowsRequest(
            new AdminSoap\Authentication(
                $this->orderBuilder->conf->getUsername( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ),
                $this->orderBuilder->conf->getPassword( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode )
            ),
            $this->orderBuilder->conf->getClientNumber( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ),
            AdminServiceRequest::CamelCaseOrderType( $this->orderBuilder->orderType ),
            $this->orderBuilder->orderId,
            new \SoapVar($updatedOrderRows, SOAP_ENC_OBJECT)
        );

        return $soapRequest;
    }

    public function validate() {
        $errors = array();
        $errors = $this->validateOrderId($errors);
        $errors = $this->validateRowNumber($errors);
        $errors = $this->validateOrderType($errors);
        $errors = $this->validateCountryCode($errors);
        $errors = $this->validateNumberedOrderRowsExist($errors);
        $errors = $this->validateNumberedOrderRowsHasPriceAndVatInformation($errors);
        return $errors;
    }

    private function validateOrderId($errors) {
        if (isset($this->orderBuilder->orderId) == FALSE) {
            $errors[] = array('missing value' => "orderId is required.");
        }
        return $errors;
    }
    private function validateRowNumber($errors) {
        foreach( $this->orderBuilder->numberedOrderRows as $orderRow ) {
            if (isset($orderRow->rowNumber) == FALSE) {
                $errors[] = array('missing value' => "rowNumber is required.");
            }
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

    private function validateNumberedOrderRowsExist($errors) {
        if (isset($this->orderBuilder->numberedOrderRows) == FALSE) {
            $errors[] = array('missing value' => "numberedOrderRows is required.");
        }
        return $errors;
    }

    private function validateNumberedOrderRowsHasPriceAndVatInformation($errors) {
        foreach( $this->orderBuilder->numberedOrderRows as $orderRow ) {
            if( !isset($orderRow->vatPercent) && (!isset($orderRow->amountIncVat) && !isset($orderRow->amountExVat)) ) {
                $errors[] = array('missing order row vat information' => "cannot calculate orderRow vatPercent, need at least two of amountExVat, amountIncVat and vatPercent.");
            }
        }
        return $errors;
    }
}
