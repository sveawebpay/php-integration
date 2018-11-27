<?php
// Integration tests should not need to use the namespace

namespace Svea\WebPay\Test\IntegrationTest\WebService\HandleOrder;

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Test\TestUtil;
use \PHPUnit\Framework\TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\DistributionType;

/**
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class DeliverPaymentPlanIntegrationTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Function to use in testfunctions
     * @return SveaOrderId
     */
    private function getPaymentPlanOrderId()
    {
        $config = ConfigurationService::getDefaultConfig();
        $response = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow(1000.00, 1))
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->usePaymentPlanPayment(TestUtil::getGetPaymentPlanParamsForTesting())
            ->doRequest();

        return $response->sveaOrderId;
    }

    public function testDeliverPaymentPlanOrder()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderId = $this->getPaymentPlanOrderId();
        $orderBuilder = WebPay::deliverOrder($config);
        $request = $orderBuilder
            ->addOrderRow(TestUtil::createOrderRow(1000.00, 1))
            ->setOrderId($orderId)
            ->setCountryCode("SE")
            ->deliverPaymentPlanOrder()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(0, $request->resultcode);
        $this->assertEquals(1250, $request->amount);
        $this->assertEquals('PaymentPlan', $request->orderType);
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    public function testDeliverPaymentPlanOrder_missing_setOrderId_throws_ValidationException()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderId = $this->getPaymentPlanOrderId();
        $orderBuilder = WebPay::deliverOrder($config);
        $request = $orderBuilder
            //->setOrderId($orderId)
            ->setCountryCode("SE")
            ->deliverPaymentPlanOrder()
            ->doRequest();
    }

    /**
     * rounding
     */

    public function testCreateOrderWithAmountIncAndDeliverWithAmountExvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $campaigncode = TestUtil::getGetPaymentPlanParamsForTesting();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(1239.876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->usePaymentPlanPayment($campaigncode)
            ->doRequest();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(999.90)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->setOrderId($order->sveaOrderId)
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCountryCode("SE")
            ->deliverPaymentPlanOrder()
            ->doRequest();
//            print_r($request);
        $this->assertEquals(1, $request->accepted);

    }

    public function testCreateOrderWithAmountExAndDeliverWithAmountIncvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $campaigncode = TestUtil::getGetPaymentPlanParamsForTesting();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(9999.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->usePaymentPlanPayment($campaigncode)
            ->doRequest();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(12398.76)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->setOrderId($order->sveaOrderId)
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCountryCode("SE")
            ->deliverPaymentPlanOrder()
            ->doRequest();
//            print_r($request);
        $this->assertEquals(1, $request->accepted);

    }

    public function testCreateOrderWithFeesAsAmountIncAndDeliverWithAmountExvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $campaigncode = TestUtil::getGetPaymentPlanParamsForTesting();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(1239.876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountIncVat(123.9876)
                ->setVatPercent(24)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountIncVat(123.9876)
                ->setVatPercent(24)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->usePaymentPlanPayment($campaigncode)
            ->doRequest();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(999.9)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountExVat(99.99)
                ->setVatPercent(24)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountExVat(99.99)
                ->setVatPercent(24)
            )
            ->setOrderId($order->sveaOrderId)
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCountryCode("SE")
            ->deliverPaymentPlanOrder()
            ->doRequest();
        $this->assertEquals(1, $request->accepted);

    }

    public function testCreateOrderWithFeesAsAmountExAndDeliverWithAmountIncvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $campaigncode = TestUtil::getGetPaymentPlanParamsForTesting();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(1239.876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountIncVat(123.9876)
                ->setVatPercent(24)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountIncVat(123.9876)
                ->setVatPercent(24)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->usePaymentPlanPayment($campaigncode)
            ->doRequest();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(999.90)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountExVat(99.99)
                ->setVatPercent(24)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountExVat(99.99)
                ->setVatPercent(24)
            )
            ->setOrderId($order->sveaOrderId)
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCountryCode("SE")
            ->deliverPaymentPlanOrder()
            ->doRequest();
        $this->assertEquals(1, $request->accepted);

    }

    public function testCreateOrderWithDiscountAsAmountExAndDeliverWithAmountIncvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $campaigncode = TestUtil::getGetPaymentPlanParamsForTesting();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(1239.876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountIncVat(8)
                ->setVatPercent(24)
            )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDiscountPercent(10)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->usePaymentPlanPayment($campaigncode)
            ->doRequest();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(999.90)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountExVat(8)
                ->setVatPercent(24)
            )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDiscountPercent(10)
            )
            ->setOrderId($order->sveaOrderId)
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCountryCode("SE")
            ->deliverPaymentPlanOrder()
            ->doRequest();
        $this->assertEquals(1, $request->accepted);

    }

}
