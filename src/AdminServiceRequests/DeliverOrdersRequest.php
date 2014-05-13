<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Admin Service DeliverOrdersRequest class
 * 
 * @author Kristian Grossman-Madsen
 */
class DeliverOrdersRequest extends AdminServiceRequest {
    
    /** @var cancelOrderBuilder $orderBuilder */
    public $orderBuilder;    

    /**
     * @param cancelOrderBuilder $orderBuilder
     */
    public function __construct($deliverOrderBuilder) {
        $this->action = "DeliverOrders";
        $this->orderBuilder = $deliverOrderBuilder;
    }    

    /**
     * populate and return soap request contents
     * @return Svea\AdminSoap\CancelOrderRequest
     */    
    public function prepareRequest() {        
        
        $this->validateRequest();
        
//        $soapRequest = new AdminSoap\DeliverOrderRequest( 
//                new AdminSoap\Authentication( 
//                    $this->orderBuilder->conf->username, 
//                    $this->orderBuilder->conf->password 
//                ),
//                $this->orderBuilder->sveaOrderId, 
//                $this->orderBuilder->orderType,
//                $this->orderBuilder->conf->clientId 
//        );
        
        return $soapRequest;
    }
        
    public function validate() {
        $errors = array();
//        $errors = $this->validateOrderId($errors);
//        $errors = $this->validateOrderType($errors);
        return $errors;
    }
    
//    private function validateOrderId($errors) {
//        if (isset($this->orderBuilder->sveaOrderId) == FALSE) {                                                        
//            $errors['missing value'] = "sveaOrderId is required.";
//        }
//        return $errors;
//    }               
//    private function validateOrderType($errors) {
//        if (isset($this->orderBuilder->orderType) == FALSE) {                                                        
//            $errors['missing value'] = "orderType is required.";
//        }
//        return $errors;
//    }                     
}        
