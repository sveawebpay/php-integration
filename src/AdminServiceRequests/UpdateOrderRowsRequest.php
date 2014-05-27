<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Admin Service UpdateOrderRowsRequest class
 * 
 * @author Kristian Grossman-Madsen
 */
class UpdateOrderRowsRequest extends AdminServiceRequest {
    
    /** @var UpdateOrderRowBuilder $orderBuilder */
    public $orderBuilder;

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
     */    
    public function prepareRequest() {        
                   
        $this->validateRequest();

        $orderRowNumbers = array();        
        foreach( $this->orderBuilder->rowsToUpdate as $rowToUpdate ) {       
            $orderRowNumbers[] = new \SoapVar($rowToUpdate, XSD_LONG, null, null, 'long', "http://schemas.microsoft.com/2003/10/Serialization/Arrays");
        }
        
        $soapRequest = new AdminSoap\UpdateOrderRowsRequest( 
            new AdminSoap\Authentication( 
                $this->orderBuilder->conf->getUsername( strtoupper($this->orderBuilder->orderType), $this->orderBuilder->countryCode ), 
                $this->orderBuilder->conf->getPassword( strtoupper($this->orderBuilder->orderType), $this->orderBuilder->countryCode ) 
            ),
            $this->orderBuilder->conf->getClientNumber( strtoupper($this->orderBuilder->orderType), $this->orderBuilder->countryCode ),
            new \SoapVar($orderRowNumbers, SOAP_ENC_OBJECT),            
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
        $errors = $this->validateRowsToUpdate($errors);                        
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
    
    private function validateRowsToUpdate($errors) {
        if (isset($this->orderBuilder->rowsToUpdate) == FALSE) {                                                        
            $errors[] = array('missing value' => "rowsToUpdate is required.");
        }
        return $errors;
    }  
}        
