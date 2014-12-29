<?php
namespace Svea\AdminService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Handles Admin Webservice GetOrdersRequest
 * @author Kristian Grossman-Madsen
 */
class GetOrdersRequest extends AdminServiceRequest {
    
    /** @var QueryOrderBuilder $orderBuilder */
    public $orderBuilder;

    /**
     * @param QueryOrderBuilder $builder
     */
    public function __construct($builder) {
        $this->action = "GetOrders";
        $this->orderBuilder = $builder;
    }

    /**
     * populate and return soap request contents using AdminSoap helper classes to get the correct data format
     * @return Svea\AdminSoap\GetOrdersRequest
     */    
    public function prepareRequest() {        
                   
        $this->validateRequest();
        
        $soapRequest = array();
        $soapRequest = new AdminSoap\GetOrdersRequest( 
            new AdminSoap\Authentication( 
                $this->orderBuilder->conf->getUsername( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ), 
                $this->orderBuilder->conf->getPassword( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ) 
            ),
            new AdminSoap\OrdersToRetrieve(
                new AdminSoap\GetOrderInformation(
                    $this->orderBuilder->conf->getClientNumber( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ),
                    $this->orderBuilder->orderId
                )
            )
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
