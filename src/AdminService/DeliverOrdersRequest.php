<?php
namespace Svea\AdminService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Admin Service DeliverOrdersRequest class
 * 
 * @author Kristian Grossman-Madsen
 */
class DeliverOrdersRequest extends AdminServiceRequest {
    
    /** @var deliverOrderBuilder $orderBuilder */
    public $orderBuilder;    

    /**
     * @param deliverOrderBuilder $deliverOrderBuilder
     */
    public function __construct($deliverOrderBuilder) {
        $this->action = "DeliverOrders";
        $this->orderBuilder = $deliverOrderBuilder;
    }    

    /**
     * populate and return soap request contents using AdminSoap helper classes to get the correct data format
     * @return Svea\AdminSoap\DeliverOrderRequest
     */    
    public function prepareRequest() {        
        
        $this->validateRequest();
        
        $soapRequest = new AdminSoap\DeliverOrdersRequest( 
            new AdminSoap\Authentication( 
                $this->orderBuilder->conf->getUsername( $this->orderBuilder->orderType, $this->orderBuilder->countryCode ), 
                $this->orderBuilder->conf->getPassword( $this->orderBuilder->orderType, $this->orderBuilder->countryCode ) 
            ),
            $this->orderBuilder->distributionType,
            new AdminSoap\OrdersToDeliver(
                new AdminSoap\DeliverOrderInformation(
                    $this->orderBuilder->conf->getClientNumber( $this->orderBuilder->orderType, $this->orderBuilder->countryCode ),
                    AdminServiceRequest::CamelCaseOrderType( $this->orderBuilder->orderType ),
                    $this->orderBuilder->orderId
                )
            )
        );
                
        return $soapRequest;
    }
        
    public function validate() {
        $errors = array();
        $errors = $this->validateDistributionType($errors);
        $errors = $this->validateOrderId($errors);
        $errors = $this->validateOrderType($errors);
        $errors = $this->validateCountryCode($errors);
        return $errors;
    }
    
    private function validateDistributionType($errors) {
        if (isset($this->orderBuilder->distributionType) == FALSE) {                                                        
            $errors[] = array('missing value' => "distributionType is required.");
        }
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
