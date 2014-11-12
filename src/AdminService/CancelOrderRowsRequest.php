<?php
namespace Svea\AdminService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Admin Service CancelOrderRowsRequest class
 * 
 * @author Kristian Grossman-Madsen
 */
class CancelOrderRowsRequest extends AdminServiceRequest {
    
    /** @var CancelOrderRowBuilder $orderBuilder */
    public $orderBuilder;

    /**
     * @param cancelOrderRowsBuilder $orderBuilder
     */
    public function __construct($cancelOrderRowsBuilder) {
        $this->action = "CancelOrderRows";
        $this->orderBuilder = $cancelOrderRowsBuilder;
    }

    /**
     * populate and return soap request contents using AdminSoap helper classes to get the correct data format
     * @return Svea\AdminSoap\CancelOrderRowsRequest
     */    
    public function prepareRequest() {        
                   
        $this->validateRequest();

        $orderRowNumbers = array();        
        foreach( $this->orderBuilder->rowsToCancel as $rowToCancel ) {       
            $orderRowNumbers[] = new \SoapVar($rowToCancel, XSD_LONG, null, null, 'long', "http://schemas.microsoft.com/2003/10/Serialization/Arrays");
        }        
        
        $soapRequest = new AdminSoap\CancelOrderRowsRequest( 
            new AdminSoap\Authentication( 
                $this->orderBuilder->conf->getUsername( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ), 
                $this->orderBuilder->conf->getPassword( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ) 
            ),
            $this->orderBuilder->conf->getClientNumber( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ),
            new \SoapVar($orderRowNumbers, SOAP_ENC_OBJECT),            
            AdminServiceRequest::CamelCaseOrderType( $this->orderBuilder->orderType ),
            $this->orderBuilder->orderId
        );

        // Example request to cancel rows 1 & 2 in order 349092
        // 
        //<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:dat="http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service" xmlns:arr="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
        //   <soapenv:Header/>
        //   <soapenv:Body>
        //      <tem:CancelOrderRows>
        //         <tem:request>
        //            <dat:Authentication>
        //               <dat:Password>sverigetest</dat:Password>
        //               <dat:Username>sverigetest</dat:Username>
        //            </dat:Authentication>
        //            <dat:ClientId>79021</dat:ClientId>
        //            <dat:OrderRowNumbers>
        //	 	 <arr:long>1</arr:long>
        //               <arr:long>2</arr:long>
        //            </dat:OrderRowNumbers>
        //            <dat:OrderType>Invoice</dat:OrderType>
        //            <dat:SveaOrderId>349092</dat:SveaOrderId>
        //         </tem:request>
        //      </tem:CancelOrderRows>
        //   </soapenv:Body>
        //</soapenv:Envelope>        
                
        return $soapRequest;
    }
        
    public function validate() {
        $errors = array();
        $errors = $this->validateOrderId($errors);
        $errors = $this->validateOrderType($errors);
        $errors = $this->validateCountryCode($errors);
        $errors = $this->validateRowsToCancel($errors);                        
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
    
    private function validateRowsToCancel($errors) {
        if (isset($this->orderBuilder->rowsToCancel) == FALSE) {                                                        
            $errors[] = array('missing value' => "rowsToCancel is required.");
        }
        return $errors;
    }  
}        
