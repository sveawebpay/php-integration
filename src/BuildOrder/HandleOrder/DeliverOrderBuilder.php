<?php

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * Description of deliverOrder
 *
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
     * Order Id recieved when creating order
     * @var Order id
     */
    public $orderId;
    /**
     * @var type String "Invoice" or "PaymentPlan"
     */
    public $orderType;
  
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

    public function __construct() {
        $this->handleValidator = new HandleOrderValidator();
        $this->conf = SveaConfig::getConfig();
    }

    /**
     * Begin building row for product or other values.
     * Use for all Payment types when Creating Order.
     * If this is a DeliverOrder: Only use for InvoicePayments.
     * @return \OrderRowBuilder
     */
    public function beginOrderRow() {
        $rowBuilder = new DeliverOrderRowBuilder($this);
        array_push($this->orderRows, $rowBuilder);
        return $rowBuilder;
    }
    /**
     * New!
     * @param type $orderRow
     * @return \deliverOrderBuilder
     */
     public function addOrderRow($orderRow){
        if(is_array($orderRow)){
            foreach ($orderRow as $row) {
                array_push($this->orderRows, $row);
            }
        }  else {
             array_push($this->orderRows, $orderRow);
        }      
       return $this;
    }
    
    /**
     * Begin building Shipping fee row
     * Use for all Payment types when Creating Order.
     * If this is a DeliverOrder: Only use for InvoicePayments.
     * @return shippingFeeRows
     */
    public function beginShippingFee() {
        $shippingFeeBuilder = new DeliverShippingFeeBuilder($this);
        array_push($this->shippingFeeRows, $shippingFeeBuilder);
        return $shippingFeeBuilder;
    }
    /**
     * 
     * @param type $itemShippingFeeObject
     * @return \deliverOrderBuilder
     */
    public function addShippingFee($itemShippingFeeObject){    
         if(is_array($itemShippingFeeObject)){
            foreach ($itemShippingFeeObject as $row) {
                array_push($this->shippingFeeRows, $row);
            }
        }  else {
        array_push($this->shippingFeeRows, $itemShippingFeeObject);
        }
      
       return $this;
    }
    /**
     * Begin building Invoice fee row
     * Use for all Payment types when Creating Order.
     * If this is a DeliverOrder: Only use for InvoicePayments.
     * @return \InvoiceFeeBuilder
     */
    public function beginInvoiceFee() {
        $invoiceFeeBuilder = new DeliverInvoiceFeeBuilder($this);
        array_push($this->invoiceFeeRows, $invoiceFeeBuilder);
        return $invoiceFeeBuilder;
    }
    
    /**
     * Begin building Discount row for fixed discount
     * @return \FixedDiscountBuilder
     */
    public function beginFixedDiscount() {
        $fixedDiscountRowBuilder = new DeliverFixedDiscountBuilder($this);
        array_push($this->fixedDiscountRows, $fixedDiscountRowBuilder);
        return $fixedDiscountRowBuilder;
    }

    /**
     * Begin building Discount row for relative discount
     * @return \RelativeDiscountBuilder
     */
    public function beginRelativeDiscount() {
        $relativeDiscountBuilder = new DeliverRelativeDiscountBuilder($this);
        array_push($this->relativeDiscountRows, $relativeDiscountBuilder);
        return $relativeDiscountBuilder;
    }
    
    /**
     * When function is called it turns into testmode
     * @return \deliverOrder
     */
    public function setTestmode() {
        $this->testmode = TRUE;
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
        if($distributionTypeAsConst != DistributionType::EMAIL || $distributionTypeAsConst != DistributionType::POST){
            $distributionTypeAsConst = trim($distributionTypeAsConst);
            if(preg_match("/post/i", $distributionTypeAsConst)){
                $distributionTypeAsConst = DistributionType::POST;
            }elseif(preg_match("/mail/i", $distributionTypeAsConst)){
                $distributionTypeAsConst = DistributionType::EMAIL;
            }else{
                $distributionTypeAsConst = DistributionType::POST;
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

?>
