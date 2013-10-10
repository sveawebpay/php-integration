<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class deliverOrderBuilder {

    /**
     * @var Array Rows containing Product rows
     */
    public $orderRows = array();
    /**
     * @var Array ShippingFeeRows containing shippingFee rows
     */
    public $shippingFeeRows = array();
    /**
     * @var  Array InvoiceFeeRows containing invoiceFee rows
     */
    public $invoiceFeeRows = array();
    /**
     * @var Array FixedDiscountRows containing fixed discount rows
     */
    public $fixedDiscountRows = array();
    /**
     * @var Array RelativeDiscountRows containing relative discount rows
     */
    public $relativeDiscountRows = array();
    /**
     * @var testmode. False means in production mode
     */
    public $testmode = false;
    
     /**
     * Order Id is recieved in response to ->doRequest when creating order.
     * This is the link between deliverOrder and createOrder.
     * @var Order id
     */
    public $orderId;
    
    /**
     * @var type String "Invoice" or "PaymentPlan"
     */
    public $orderType;
    public $countryCode;

    public $numberOfCreditDays;
    /**
     * @var type String "Post" or "Email"
     */
    public $distributionType;
    /**
     * If Invoice is to be credit Invoice
     * @var Invoice Id
     */
    public $invoiceIdToCredit;
    /**
     * @var Instance of class SveaConfig
     */
    public $conf;

    public function __construct($config) {
        $this->handleValidator = new HandleOrderValidator();
        $this->conf = $config;
    }

    /**
     * New!
     * @param type $orderRow
     * @return \deliverOrderBuilder
     */
    public function addOrderRow($orderRow) {
        if (is_array($orderRow)) {
            foreach ($orderRow as $row) {
                array_push($this->orderRows, $row);
            }
        } else {
            array_push($this->orderRows, $orderRow);
        }
        
       return $this;
    }

    /**
     * New!
     * @param type $itemFeeObject
     * @return \deliverOrderBuilder
     */
    public function addFee($itemFeeObject) {
        if (is_array($itemFeeObject)) {
            foreach ($itemFeeObject as $row) {
                if (get_class($row) == "Svea\ShippingFee") {
                    array_push($this->shippingFeeRows, $row);
                }
                if (get_class($row) == "Svea\InvoiceFee") {
                    array_push($this->invoiceFeeRows, $row);
                }
            }
        } else {
            if (get_class($itemFeeObject) == "Svea\ShippingFee") {
                array_push($this->shippingFeeRows, $itemFeeObject);
            }
            if (get_class($itemFeeObject) == "Svea\InvoiceFee") {
                array_push($this->invoiceFeeRows, $itemFeeObject);
            }
        }

        return $this;
    }

    /**
     * New!
     * @param type $itemDiscounObject
     * @return \deliverOrderBuilder
     */
    public function addDiscount($itemDiscounObject) {
        if (is_array($itemDiscounObject)) {
            foreach ($itemDiscounObject as $row) {
                if (get_class($row) == "Svea\FixedDiscount") {
                    array_push($this->fixedDiscountRows, $row);
                }
                if (get_class($row) == "Svea\RelativeDiscount") {
                    array_push($this->relativeDiscountRows, $row);
                }
            }
        } else {
            if (get_class($itemDiscounObject) == "Svea\FixedDiscount") {
                array_push($this->fixedDiscountRows, $itemDiscounObject);
            }
            if (get_class($itemDiscounObject) == "Svea\RelativeDiscount") {
                array_push($this->relativeDiscountRows, $itemDiscounObject);
            }
        }
        
       return $this;
    }

    /**
     * When function is called it turns into testmode
     * @return \deliverOrder

    public function setTestmode() {
        $this->testmode = TRUE;
        return $this;
    }
     */

    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }

    /**
     * Required
     * @param type $orderIdAsString
     * @return \deliverOrder
     */
    public function setOrderId($orderIdAsString) {
        $this->orderId = $orderIdAsString;
        return $this;
    }

    /**
     * Invoice payments only! Required
     * @param type DistributionType $distributionTypeAsConst ex. DistributionType::POST or DistributionType::EMAIL
     * @return \deliverOrder
     */
    public function setInvoiceDistributionType($distributionTypeAsConst) {
        if ($distributionTypeAsConst != \ DistributionType::EMAIL || $distributionTypeAsConst != \ DistributionType::POST) {
            $distributionTypeAsConst = trim($distributionTypeAsConst);
            if (preg_match("/post/i", $distributionTypeAsConst)) {
                $distributionTypeAsConst = \ DistributionType::POST;
            } elseif (preg_match("/mail/i", $distributionTypeAsConst)) {
                $distributionTypeAsConst = \ DistributionType::EMAIL;
            } else {
                $distributionTypeAsConst = \ DistributionType::POST;
            }
        }
        $this->distributionType = $distributionTypeAsConst;
        
        return $this;
    }

    /**
     * Invoice payments only!
     * Use if this should be a credit invoice
     * @param type $invoiceId
     * @return \deliverOrder
     */
    public function setCreditInvoice($invoiceId) {
        $this->invoiceIdToCredit = $invoiceId;
        return $this;
    }

    /**
     * Invoice payments only!
     * @param type $numberOfDaysAsInt
     * @return \deliverOrder
     */
    public function setNumberOfCreditDays($numberOfDaysAsInt) {
        $this->numberOfCreditDays = $numberOfDaysAsInt;
        return $this;
    }

    /**
     * deliverInvoiceOrder updates the Invoice order with additional information and prepares it for delivery.
     * The method will automatically match all order rows that are to be delivered to those rows that was sent when creating the Invoice order.
     * @return \DeliverInvoice
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
