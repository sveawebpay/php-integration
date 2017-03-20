<?php

namespace Svea\WebPay\BuildOrder;

use Svea\WebPay\AdminService\AdminSoap\AccountCredit\AccountCreditInformation;
use Svea\WebPay\AdminService\GetOrdersRequest;
use Svea\WebPay\AdminService\SearchOrdersRequest;
use Svea\WebPay\Checkout\Service\Admin\GetOrderService;
use Svea\WebPay\Helper\Helper;
use Svea\WebPay\Config\ConfigurationProvider;
use Svea\WebPay\HostedService\HostedAdminRequest\QueryTransaction;

/**
 * QueryOrderBuilder is the class used to query information about an order from Svea.
 *
 * Use setOrderId() to specify the Svea order id, this is the order id returned
 * with the original create order request response as either sveaOrderId or transactionId.
 *
 * Use setCountryCode() to specify the country code matching the original create
 * order request.
 *
 * Then get a request object using either queryInvoiceOrder(), queryPaymentPlanOrder(),
 * queryCardOrder(), or queryDirectBankOrder() or for Checkout order
 * use queryCheckoutOrder() which ever matches the payment method
 * used in the original order request, and send the query request to svea using the
 * request object doRequest() method.
 *
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class AccountCreditQueryBuilder extends CheckoutAdminOrderBuilder
{
    /**
     * @var ConfigurationProvider $conf
     */
    public $conf;

    /**
     * @var AccountCreditInformation []
     * */
    public $clientAccountCreditInformation = array();

    public function __construct($config) {
        $this->conf = $config;
    }

    /**
     * Add ClientAccountInformation, this is only for AccountCredit
     *
     * @var AccountCreditInformation @info
     * @return $this
     */
    public function addClientAccountCreditInformation($info)
    {
        $this->clientAccountCreditInformation [] = $info;

        return $this;
    }

    /**
     * Retrieve list of clientAccountCreditInformation
     */
    public function getClientAccountCreditInformation()
    {
        return $this->clientAccountCreditInformation;
    }

    public function queryAccountCreditOder()
    {
        $this->orderType = ConfigurationProvider::ACCOUNTCREDIT_TYPE;
        return new SearchOrdersRequest($this);
    }
}
