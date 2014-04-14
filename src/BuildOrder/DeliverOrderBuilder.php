<?php
namespace Svea;

require_once 'OrderBuilder.php'; 
require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class deliverOrderBuilder extends OrderBuilder {

     /**
     * Order Id is recieved in response to ->doRequest when creating order.
     * This is the link between deliverOrder and createOrder.
     * @var Order id
     */
    public $orderId;
    
    /**
     * @var string String "Invoice" or "PaymentPlan"
     */
    //public $orderType;

    public function __construct($config) {
        $this->handleValidator = new HandleOrderValidator();
        parent::__construct($config);
    }

    /**
     * Required.
     * @param string $orderIdAsString
     * @return $this
     */
    public function setOrderId($orderIdAsString) {
        $this->orderId = $orderIdAsString;
        return $this;
    }

    /**
     * Invoice payments only! Required.
     * @param string DistributionType $distributionTypeAsConst  i.e. DistributionType::POST|DistributionType::EMAIL
     * @return $this
     */
    public function setInvoiceDistributionType($distributionTypeAsConst) {
        if ($distributionTypeAsConst != \DistributionType::EMAIL || $distributionTypeAsConst != \DistributionType::POST) {
            $distributionTypeAsConst = trim($distributionTypeAsConst);
            if (preg_match("/post/i", $distributionTypeAsConst)) {
                $distributionTypeAsConst = \DistributionType::POST;
            } elseif (preg_match("/mail/i", $distributionTypeAsConst)) {
                $distributionTypeAsConst = \DistributionType::EMAIL;
            } else {
                $distributionTypeAsConst = \DistributionType::POST;
            }
        }
        $this->distributionType = $distributionTypeAsConst;
        return $this;
    }
    /**
     * @var string  "Post" or "Email"
     */
    public $distributionType;
    
    /**
     * Invoice payments only!
     * Use if this should be a credit invoice
     * @param type $invoiceId
     * @return $this
     */
    public function setCreditInvoice($invoiceId) {
        $this->invoiceIdToCredit = $invoiceId;
        return $this;
    }
    /** @var int */
    public $numberOfCreditDays;

    
    
    /**
     * If Invoice is to be credit Invoice
     * @var Invoice Id
     */
    public $invoiceIdToCredit;

    /**
     * Invoice payments only!
     * @param int $numberOfDaysAsInt
     * @return \deliverOrder
     */
    public function setNumberOfCreditDays($numberOfDaysAsInt) {
        $this->numberOfCreditDays = $numberOfDaysAsInt;
        return $this;
    }

    /**
     * deliverInvoiceOrder updates the Invoice order with additional information and prepares it for delivery.
     * The method will automatically match all order rows that are to be delivered to those rows that was sent when creating the Invoice order.
     * @return DeliverInvoice
     */
    public function deliverInvoiceOrder() {
        $this->orderType = "Invoice";
        $this->handleValidator->validate($this);
        return new DeliverInvoice($this);
    }

    /**
     * deliverPaymentPlanOrder prepares the PaymentPlan order for delivery.
     * @return \DeliverPaymentPlan
     */
    public function deliverPaymentPlanOrder() {
        $this->orderType = "PaymentPlan";
        $this->handleValidator->validate($this);
        return new DeliverPaymentPlan($this);
    }
}
