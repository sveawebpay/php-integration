<?php
namespace Svea\AdminService;

require_once SVEA_REQUEST_DIR . '/Includes.php';
require_once 'AdminServiceRequest.php';

/**
 * Admin Service CreditOrderRowsRequest class
 *
 * @author Kristian Grossman-Madsen
 */
class CancelOrderRowsRequest extends AdminServiceRequest {

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
        $this->action = "CancelPaymentPlanRows";
        $this->orderRows = array();
        $this->orderBuilder = $creditOrderRowsBuilder;

    }

    /**
     * populate and return soap request contents using AdminSoap helper classes to get the correct data format
     * @return Svea\AdminSoap\CreditOrderRowsRequest
     * @throws Svea\ValidationException
     */
    public function prepareRequest( $resendOrderWithFlippedPriceIncludingVat = false) {
        $this->validateRequest();
        $this->orderRows = $this->getAdminSoapOrderRowsFromBuilderOrderRowsUsingVatFlag( $this->orderBuilder->cancelOrderRows );
        $soapRequest = new AdminSoap\CancelPaymentPlanRowsRequest(
            new AdminSoap\Authentication(
                $this->orderBuilder->conf->getUsername( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ),
                $this->orderBuilder->conf->getPassword( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode )
            ),
            $this->orderRows,
            $this->orderBuilder->conf->getClientNumber( ($this->orderBuilder->orderType), $this->orderBuilder->countryCode ),
            $this->orderBuilder->contractNumber

        );

        return $soapRequest;
    }

    public function validate() {
        $errors = array();
        $errors = $this->validateContractNumber($errors);
        $errors = $this->validateHasRows($errors);
        $errors = $this->validateOrderType($errors);
        $errors = $this->validateCountryCode($errors);
        $errors = $this->validateCreditOrderRowsHasPriceAndVatInformation($errors);
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
        if (count($this->orderBuilder->cancelOrderRows) == 0) {
             $errors[] = array('missing value' => "no rows to cancel, use addCancelOrderRow(s)().");
        }
        return $errors;
    }

    private function validateCreditOrderRowsHasPriceAndVatInformation($errors) {
        foreach( $this->orderBuilder->cancelOrderRows as $orderRow ) {
            if( !isset($orderRow->vatPercent) && (!isset($orderRow->amountIncVat) && !isset($orderRow->amountExVat)) ) {
                $errors[] = array('missing order row vat information' => "cannot calculate orderRow vatPercent, need at least two of amountExVat, amountIncVat and vatPercent.");
            }
        }
        return $errors;
    }

    public function validateContractNumber($errors) {
          if (isset($this->orderBuilder->contractNumber) == FALSE) {
            $errors[] = array('missing value' => "contractNumber is required, use setContractNumber().");
        }
        return $errors;
    }

        protected function getAdminSoapOrderRowsFromBuilderOrderRowsUsingVatFlag($builderOrderRows, $priceIncludingVat = NULL) {
        $amount = 0;
        $orderRows = array();
        foreach ($builderOrderRows as $orderRow) {
            if (isset($orderRow->vatPercent) && isset($orderRow->amountExVat)) {
                $amount =  \Svea\WebService\WebServiceRowFormatter::convertExVatToIncVat($orderRow->amountExVat, $orderRow->vatPercent);
            } elseif (isset($orderRow->vatPercent) && isset($orderRow->amountIncVat)) {
                $amount = $orderRow->amountIncVat;
            } else {
                $amount = $orderRow->amountIncVat;
                $orderRow->vatPercent = \Svea\WebService\WebServiceRowFormatter::calculateVatPercentFromPriceExVatAndPriceIncVat($orderRow->amountIncVat, $orderRow->amountExVat);
            }
            $orderRows[] = new \SoapVar(
                new AdminSoap\CancellationRow(
                    $amount,
                    $this->formatRowNameAndDescription($orderRow),
                    $orderRow->rowNumber,
                    $orderRow->vatPercent
                ), SOAP_ENC_OBJECT, null, null, 'CancellationRow', "http://schemas.datacontract.org/2004/07/DataObjects.Webservice"
            );
        }
        return $orderRows;
    }
}
