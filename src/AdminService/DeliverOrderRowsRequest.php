<?php
namespace Svea\AdminService;

require_once SVEA_REQUEST_DIR . '/Includes.php';
require_once 'AdminServiceRequest.php';

/**
 * Admin Service DeliverOrderRowsRequest class
 * 
 * @author Kristian Grossman-Madsen
 */
class DeliverOrderRowsRequest extends AdminServiceRequest {
    
    /** @var DeliverOrderRowBuilder $orderBuilder */
    public $orderBuilder;
    
//    /** @var SoapVar[] $rowNumbers  initally empty, contains the indexes of all order rows that will be delivered */
//    public $rowNumbers; 
//    
//    /** @var SoapVar[] $orderRows  initially empty, specifies any additional deliver order rows to deliver */    
//    public $orderRows;

    /**
     * @param deliverOrderRowsBuilder $orderBuilder
     */
    public function __construct($deliverOrderRowsBuilder) {
        $this->action = "DeliverPartial";
        $this->orderBuilder = $deliverOrderRowsBuilder;
//        $this->rowNumbers = array();
//        $this->orderRows = array();
    }

    /**
     * populate and return soap request contents using AdminSoap helper classes to get the correct data format
     * @return Svea\AdminSoap\DeliverOrderRowsRequest
     * @throws Svea\ValidationException
     */
    public function prepareRequest() {
                   
        $this->validateRequest();
        
//        foreach( $this->orderBuilder->deliverOrderRows as $orderRow ) {
//
//            // handle different ways to spec an orderrow            
//            // inc + ex
//            if( !isset($orderRow->vatPercent) && (isset($orderRow->amountExVat) && isset($orderRow->amountIncVat)) ) {
//                $orderRow->vatPercent = \Svea\WebService\WebServiceRowFormatter::calculateVatPercentFromPriceExVatAndPriceIncVat($orderRow->amountIncVat, $orderRow->amountExVat );
//            }
//            // % + inc
//            elseif( (isset($orderRow->vatPercent) && isset($orderRow->amountIncVat)) && !isset($orderRow->amountExVat) ) {
//                $orderRow->amountExVat = \Svea\WebService\WebServiceRowFormatter::convertIncVatToExVat($orderRow->amountIncVat, $orderRow->vatPercent);
//            }
//            // % + ex, no need to do anything
//
//            $this->orderRows[] = new \SoapVar( 
//                new AdminSoap\OrderRow(
//                    $orderRow->articleNumber, 
//                    $orderRow->name.": ".$orderRow->description,
//                    $orderRow->discountPercent,
//                    $orderRow->quantity, 
//                    $orderRow->amountExVat, 
//                    $orderRow->unit, 
//                    $orderRow->vatPercent
//                ),
//                SOAP_ENC_OBJECT, null, null, 'OrderRow', "http://schemas.datacontract.org/2004/07/DataObjects.Webservice" 
//            );
//        }
        
        foreach( $this->orderBuilder->rowsToDeliver as $rowToDeliver ) {       
            $this->rowNumbers[] = new \SoapVar($rowToDeliver, XSD_LONG, null,null, 'long', "http://schemas.microsoft.com/2003/10/Serialization/Arrays");
        }    
        
        $soapRequest = new AdminSoap\DeliverPartialRequest( 
            new AdminSoap\Authentication( 
                $this->orderBuilder->conf->getUsername( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ), 
                $this->orderBuilder->conf->getPassword( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ) 
            ),
            $this->orderBuilder->distributionType,

            new AdminSoap\OrderToDeliver(
                $this->orderBuilder->conf->getClientNumber( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ),
                AdminServiceRequest::CamelCaseOrderType( $this->orderBuilder->orderType ),
                $this->orderBuilder->orderId
            ),
            $this->rowNumbers
        );
        return $soapRequest;
    }
        
    public function validate() {
        $errors = array();
        $errors = $this->validateOrderId($errors);
        $errors = $this->validateOrderType($errors);
        $errors = $this->validateCountryCode($errors);
        $errors = $this->validateRowsToDeliver($errors);     
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
    
    private function validateRowsToDeliver($errors) {
        if (isset($this->orderBuilder->rowsToDeliver) == FALSE) {                                                        
            $errors[] = array('missing value' => "rowsToDeliver is required.");
        }
        return $errors;
    }
}        
