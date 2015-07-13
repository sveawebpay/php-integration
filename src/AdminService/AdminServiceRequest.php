<?php
namespace Svea\AdminService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * AdminServiceRequest is the parent of all admin webservice requests.
 *
 * @author Kristian Grossman-Madsen
 */
abstract class AdminServiceRequest {

    /** @var string $action  the AdminService soap action called by this class */
    protected $action;

    /** @var string $countryCode */
    protected $countryCode;

    /** @var boolean $priceIncludingVat */
    protected $priceIncludingVat;

    protected $resendOrderVat = NULL;

    /**
     * Set up the soap client and perform the soap call, with the soap action and prepared request from the relevant subclass.
     * Returns the appropriate request response class, as determined by SveaResponse matching on request action.
     */
    public function doRequest( $resendOrderWithFlippedPriceIncludingVat = false ) {
        
        $requestObject = $this->prepareRequest( $resendOrderWithFlippedPriceIncludingVat );

        $soapClient = new AdminSoap\SoapClient( $this->orderBuilder->conf, \ConfigurationProvider::ADMIN_TYPE );
        $soapResponse = $soapClient->doSoapCall($this->action, $requestObject );
        $sveaResponse = new \SveaResponse( $soapResponse, null, null, $this->action );
        $response = $sveaResponse->getResponse();
        
        // iff error 50036, flip priceIncludingVat and resend enforcing flipped value
        if ($response->resultcode == "50036") {            
            if(property_exists($requestObject, 'OrderRows')) {
                 $priceIncludingVat =  $requestObject->OrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value;
            }  elseif (property_exists($requestObject, 'UpdatedOrderRows')) {
                 $priceIncludingVat =  $requestObject->UpdatedOrderRows->enc_value->enc_value[0]->enc_value->PriceIncludingVat->enc_value;
            } else {
                $priceIncludingVat = $this->priceIncludingVat;
            }
            $this->priceIncludingVat = !$priceIncludingVat;

            return $this->doRequest( true );
        }
        else {
            return $sveaResponse->getResponse();
        }
    }

    /**
     * Validates the orderBuilder object to make sure that all required settings
     * are present. If not, throws an exception. Actual validation is delegated
     * to subclass validate() implementations.
     *
     * @throws ValidationException
     */
    public function validateRequest() {
        // validate sub-class requirements by calling sub-class validate() method
        $errors = $this->validate();

        if (count($errors) > 0) {
            $exceptionString = "";
            foreach ($errors as $error) {
                foreach( $error as $key => $value) {
                    $exceptionString .="-". $key. " : ".$value."\n";
                }
            }

            throw new \Svea\ValidationException($exceptionString);
        }
    }

    abstract function prepareRequest(); // prepare the soap request data

    abstract function validate(); // validate is defined by subclasses, should validate all elements required for call is present

    /**
     * the integration package ConfigurationProvider::INVOICE_TYPE and ::PAYMENTPLAN_TYPE constants are all caps, whereas the admin service
     * enumeration used in the calls are CamelCase. This function converts the package constants so that they work with the admin service.
     */
    public static function CamelCaseOrderType( $orderTypeAsConst ) {
        switch( $orderTypeAsConst ) {
            case \ConfigurationProvider::INVOICE_TYPE:
                return "Invoice";
                break;
            case \ConfigurationProvider::PAYMENTPLAN_TYPE:
                return "PaymentPlan";
                break;
            default:
                return $orderTypeAsConst;
        }
    }
    
    /** @returns true iff all order rows are specified using amountIncVat, and the $flipPriceIncludingVat flag is omitted or false */
    protected function determineVatFlag( $orderRows, $flipPriceIncludingVat = false) {
        
        $exVat = 0;
        $incVat = 0;
        foreach ($orderRows as $row) {
            if(isset($row->amountExVat) && isset($row->amountIncVat)){
                $incVat++;
            }elseif (isset($row->amountExVat) && isset ($row->vatPercent)) {
                $exVat++;
            }else {
                $incVat++;
            }
        }
        //if at least one of the rows are set as exVat, set priceIncludingVat flag to false
        $priceIncludingVat = ($exVat >= 1) ? FALSE : TRUE;

        return $flipPriceIncludingVat ? !$priceIncludingVat : $priceIncludingVat;                
    }
    
    protected function getAdminSoapOrderRowsFromBuilderOrderRowsUsingVatFlag($builderOrderRows, $priceIncludingVat) {
        $amount = 0;
        $orderRows = array();
        foreach ($builderOrderRows as $orderRow) {
            if (isset($orderRow->vatPercent) && isset($orderRow->amountExVat)) {
                $amount = $priceIncludingVat ? \Svea\WebService\WebServiceRowFormatter::convertExVatToIncVat($orderRow->amountExVat, $orderRow->vatPercent) : $orderRow->amountExVat;
            } elseif (isset($orderRow->vatPercent) && isset($orderRow->amountIncVat)) {
                $amount = $priceIncludingVat ? $orderRow->amountIncVat : \Svea\WebService\WebServiceRowFormatter::convertIncVatToExVat($orderRow->amountIncVat, $orderRow->vatPercent);
            } else {
                $amount = $priceIncludingVat ? $orderRow->amountIncVat : $orderRow->amountExVat;
                $orderRow->vatPercent = \Svea\WebService\WebServiceRowFormatter::calculateVatPercentFromPriceExVatAndPriceIncVat($orderRow->amountIncVat, $orderRow->amountExVat);
            }

            $orderRows[] = new \SoapVar(
                new AdminSoap\OrderRow(
                    $orderRow->articleNumber, 
                    $this->formatRowNameAndDescription($orderRow),                        
                    !isset($orderRow->discountPercent) ? 0 : $orderRow->discountPercent, 
                    $orderRow->quantity, 
                    $amount, 
                    $orderRow->unit, 
                    $orderRow->vatPercent, 
                    $priceIncludingVat // attribute is set in correct (alphabetical) position via OrderRow constructor, see AdminSoap/OrderRow
                ), SOAP_ENC_OBJECT, null, null, 'OrderRow', "http://schemas.datacontract.org/2004/07/DataObjects.Webservice"
            );
        }
        return $orderRows;
    }    
    
    /**
     * wraps Svea\WebServiceRowFormatter->formatRowNameAndDescription to create a request description from order builder row name & description fields
     *
     * @param OrderRow|ShippingFee|et al. $webPayItemRow  an instance of the order row classes from WebPayItem
     * @return string  the combined description string that should be written to Description
     */
    private function formatRowNameAndDescription( $webPayItemRow ) {        
        $wsrf = new \Svea\WebService\WebServiceRowFormatter( null, null );
        return $wsrf->formatRowNameAndDescription( $webPayItemRow );
    }
    
    protected function getAdminSoapNumberedOrderRowsFromBuilderOrderRowsUsingVatFlag($builderOrderRows, $priceIncludingVat) {
        $amount = 0;
        $numberedOrderRows = array();
        foreach ($builderOrderRows as $orderRow) {
            if (isset($orderRow->vatPercent) && isset($orderRow->amountExVat)) {
                $amount = $priceIncludingVat ? \Svea\WebService\WebServiceRowFormatter::convertExVatToIncVat($orderRow->amountExVat, $orderRow->vatPercent) : $orderRow->amountExVat;
            } elseif (isset($orderRow->vatPercent) && isset($orderRow->amountIncVat)) {
                $amount = $priceIncludingVat ? $orderRow->amountIncVat : \Svea\WebService\WebServiceRowFormatter::convertIncVatToExVat($orderRow->amountIncVat, $orderRow->vatPercent);
            } else {
                $amount = $priceIncludingVat ? $orderRow->amountIncVat : $orderRow->amountExVat;
                $orderRow->vatPercent = \Svea\WebService\WebServiceRowFormatter::calculateVatPercentFromPriceExVatAndPriceIncVat($orderRow->amountIncVat, $orderRow->amountExVat);
            }
           
            $numberedOrderRows[] = new \SoapVar(
                new AdminSoap\NumberedOrderRow(
                    $orderRow->articleNumber,
                    $this->formatRowNameAndDescription($orderRow),                        
                    !isset($orderRow->discountPercent) ? 0 : $orderRow->discountPercent,
                    $orderRow->quantity,
                    $amount,
                    $orderRow->unit,
                    $orderRow->vatPercent,
                    $orderRow->creditInvoiceId,
                    $orderRow->invoiceId,
                    $orderRow->rowNumber,
                    $priceIncludingVat // attribute is set in correct (alphabetical) position via OrderRow constructor, see AdminSoap/OrderRow
                ),
                SOAP_ENC_OBJECT, null, null, 'NumberedOrderRow', "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service"
            );
        }
        return $numberedOrderRows;
    }
}
