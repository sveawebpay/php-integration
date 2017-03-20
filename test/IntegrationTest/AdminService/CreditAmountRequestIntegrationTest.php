<?php

namespace Svea\WebPay\Test\IntegrationTest\AdminService;
 
use PHPUnit_Framework_TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\DistributionType;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\WebPayItem;
use stdClass;

/** helper class, used to return information about an order */


/**
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class CreditAmountRequestIntegrationTest extends PHPUnit_Framework_TestCase
{

    public static function getAccountCreditParamsForTesting()
    {
        $ppCampaign = WebPay::getAccountCreditParams(ConfigurationService::getDefaultConfig());

        $campaigns = $ppCampaign->setCountryCode('SE')
            ->doRequest();

        return $campaigns->AccountCreditCampaignCodes[0]->campaignCode;
    }

    public static function getCustomer()
    {
        return WebPayItem::individualCustomer()
            ->setNationalIdNumber("194605092222")
            ->setBirthDate(1986, 03, 31)
            ->setName("Janko", "Stevanovic")
            ->setStreetAddress("Neka tamo", 1)
            ->setCoAddress("c/o BB, Batajnica")
            ->setLocality("Okrug Beograda")
            ->setEmail('batajarules@svea.com')
            ->setZipCode("99999");
    }

    /** helper function, returns invoice for delivered order with one row, sent with PriceIncludingVat flag set to true */
    public function get_orderInfo_sent_inc_vat($amount, $vat, $quantity)
    {
        $config = ConfigurationService::getDefaultConfig();

        $campaignCode = self::getAccountCreditParamsForTesting();

        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat($amount)
                    ->setVatPercent($vat)
                    ->setQuantity($quantity)
            )
            ->addCustomerDetails(self::getCustomer())
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12");


        $orderResponse = $orderResponse->useAccountCredit($campaignCode)
                ->doRequest();


        $this->assertEquals(1, $orderResponse->accepted);

        $svea_order_id = $orderResponse->sveaOrderId;

        $svea_delivery_request = \Svea\WebPay\WebPay::deliverOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($svea_order_id)
            ->setOrderDate(date('c'))
            ->setCountryCode('SE')
            ->setInvoiceDistributionType(DistributionType::POST)
            ->deliverAccountCreditOrder()
            ->doRequest();


        $this->assertEquals(1, $svea_delivery_request->accepted);

        $response =  new stdClass();
        $response->sveaOrderId = $orderResponse->sveaOrderId;
        $response->referenceNumber = $svea_delivery_request->deliveryReferenceNumber;
        return $response;
    }

    /** helper function, returns invoice for delivered order with one row, sent with PriceIncludingVat flag set to false */
    public function get_orderInfo_sent_ex_vat($amount, $vat, $quantity)
    {
        $config = ConfigurationService::getDefaultConfig();

        $campaignCode = self::getAccountCreditParamsForTesting();

        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat($amount)
                    ->setVatPercent($vat)
                    ->setQuantity($quantity)
            )
            ->addCustomerDetails(self::getCustomer())
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12");


        $orderResponse = $orderResponse->useAccountCredit($campaignCode)
            ->doRequest();


        $this->assertEquals(1, $orderResponse->accepted);

        $svea_order_id = $orderResponse->sveaOrderId;

        $svea_delivery_request = \Svea\WebPay\WebPay::deliverOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($svea_order_id)
            ->setOrderDate(date('c'))
            ->setCountryCode('SE')
            ->setInvoiceDistributionType(DistributionType::POST)
            ->deliverAccountCreditOrder()
            ->doRequest();


        $this->assertEquals(1, $svea_delivery_request->accepted);

        $response =  new stdClass();
        $response->sveaOrderId = $orderResponse->sveaOrderId;
        $response->referenceNumber = $svea_delivery_request->deliveryReferenceNumber;
        return $response;
    }


    public function test_creditAmount_creditPaymentPlan_on_order_ex_vat()
    {
        $config = ConfigurationService::getDefaultConfig();

        $orderInfo = $this->get_orderInfo_sent_ex_vat(999.99, 24, 1);
        $credit = WebPayAdmin::creditAmount($config)
            ->setOrderId($orderInfo->referenceNumber)
            ->setCountryCode('SE')
            ->setDescription('credit desc')
            ->setAmountIncVat(100)
            ->creditAccountCredit()->doRequest();

        $this->assertEquals(1, $credit->accepted);
        //print_r($credit);
    }

    public function test_creditAmount_creditPaymentPlan_on_order_inc_vat()
    {
        $config = ConfigurationService::getDefaultConfig();

        $orderInfo = $this->get_orderInfo_sent_inc_vat(1000.00, 25, 1);
        $credit = WebPayAdmin::creditAmount($config)
            ->setOrderId($orderInfo->referenceNumber)
            ->setCountryCode('SE')
            ->setDescription('credit desc')
            ->setAmountIncVat(100.00)
            ->creditAccountCredit()->doRequest();

        $this->assertEquals(1, $credit->accepted);
    }

    public function test_creditAmount_creditPaymentPlan_amount_exceeds_orderamount()
    {
        $config = ConfigurationService::getDefaultConfig();

        $orderInfo = $this->get_orderInfo_sent_inc_vat(1000.00, 25, 1);
        $credit = WebPayAdmin::creditAmount($config)
            ->setOrderId($orderInfo->referenceNumber)
            ->setCountryCode('SE')
            ->setDescription('credit desc')
            ->setAmountIncVat(1500.00)
            ->creditAccountCredit()->doRequest();

        $this->assertEquals(0, $credit->accepted);
    }

}