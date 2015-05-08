<?php
namespace Svea\AdminService;

require_once SVEA_REQUEST_DIR . '/Includes.php';
require_once 'AdminServiceRequest.php';

/**
 * Admin Service CreditOrderRowsRequest class
 * 
 * @author Kristian Grossman-Madsen
 */
class CreditOrderRowsRequest extends AdminServiceRequest {
    
    /** @var CreditOrderRowBuilder $orderBuilder */
    public $orderBuilder;
    
    /** @var SoapVar[] $rowNumbers  initally empty, contains the indexes of all order rows that will be credited */
    public $rowNumbers; 
    
    /** @var SoapVar[] $orderRows  initially empty, specifies any additional credit order rows to credit */    
    public $orderRows;

    /**
     * @param creditOrderRowsBuilder $orderBuilder
     */
    public function __construct($creditOrderRowsBuilder) {
        $this->action = "CreditInvoiceRows";
        $this->orderBuilder = $creditOrderRowsBuilder;
        $this->rowNumbers = array();
        $this->orderRows = array();
    }

    /**
     * populate and return soap request contents using AdminSoap helper classes to get the correct data format
     * @return Svea\AdminSoap\CreditOrderRowsRequest
     * @throws Svea\ValidationException
     */
    public function prepareRequest( $resendOrderWithFlippedPriceIncludingVat = false) {
        $this->validateRequest();        
        $this->priceIncludingVat = $this->determineVatFlag( $this->orderBuilder->creditOrderRows, $resendOrderWithFlippedPriceIncludingVat);
        $this->orderRows = 
            $this->getAdminSoapOrderRowsFromBuilderOrderRowsUsingVatFlag( $this->orderBuilder->creditOrderRows, $this->priceIncludingVat );
        
        foreach( $this->orderBuilder->rowsToCredit as $rowToCredit ) {       
            $this->rowNumbers[] = new \SoapVar($rowToCredit, XSD_LONG, null,null, 'long', "http://schemas.microsoft.com/2003/10/Serialization/Arrays");
        }    
        
        $soapRequest = new AdminSoap\CreditInvoiceRowsRequest( 
            new AdminSoap\Authentication( 
                $this->orderBuilder->conf->getUsername( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ), 
                $this->orderBuilder->conf->getPassword( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ) 
            ),
            $this->orderBuilder->conf->getClientNumber( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ),
            $this->orderBuilder->distributionType,
            $this->orderBuilder->invoiceId,                
            $this->orderRows,
            $this->rowNumbers
        );
        return $soapRequest;
    }
        
    public function validate() {
        $errors = array();
        $errors = $this->validateInvoiceId($errors);
        $errors = $this->validateInvoiceDistributionType($errors);
        $errors = $this->validateOrderType($errors);
        $errors = $this->validateCountryCode($errors);
        $errors = $this->validateHasRows($errors);     
        $errors = $this->validateCreditOrderRowsHasPriceAndVatInformation($errors);
        return $errors;
    }
    
    private function validateInvoiceId($errors) {
        if (isset($this->orderBuilder->invoiceId) == FALSE) {                                                        
            $errors[] = array('missing value' => "invoiceId is required, use setInvoiceId().");
        }
        return $errors;
    }               

    private function validateInvoiceDistributionType($errors) {
        if (isset($this->orderBuilder->distributionType) == FALSE) {                                                        
            $errors[] = array('missing value' => "distributionType is required, use setInvoiceDistributionType().");
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
            $errors[] = array('missing value' => "countryCode is required, use setCountryCode().");
        }
        return $errors;
    }

    private function validateHasRows($errors) {
        if( (count($this->orderBuilder->rowsToCredit) == 0) &&
            (count($this->orderBuilder->creditOrderRows) == 0) )
        {                                                        
            $errors[] = array('missing value' => "no rows to credit, use setRow(s)ToCredit() or addCreditOrderRow(s)().");
        }
        return $errors;
    }

    private function validateCreditOrderRowsHasPriceAndVatInformation($errors) {
        foreach( $this->orderBuilder->creditOrderRows as $orderRow ) {                                                        
            if( !isset($orderRow->vatPercent) && (!isset($orderRow->amountIncVat) && !isset($orderRow->amountExVat)) ) {            
                $errors[] = array('missing order row vat information' => "cannot calculate orderRow vatPercent, need at least two of amountExVat, amountIncVat and vatPercent.");
            }
        }
        return $errors;
    }
}        
