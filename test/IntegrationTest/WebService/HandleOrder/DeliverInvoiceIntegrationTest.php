<?php
// Integration tests should not need to use the namespace

namespace Svea\WebPay\Test\IntegrationTest\WebService\HandleOrder;

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Test\TestUtil;
use PHPUnit_Framework_TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\DistributionType;
use Svea\WebPay\BuildOrder\DeliverOrderBuilder;
use Svea\WebPay\WebService\HandleOrder\DeliverInvoice;


/**
 * @author jona-lit
 */
class DeliverInvoiceIntegrationTest extends PHPUnit_Framework_TestCase
{

    /**
     * Function to use in testfunctions
     * @return SveaOrderId
     */
    private function getInvoiceOrderId()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::createOrder($config)
            ->addOrderRow(TestUtil::createOrderRow())
            ->addCustomerDetails(WebPayItem::individualCustomer()->setNationalIdNumber(194605092222))
            ->setCountryCode("SE")
            ->setCustomerReference("33")
            ->setOrderDate("2012-12-12")
            ->setCurrency("SEK")
            ->useInvoicePayment()
            ->doRequest();

        return $request->sveaOrderId;
    }

    public function testDeliverInvoiceOrder()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = WebPay::deliverOrder($config);
        $request = $orderBuilder
            ->addOrderRow(TestUtil::createOrderRow())
            ->setOrderId($orderId)
            ->setNumberOfCreditDays(1)
            ->setCountryCode("SE")
            ->setInvoiceDistributionType('Post')//Post or Email
            ->deliverInvoiceOrder()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);
        $this->assertEquals(0, $request->resultcode);
        $this->assertEquals(250, $request->amount);
        $this->assertEquals('Invoice', $request->orderType);
        //Invoice specifics
        //$this->assertEquals(0000, $request->invoiceId); //differs in every test
        //$this->assertEquals(date(), $request->dueDate); //differs in every test
        //$this->assertEquals(date(), $request->invoiceDate); //differs in every test
        $this->assertEquals('Post', $request->invoiceDistributionType);
        //$this->assertEquals('Invoice', $request->contractNumber); //for paymentplan
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    public function testDeliverInvoiceOrder_missing_setOrderId_throws_ValidationException()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = WebPay::deliverOrder($config);
        $request = $orderBuilder
            ->addOrderRow(TestUtil::createOrderRow())
            //->setOrderId($orderId)
            ->setNumberOfCreditDays(1)
            ->setCountryCode("SE")
            ->setInvoiceDistributionType('Post')//Post or Email
            ->deliverInvoiceOrder()
            ->doRequest();
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     *
     * bypasses Svea\WebPay\WebPay::deliverOrders, as 2.0 allows deliverOrder w/o orderRows
     */
    public function test_DeliverInvoice_missing_addOrderRow_throws_ValidationException()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = new DeliverOrderBuilder($config);
        $orderBuilder = $orderBuilder
            //->addOrderRow(Svea\WebPay\Test\TestUtil::createOrderRow())
            ->setOrderId($orderId)
            ->setNumberOfCreditDays(1)
            ->setCountryCode("SE")
            ->setInvoiceDistributionType('Post')//Post or Email
        ;
        $deliverInvoiceObject = new DeliverInvoice($orderBuilder);
        $response = $deliverInvoiceObject->doRequest();
    }

    /**
     * @expectedException \Svea\WebPay\BuildOrder\Validator\ValidationException
     */
    public function testDeliverInvoiceOrder_missing_setInvoiceDistributionType_throws_ValidationException()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderId = $this->getInvoiceOrderId();
        $orderBuilder = WebPay::deliverOrder($config);
        $request = $orderBuilder
            ->addOrderRow(TestUtil::createOrderRow())
            ->setOrderId($orderId)
            ->setNumberOfCreditDays(1)
            ->setCountryCode("SE")
            //->setInvoiceDistributionType('Post')//Post or Email
            ->deliverInvoiceOrder()
            ->doRequest();
    }

    /**
     * rounding**
     */

    public function testDeliverOrderWithAmountExVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(80.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountExVat(80.00)
                ->setVatPercent(24)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountExVat(80.00)
                ->setVatPercent(24)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountExVat(8)
                ->setVatPercent(24)
            )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDiscountPercent(10)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(80.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountExVat(80.00)
                ->setVatPercent(24)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountExVat(80.00)
                ->setVatPercent(24)
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
            ->deliverInvoiceOrder()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);

    }

    public function testDeliverOrderWithAmountIncVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(80.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountIncVat(80.00)
                ->setVatPercent(24)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountIncVat(80.00)
                ->setVatPercent(24)
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
            ->useInvoicePayment()
            ->doRequest();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(80.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountIncVat(80.00)
                ->setVatPercent(24)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountIncVat(80.00)
                ->setVatPercent(24)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountIncVat(8)
                ->setVatPercent(24)
            )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDiscountPercent(10)
            )
            ->setOrderId($order->sveaOrderId)
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->doRequest();

        $this->assertEquals(1, $request->accepted);

    }

    public function testCreateOrderWithAmountIncAndDeliverWithAmountExvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->setOrderId($order->sveaOrderId)
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->doRequest();
        $this->assertEquals(1, $request->accepted);

    }

    public function testCreateOrderWithAmountExAndDeliverWithAmountIncvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()
            ->doRequest();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->setOrderId($order->sveaOrderId)
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->doRequest();
//            print_r($request);
        $this->assertEquals(1, $request->accepted);

    }

    public function testCreateOrderWithFeesAsAmountIncAndDeliverWithAmountExvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
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
            ->useInvoicePayment()
            ->doRequest();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
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
            ->deliverInvoiceOrder()
            ->doRequest();
        $this->assertEquals(1, $request->accepted);

    }

    public function testCreateOrderWithFeesAsAmountExAndDeliverWithAmountIncvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
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
            ->useInvoicePayment()
            ->doRequest();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
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
            ->deliverInvoiceOrder()
            ->doRequest();
        $this->assertEquals(1, $request->accepted);

    }

    public function testCreateOrderWithDiscountAsAmountExAndDeliverWithAmountIncvat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $order = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
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
            ->useInvoicePayment()
            ->doRequest();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
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
            ->deliverInvoiceOrder()
            ->doRequest();
        $this->assertEquals(1, $request->accepted);

    }

}
