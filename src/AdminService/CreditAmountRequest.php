<?php

namespace Svea\WebPay\AdminService;

use SoapVar;
use Svea\WebPay\BuildOrder\CreditOrderRowsBuilder;
use Svea\WebPay\AdminService\AdminSoap\Authentication;
use Svea\WebPay\AdminService\AdminSoap\CancellationRow;
use Svea\WebPay\Helper\Helper;
use Svea\WebPay\WebService\Helper\WebServiceRowFormatter;
use Svea\WebPay\BuildOrder\Validator\ValidationException;
use Svea\WebPay\AdminService\AdminSoap\CancelPaymentPlanAmountRequest;

/**
 * Admin Service CreditAmountRequest class
 *
 * @author ann-hal
 */
class CreditAmountRequest extends AdminServiceRequest
{

    /** 
     * @var CreditOrderRowsBuilder $orderBuilder 
     */
    public $orderBuilder;

    /**
     * @param CreditAmountBuilder $creditAmountBuilder
     */
    public function __construct($creditAmountBuilder)
    {
        $this->action = "CancelPaymentPlanAmount";
        $this->orderBuilder = $creditAmountBuilder;

    }

    /**
     * populate and return soap request contents using AdminSoap helper classes to get the correct data format
     * @param bool $resendOrderWithFlippedPriceIncludingVat
     * @return CreditOrderRowsRequest
     * @throws ValidationException
     */
    public function prepareRequest($resendOrderWithFlippedPriceIncludingVat = false)
    {
        $this->validateRequest();
        $soapRequest = new CancelPaymentPlanAmountRequest(
            new Authentication(
                $this->orderBuilder->conf->getUsername(($this->orderBuilder->orderType), $this->orderBuilder->countryCode),
                $this->orderBuilder->conf->getPassword(($this->orderBuilder->orderType), $this->orderBuilder->countryCode)
            ),
            $this->orderBuilder->amountIncVat,
            $this->orderBuilder->description,
            $this->orderBuilder->conf->getPassword(($this->orderBuilder->orderType), $this->orderBuilder->countryCode),
            $this->orderBuilder->contractNumber

        );

        return $soapRequest;
    }

    public function validate()
    {
        $errors = array();
        $errors = $this->validateContractNumber($errors);
        $errors = $this->validateOrderType($errors);
        $errors = $this->validateCountryCode($errors);
        $errors = $this->validateAmount($errors);

        return $errors;
    }

    public function validateContractNumber($errors)
    {
        if (isset($this->orderBuilder->contractNumber) == FALSE) {
            $errors[] = array('missing value' => "contractNumber is required, use setContractNumber().");
        }

        return $errors;
    }

    private function validateOrderType($errors)
    {
        if (isset($this->orderBuilder->orderType) == FALSE) {
            $errors[] = array('missing value' => "orderType is required.");
        }

        return $errors;
    }

    private function validateCountryCode($errors)
    {
        if (isset($this->orderBuilder->countryCode) == FALSE) {
            $errors[] = array('missing value' => "countryCode is required, use setCountryCode().");
        }

        return $errors;
    }

    private function validateAmount($errors)
    {
        if (!isset($this->orderBuilder->amountIncVat) || $this->orderBuilder->amountIncVat <= 0) {
            $errors[] = array('incorrect value' => "amountIncVat is too small.");
        } elseif (isset($this->orderBuilder->amountIncVat) && !(is_float($this->orderBuilder->amountIncVat) || is_int($this->orderBuilder->amountIncVat))) {
            $errors[] = array('incorrect datatype' => "amountIncVat is not of type float or float.");
        }

        return $errors;
    }

    protected function getAdminSoapOrderRowsFromBuilderOrderRowsUsingVatFlag($builderOrderRows, $priceIncludingVat = NULL)
    {
        $amount = 0;
        $orderRows = array();
        foreach ($builderOrderRows as $orderRow) {
            if (isset($orderRow->vatPercent) && isset($orderRow->amountExVat)) {
                $amount = WebServiceRowFormatter::convertExVatToIncVat($orderRow->amountExVat, $orderRow->vatPercent);
            } elseif (isset($orderRow->vatPercent) && isset($orderRow->amountIncVat)) {
                $amount = $orderRow->amountIncVat;
            } else {
                $amount = $orderRow->amountIncVat;
                $orderRow->vatPercent = WebServiceRowFormatter::calculateVatPercentFromPriceExVatAndPriceIncVat($orderRow->amountIncVat, $orderRow->amountExVat);
            }
            $orderRows[] = new SoapVar(
                new CancellationRow(
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
