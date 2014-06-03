<?php
namespace Svea;

require_once 'OrderBuilder.php'; 
require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
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
class deliverOrderBuilder extends OrderBuilder {

     /**
     * Order Id is recieved in response to ->doRequest when creating order.
     * This is the link between deliverOrder and createOrder.
     * @var Order id
     */
    public $orderId;    

    public function __construct($config) {
        parent::__construct($config);
    }

    /**
     * @deprecated 2.0.0 Use WebPayAdmin::UpdateOrder to modify or partially deliver an order.
     * 
     * 1.x: Required. Use setOrderRos to add order rows to deliver. Rows matching 
     * the original create order request order rows will be invoiced by Svea. 
     * 
     * If not all order rows match, the order will be partially delivered/invoiced, 
     * see the Svea Web Service EU API documentation for details.
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
     * order has any order rows defined, if so performs a DeliverOrderEU request
     * to Svea, passing on the order rows.
     * 
     * If no order rows are defined, it performs av DeliverOrders request using
     * the Admin Web Service API at Svea.
     * 
     * @return DeliverInvoice
     */
    public function deliverInvoiceOrder() {
        if( count($this->orderRows) >0 ) {
            return new DeliverInvoice($this);
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
        return new DeliverPaymentPlan($this);
    }
    /** @var string orderType  one of "Invoice" or "PaymentPlan"*/
    public $orderType;

    /**
     * deliverCardOrder() sets the status of a card order to CONFIRMED.
     * A default capturedate equal to the current date will be supplied. This 
     * may be overridden using the ConfirmTransaction setCaptureDate() method 
     * @return DeliverPaymentPlan
     */
    public function deliverCardOrder() {        
        $this->orderType = \ConfigurationProvider::HOSTED_TYPE;
        
        $defaultCaptureDate = explode("T", date('c')); // [0] contains date part

        $confirmTransaction = new ConfirmTransaction($this->conf);
        $confirmTransaction
            ->setCountryCode($this->countryCode)
            ->setTransactionId($this->orderId)
            ->setCaptureDate($defaultCaptureDate[0])
        ;
        return $confirmTransaction;
    }    
}
