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
    public function prepareRequest() {
        $this->validateRequest();
        if($this->resendOrderVat === NULL){
             $this->determineVatFlag();
        }
//        $orderRowNumbers = array();
        foreach( $this->orderBuilder->orderRows as $orderRow ) {
             if (isset($orderRow->vatPercent) && isset($orderRow->amountExVat)) {
                 $this->amount = $this->priceIncludingVat ? \Svea\WebService\WebServiceRowFormatter::convertExVatToIncVat($orderRow->amountExVat, $orderRow->vatPercent) : $orderRow->amountExVat;
                 $this->priceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;

               // amountIncVat & vatPercent used to specify product price
             }elseif (isset($orderRow->vatPercent) && isset($orderRow->amountIncVat)) {
                 $this->amount = $this->priceIncludingVat ? $orderRow->amountIncVat : \Svea\WebService\WebServiceRowFormatter::convertIncVatToExVat($orderRow->amountIncVat, $orderRow->vatPercent);
                 $this->priceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;
             }else{
                 $this->amount = $this->priceIncludingVat ? $orderRow->amountIncVat : $orderRow->amountExVat;
                 $this->priceIncludingVat = $this->priceIncludingVat ? TRUE : FALSE;
                $orderRow->vatPercent = \Svea\WebService\WebServiceRowFormatter::calculateVatPercentFromPriceExVatAndPriceIncVat($orderRow->amountIncVat, $orderRow->amountExVat );
             }
            //discountPercent must be 0 or an int
            if(!isset($orderRow->discountPercent)) {
                $orderRow->discountPercent = 0;
            }

            $orderRows[] = new \SoapVar(
                new AdminSoap\OrderRow(
                    $orderRow->articleNumber,
                    $orderRow->name.": ".$orderRow->description,
                    $orderRow->discountPercent,
                    $orderRow->quantity,
                    $this->amount,
                    $orderRow->unit,
                    $orderRow->vatPercent,
                    $this->priceIncludingVat
                ),
                SOAP_ENC_OBJECT, null, null, 'OrderRow', "http://schemas.datacontract.org/2004/07/DataObjects.Webservice"
            );
        }

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

    private function determineVatFlag() {
        $exVat = 0;
        $incVat = 0;

        //check first if there is a mix of orderrows
        foreach ($this->orderBuilder->orderRows as $row) {
            if(isset($row->amountExVat) && isset($row->amountIncVat)){
                $incVat++;
            }elseif (isset($row->amountExVat) && isset ($row->vatPercent)) {
                $exVat++;
            }else {
                $incVat++;
            }
        }

          //if atleast one of the rows are set as exVat
          if ($exVat >= 1) {
              $this->priceIncludingVat = FALSE;
          }  else {
              $this->priceIncludingVat = TRUE;
          }
    }
}
