<?php
namespace Svea\AdminService;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Admin Service UpdateOrderRowsRequest class
 *
 * @author Kristian Grossman-Madsen
 */
class UpdateOrderRowsRequest extends AdminServiceRequest {

    /** @var UpdateOrderRowBuilder $orderBuilder */
    public $orderBuilder;
    private $amount;

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
     * @throws Svea\ValidationException
     */
    public function prepareRequest() {
        $this->validateRequest();
        if($this->resendOrderVat === NULL){
             $this->determineVatFlag();
        }
        $updatedOrderRows = array();
        foreach( $this->orderBuilder->numberedOrderRows as $orderRow ) {
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
            if(!isset( $orderRow->discountPercent)) {
                 $orderRow->discountPercent = 0;
            }

            $updatedOrderRows[] = new \SoapVar(
                new AdminSoap\NumberedOrderRow(
                    $orderRow->articleNumber,
                    $orderRow->name.": ".$orderRow->description,
                    $orderRow->discountPercent,
                    $orderRow->quantity,
                     $this->amount,
                    $orderRow->unit,
                    $orderRow->vatPercent,
                    $orderRow->creditInvoiceId,
                    $orderRow->invoiceId,
                    $orderRow->rowNumber,
                    $this->priceIncludingVat
                ),
                SOAP_ENC_OBJECT, null, null, 'NumberedOrderRow', "http://schemas.datacontract.org/2004/07/DataObjects.Admin.Service"
            );
        }

        $soapRequest = new AdminSoap\UpdateOrderRowsRequest(
            new AdminSoap\Authentication(
                $this->orderBuilder->conf->getUsername( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ),
                $this->orderBuilder->conf->getPassword( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode )
            ),
            $this->orderBuilder->conf->getClientNumber( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ),
            AdminServiceRequest::CamelCaseOrderType( $this->orderBuilder->orderType ),
            $this->orderBuilder->orderId,
            new \SoapVar($updatedOrderRows, SOAP_ENC_OBJECT)
        );

        return $soapRequest;
    }

    public function validate() {
        $errors = array();
        $errors = $this->validateOrderId($errors);
        $errors = $this->validateRowNumber($errors);
        $errors = $this->validateOrderType($errors);
        $errors = $this->validateCountryCode($errors);
        $errors = $this->validateNumberedOrderRowsExist($errors);
        $errors = $this->validateNumberedOrderRowsHasPriceAndVatInformation($errors);
        return $errors;
    }

    private function validateOrderId($errors) {
        if (isset($this->orderBuilder->orderId) == FALSE) {
            $errors[] = array('missing value' => "orderId is required.");
        }
        return $errors;
    }
    private function validateRowNumber($errors) {
        foreach( $this->orderBuilder->numberedOrderRows as $orderRow ) {
            if (isset($orderRow->rowNumber) == FALSE) {
                $errors[] = array('missing value' => "rowNumber is required.");
            }
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

    private function validateNumberedOrderRowsExist($errors) {
        if (isset($this->orderBuilder->numberedOrderRows) == FALSE) {
            $errors[] = array('missing value' => "numberedOrderRows is required.");
        }
        return $errors;
    }

    private function validateNumberedOrderRowsHasPriceAndVatInformation($errors) {
        foreach( $this->orderBuilder->numberedOrderRows as $orderRow ) {
            if( !isset($orderRow->vatPercent) && (!isset($orderRow->amountIncVat) && !isset($orderRow->amountExVat)) ) {
                $errors[] = array('missing order row vat information' => "cannot calculate orderRow vatPercent, need at least two of amountExVat, amountIncVat and vatPercent.");
            }
        }
        return $errors;
    }

        private function determineVatFlag() {
        $exVat = 0;
        $incVat = 0;

        //check first if there is a mix of orderrows
        foreach( $this->orderBuilder->numberedOrderRows as $row ) {
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
