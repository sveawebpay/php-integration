<?php
namespace Svea\AdminService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Admin Service CancelOrderRequest class
 * 
 * @author Kristian Grossman-Madsen
 */
class CancelOrderRequest extends AdminServiceRequest {
    
    /** @var cancelOrderBuilder $orderBuilder */
    public $orderBuilder;    

    /**
     * @param cancelOrderBuilder $orderBuilder
     */
    public function __construct($cancelOrderBuilder) {
        $this->action = "CancelOrder";
        $this->orderBuilder = $cancelOrderBuilder;
    }    

    /**
     * populate and return soap request contents
     * @return Svea\AdminSoap\CancelOrderRequest
     */    
    public function prepareRequest() {        
        
        $this->validateRequest();
        
        $soapRequest = new AdminSoap\CancelOrderRequest( 
                new AdminSoap\Authentication( 
                    $this->orderBuilder->conf->getUsername( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ), 
                    $this->orderBuilder->conf->getPassword( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ) 
                ),
                $this->orderBuilder->orderId, 
                AdminServiceRequest::CamelCaseOrderType( $this->orderBuilder->orderType ),
                $this->orderBuilder->conf->getClientNumber( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode )
        );
        
        return $soapRequest;
    }
        
    public function validate() {
        $errors = array();
        $errors = $this->validateOrderId($errors);
        $errors = $this->validateOrderType($errors);
        $errors = $this->validateCountryCode($errors);
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
}        
