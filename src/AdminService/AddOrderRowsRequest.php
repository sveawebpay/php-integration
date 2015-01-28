<?php
/**
 * Namespace Svea\AdminService Implements SveaWebPay Administration Service API 1.12.
 */
namespace Svea\AdminService;

require_once SVEA_REQUEST_DIR . '/Includes.php';
require_once 'AdminServiceRequest.php';

/**
 * Admin Service AddOrderRowsRequest class
 *
 * @author Kristian Grossman-Madsen
 */
class AddOrderRowsRequest extends AdminServiceRequest {

    /** @var AddOrderRowBuilder $orderBuilder */
    public $orderBuilder;
    private $amount;

    /**
     * @param addOrderRowsBuilder $orderBuilder
     */
    public function __construct($addOrderRowsBuilder) {
        $this->action = "AddOrderRows";
        $this->orderBuilder = $addOrderRowsBuilder;
    }
    
    /**
     * populate and return soap request contents using AdminSoap helper classes to get the correct data format
     * @return \Svea\AdminService\AdminSoap\AddOrderRowsRequest
     * @throws \Svea\ValidationException
     */
    public function prepareRequest( $resendOrderWithFlippedPriceIncludingVat = false) {
        $this->validateRequest();
        
        $this->priceIncludingVat = $this->determineVatFlag( $this->orderBuilder->orderRows, $resendOrderWithFlippedPriceIncludingVat);        
        $orderRows = $this->getAdminSoapOrderRowsFromBuilderOrderRowsUsingVatFlag( $this->orderBuilder->orderRows, $this->priceIncludingVat );        

        $soapRequest = new AdminSoap\AddOrderRowsRequest(
            new AdminSoap\Authentication(
                $this->orderBuilder->conf->getUsername( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ),
                $this->orderBuilder->conf->getPassword( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode )
            ),
            $this->orderBuilder->conf->getClientNumber( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ),
            new \SoapVar($orderRows, SOAP_ENC_OBJECT),
            AdminServiceRequest::CamelCaseOrderType( $this->orderBuilder->orderType ),
            $this->orderBuilder->orderId
        );
        return $soapRequest;
    }

    public function validate() {
        $errors = array();
        $errors = $this->validateOrderId($errors);
        $errors = $this->validateOrderType($errors);
        $errors = $this->validateCountryCode($errors);
        $errors = $this->validateRowsToAdd($errors);
        $errors = $this->validateRowsHasPriceAndVatInformation($errors);
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

    private function validateRowsToAdd($errors) {
        if (isset($this->orderBuilder->orderRows) == FALSE) {
            $errors[] = array('missing value' => "orderRows is required.");
        }
        return $errors;
    }

    private function validateRowsHasPriceAndVatInformation($errors) {
        if( isset($this->orderBuilder->orderRows) ) {
            foreach( $this->orderBuilder->orderRows as $orderRow ) {
                if( !isset($orderRow->vatPercent) && (!isset($orderRow->amountIncVat) && !isset($orderRow->amountExVat)) ) {
                    $errors[] = array('missing order row vat information' => "cannot calculate orderRow vatPercent, need at least two of amountExVat, amountIncVat and vatPercent.");
                }
            }
        }
        return $errors;
    } 
}
