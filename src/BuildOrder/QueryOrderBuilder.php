<?php
namespace Svea;

require_once SVEA_REQUEST_DIR . '/Includes.php';

/**
 * QueryOrderBuilder is the class used to query information about an order from Svea.
 * 
 * Supports fetching information about invoice, payment plan and card orders
 * 
 * Use setOrderId() to specify the Svea order id, this is the order id returned 
 * with the original create order request response.
 *
 * Use setCountryCode() to specify the country code matching the original create
 * order request.
 * 
 * Use either queryInvoiceOrder(), queryPaymentPlanOrder or queryCardOrder,
 * which ever matches the payment method used in the original order request.
 *  
 * The final doRequest() will send the queryOrder request to Svea, and the 
 * resulting response code specifies the outcome of the request. 
 * 
 * @author Kristian Grossman-Madsen for Svea WebPay
 */
class QueryOrderBuilder {

    /** ConfigurationProvider $conf  */
    public $conf;
    
    public function __construct($config) {
         $this->conf = $config;
    }

    /**
     * Required. Use SveaOrderId recieved with createOrder response.
     * @param string $orderIdAsString
     * @return $this
     */
    public function setOrderId($orderIdAsString) {
        $this->orderId = $orderIdAsString;
        return $this;
    }
    /** string $orderId  Svea order id to query, as returned in the createOrder request response, either a transactionId or a SveaOrderId */
    public $orderId;
    
    /**
     * Required. Use same countryCode as in createOrder request.
     * @param string $countryCode
     * @return $this
     */
    public function setCountryCode($countryCodeAsString) {
        $this->countryCode = $countryCodeAsString;
        return $this;
    }
    /** @var string $countryCode */
    public $countryCode;

    /**
     * Required.
     * @param string $orderType -- one of ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE, ::HOSTED_TYPE
     * @return $this
     */
    public function setOrderType($orderTypeAsConst) {
        $this->orderType = $orderTypeAsConst;
        return $this;
    }
    /** @var string $orderType -- one of ConfigurationProvider::INVOICE_TYPE, ::PAYMENTPLAN_TYPE, ::HOSTED_TYPE */
    public $orderType;    
    
    /**
     * Use queryInvoiceOrder() to query an Invoice order using AdminServiceRequest GetOrders request
     * @return AdminService\GetOrdersRequest 
     */
    public function queryInvoiceOrder() {
        $this->setOrderType(\ConfigurationProvider::INVOICE_TYPE );
        return new AdminService\GetOrdersRequest($this);
    }
    
    /**
     * Use queryPaymentPlanOrder() to query an PaymentPlan order using AdminServiceRequest GetOrders request
     * @return AdminService\GetOrdersRequest 
     */
    public function queryPaymentPlanOrder() {
        $this->setOrderType(\ConfigurationProvider::PAYMENTPLAN_TYPE);
        return new AdminService\GetOrdersRequest($this);    
    }

    /**
     * Use queryCardOrder() to query a Card order.
     * @return HostedService\QueryTransaction
     */
    public function queryCardOrder() {
        $this->setOrderType(\ConfigurationProvider::HOSTED_ADMIN_TYPE);
        $queryTransaction = new HostedService\QueryTransaction($this->conf);
        $queryTransaction->transactionId = $this->orderId;
        $queryTransaction->setCountryCode($this->countryCode);
        return $queryTransaction;
    }  

    /**
     * Use queryDirectBankOrder() to query a Direct Bank order.
     * @return HostedService\QueryTransaction
     */
    public function queryDirectBankOrder() {
        $this->setOrderType(\ConfigurationProvider::HOSTED_ADMIN_TYPE);
        $queryTransaction = new HostedService\QueryTransaction($this->conf);
        $queryTransaction->transactionId = $this->orderId;
        $queryTransaction->setCountryCode($this->countryCode);
        return $queryTransaction;
    }          
}
