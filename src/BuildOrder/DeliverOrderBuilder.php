<?php
namespace Svea;

require_once 'OrderBuilder.php'; 
require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * DeliverOrderBuilder collects and prepares order data for use in a deliver 
 * order request to Svea.
 * 
 * For invoice and payment plan orders, the deliver order request should 
 * generally be sent to Svea once the ordered items have been sent out, or 
 * otherwise delivered, to the customer.
 * 
 * For invoice and payment plan orders, the deliver order request triggers the 
 * customer invoice being sent out to the customer by Svea. 
 * 
 * For card orders, the deliver order request confirms the card transaction, 
 * which in turn causes the card transaction to be batch processed by Svea. An 
 * auto-confirm account setting is also available, ask your Svea integration 
 * manager about this.
 * 
 * For card orders, the deliver order request confirms the card transaction, which in
 * turn causes the card transaction to be batch processed by Svea.
 * 
 * Generally, orders are delivered in full, and so will also be the case for orders
 * delivered when no order rows have been added to the DeliverOrderBuilder object.
 * 
 * Set all required order attributes in a DeliverOrderBuilder instance by using the 
 * OrderBuilder setAttribute() methods. Instance methods can be chained together, as 
 * they return the instance itself in a fluent manner.
 * 
 * Finish by using the delivery method matching the payment method specified in the 
 * createOrder request.
 * 
 * You can then go on specifying any payment method specific settings, using methods provided by the 
 * returned deliver order request class.
 * 
 * Invoice required methods: 
 * ->addOrderRow( TestUtil::createOrderRow() )
 * ->setCountryCode("SE")
 * ->setOrderId( $orderId )
 * ->setInvoiceDistributionType(\DistributionType::POST)
 *
 * PaymentPlan required methods:
 * ->addOrderRow( TestUtil::createOrderRow() )
 * ->setCountryCode("SE")
 * ->setOrderId( $orderId )
 * 
 * Card required methods:
 * ->setOrderId( $orderId )
 * ->setCountryCode("SE")
 * Card optional methods:
 * ->setCaptureDate( $orderId )
 *  
 * @author Kristian Grossman-Madsen, Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class DeliverOrderBuilder extends OrderBuilder {

     /**
     * Order Id is recieved when creating order.
     * This is the link between deliverOrder and createOrder.
     * @var numeric $orderId
     */
    public $orderId;    

    /**
     * @deprecated 2.0.0 Use WebPayAdmin::UpdateOrder to modify or partially deliver an order.
     * 
     * 1.x: Required. Use setOrderRos to add order rows to deliver. Rows matching 
     * the original create order request order rows will be invoiced by Svea. 
     * 
     * If not all order rows match, the order will be partially delivered/invoiced, 
     * see the Svea Web Service EU API documentation for details on how this works.
     */
    public function addOrderRow($itemOrderRowObject) {
        return parent::addOrderRow($itemOrderRowObject);
    }

    /* Required. Set order id of the order you wish to deliver.
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
    /**
     * If Invoice is to be credit Invoice
     * @var Invoice Id
     */
    public $invoiceIdToCredit;

    /**
     * Invoice payments only!
     * @param int $numberOfDaysAsInt
     * @return $this
     */
    public function setNumberOfCreditDays($numberOfDaysAsInt) {
        $this->numberOfCreditDays = $numberOfDaysAsInt;
        return $this;
    }
    /** @var int $numberOfCreditDays */
    public $numberOfCreditDays;

    
    /**
     * To ensure backwards compatibility, deliverInvoiceOrder() checks if the 
     * order has any order rows defined, and if so performs a DeliverOrderEU 
     * request to Svea, passing on the order rows.
     * 
     * If no order rows are defined, deliverInvoiceOrder() performs a 
     * DeliverOrders request using the Admin Web Service API at Svea.
     * 
     * @return WebService\DeliverInvoice|AdminService\DeliverOrdersRequest
     */
    public function deliverInvoiceOrder() {
        if( count($this->orderRows) > 0 ) {
            return new WebService\DeliverInvoice($this);
        }
        else {
            $this->orderType = "Invoice";
            return new AdminService\DeliverOrdersRequest($this);
        }
    }

    /**
     * deliverPaymentPlanOrder prepares the PaymentPlan order for delivery.
     * @return DeliverPaymentPlan
     */
    public function deliverPaymentPlanOrder() {
        if( count($this->orderRows) > 0 ) {
            return new WebService\DeliverPaymentPlan($this);
        }
        else {
            $this->orderType = "PaymentPlan";
            return new AdminService\DeliverOrdersRequest($this);
        }
    }
    /** @var string orderType  one of "Invoice" or "PaymentPlan"*/
    public $orderType;

    /**
     * deliverCardOrder() sets the status of a card order to CONFIRMED.
     * 
     * A default capturedate equal to the current date will be supplied. This 
     * may be overridden using the ConfirmTransaction setCaptureDate() method 
     * on the returned ConfirmTransaction object.
     * 
     * @return DeliverPaymentPlan
     */
    public function deliverCardOrder() {        
        $this->orderType = \ConfigurationProvider::HOSTED_TYPE;
        
        $defaultCaptureDate = explode("T", date('c')); // [0] contains date part

        $confirmTransaction = new HostedService\ConfirmTransaction($this->conf);
        $confirmTransaction->transactionId = $this->orderId;
        $confirmTransaction->captureDate = $defaultCaptureDate[0];
        $confirmTransaction->setCountryCode($this->countryCode);
        return $confirmTransaction;
    }    

    /**  
     * @param \ConfigurationProvider $config 
     */
    public function __construct($config) {
        parent::__construct($config);
    }
}
