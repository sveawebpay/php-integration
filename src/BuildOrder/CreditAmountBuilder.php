<?php

namespace Svea\WebPay\BuildOrder;

use Svea\WebPay\AdminService\CreditAmountRequest;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\BuildOrder\Validator\ValidationException;

/**
 * The Svea\WebPay\WebPayAdmin::creditAmount entrypoint method is used to credit an amount in an order after it has
 * been delivered. Supports Payment Plan order.
 */
class CreditAmountBuilder extends PaymentAdminOrderBuilder
{
    /**
     * @var ConfigurationProvider $conf
     */
    public $conf;

    /**
     * @var string $orderType -- one of Svea\WebPay\Config\ConfigurationProvider::INVOICE_TYPE, PAYMENTPLAN_TYPE,
     *     ::HOSTED_ADMIN_TYPE
     */
    public $orderType;

    /**
     * @var float $amountIncVat
     */
    public $amountIncVat;

    /**
     * @var string @contractNumber  contract number as returned in the deliverOrder request response. PaymentPlan
     *     orders only.
     */
    public $contractNumber;

    /**
     * @var string $countryCode
     */
    public $countryCode;

    /**
     * @var string $description
     */
    public $description;

    /**
     * Required
     * @param float $amountAsFloat
     * @return $this
     */
    public function setAmountIncVat($amountAsFloat)
    {
        $this->amountIncVat = $amountAsFloat;

        return $this;
    }

    /**
     * Required -- use same countryCode as in createOrder request
     *
     * Use setCountryCode() to specify the country code matching the original
     * createOrder request.
     *
     * @param string $countryCodeAsString
     * @return $this
     */
    public function setCountryCode($countryCodeAsString)
    {
        $this->countryCode = $countryCodeAsString;

        return $this;
    }

    /**
     * PaymentPlan only
     * Required for creditPaymentPlanOrder() -- use contract number recieved with deliverOrder response.
     *
     *
     * @param string $contractNumberAsString
     * @return $this
     */
    public function setContractNumber($contractNumberAsString)
    {
        $this->contractNumber = $contractNumberAsString;

        return $this;
    }

    /**
     * Required
     *
     * @param string $descriptionAsString
     * @return $this
     */
    public function setDescription($descriptionAsString)
    {
        $this->description = $descriptionAsString;

        return $this;
    }

    /**
     * Use creditPaymentPlanAmount() to cancel amount to a delivered Payment plan using AdminServiceRequest
     * CreditAmount request
     * @return CancelAmountRequest
     */
    public function creditPaymentPlanAmount()
    {
        $this->orderType = ConfigurationProvider::PAYMENTPLAN_TYPE;

        // CreditPaymentPlan amount is really a CancelPaymentPlanAmount in API but wrapped in lib
        return new CreditAmountRequest($this);
    }

    
}