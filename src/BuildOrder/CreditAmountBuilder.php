<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * The WebPayAdmin::creditAmount entrypoint method is used to credit an amount in an order after it has been delivered.
 * Supports Payment Plan order.
 */
class CreditAmountBuilder {

    /** @var ConfigurationProvider $conf  */
    public $conf;

    /** @var string $orderType -- one of ConfigurationProvider::INVOICE_TYPE, ::HOSTED_ADMIN_TYPE */
    public $orderType;

    /** @var float $amountIncVat */
    public $amountIncVat;

    /** @var string @contractNumber  contract number as returned in the deliverOrder request response. PaymentPlan orders only. */
    public $contractNumber;

    /** @var string $countryCode */
    public $countryCode;

    /** @var string $description*/
    public $description;

    public function __construct($config) {
        $this->conf = $config;
    }

    /**
     *
     * @param float $AmountAsFloat
     * @return $this
     */
    public function setAmountIncVat($amountAsFloat) {
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
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

    /**
     * PaymentPlan only
     * Required for creditPaymentPlanOrder() -- use contract number recieved with deliverOrder response.
     *
     * Use setInvoiceId() to set the invoice to credit. Use setContractNumber() to set the contract id to credit. Use setOrderId() to set the
     * card or direct bank transaction to credit.
     *
     * @param string $contractNumberAsString
     * @return $this
     */
    public function setContractNumber($contractNumberAsString) {
        $this->contractNumber = $contractNumberAsString;
        return $this;
    }

    /**
     * Required
     *
     * @param string $descriptionAsString
     * @return $this
     */
    public function setDescription($descriptionAsString) {
        $this->description = $descriptionAsString;
        return $this;
    }

    /**
     * Use creditPaymentPlanOrderRows() to cancel rows to a delivered Payment plan using AdminServiceRequest CreditOrderRows request
     * @return \Svea\AdminService\CreditOrderRowsRequest
     */
    public function cancelPaymentPlanAmount() {
        $this->orderType = \ConfigurationProvider::PAYMENTPLAN_TYPE;
        // validation is done in CreditOrderRowsRequest
        return new AdminService\CancelAmountRequest($this);
    }

}
