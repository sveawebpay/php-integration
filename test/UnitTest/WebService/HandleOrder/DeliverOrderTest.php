<?php

namespace Svea\WebPay\Test\UnitTest\WebService\HandleOrder;

use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Test\TestUtil;
use PHPUnit_Framework_TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\DistributionType;


/**
 * @author Anneli Halld'n, Daniel Brolund for Svea Webpay
 */
class DeliverOrderTest extends PHPUnit_Framework_TestCase
{

    public function testBuildRequest()
    {
        $config = ConfigurationService::getDefaultConfig();
        $handler = WebPay::deliverOrder($config);
        $request = $handler
            ->setOrderId("id");

        $this->assertEquals("id", $request->orderId);
    }

    public function testDeliverInvoiceDistributionType()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderBuilder = WebPay::deliverOrder($config);
        $request = $orderBuilder
            ->addOrderRow(TestUtil::createOrderRow())
            ->setOrderId("id")
            ->setNumberOfCreditDays(1)
            ->setCountryCode("SE")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals('Post', $request->request->DeliverOrderInformation->DeliverInvoiceDetails->InvoiceDistributionType);
    }

    public function testDeliverInvoiceOrder()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderBuilder = WebPay::deliverOrder($config);
        $request = $orderBuilder
            ->addOrderRow(TestUtil::createOrderRow())
            ->addDiscount(WebPayItem::fixedDiscount()->setAmountIncVat(10))
            ->addFee(WebPayItem::shippingFee()
                ->setShippingId('33')
                ->setName('shipping')
                ->setDescription("Specification")
                ->setAmountExVat(50)
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
            )
            ->setOrderId("id")
            ->setNumberOfCreditDays(1)
            ->setCountryCode("SE")
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setCreditInvoice("id")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals("Product: Specification", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Description);
        $this->assertEquals(100.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(2, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->DiscountPercent);
        //discount
        $this->assertEquals(-8.0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        //shippingfee
        $this->assertEquals("33", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->ArticleNumber);
        $this->assertEquals("shipping: Specification", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->Description);
        $this->assertEquals(50, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->NumberOfUnits);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->DiscountPercent);

        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->NumberOfCreditDays);
        $this->assertEquals("Post", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->InvoiceDistributionType);
        $this->assertEquals(true, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->IsCreditInvoice);
        $this->assertEquals("id", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->InvoiceIdToCredit);
        $this->assertEquals("id", $request->request->DeliverOrderInformation->SveaOrderId);
        $this->assertEquals("Invoice", $request->request->DeliverOrderInformation->OrderType);
    }

    public function testDeliverPaymentPlanOrder()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderBuilder = WebPay::deliverOrder($config);

        $request = $orderBuilder
            ->addOrderRow(TestUtil::createOrderRow())
            ->setCountryCode("SE")
            ->setOrderId("id")
            ->deliverPaymentPlanOrder()
            ->prepareRequest();
        $this->assertEquals("id", $request->request->DeliverOrderInformation->SveaOrderId);
        $this->assertEquals("PaymentPlan", $request->request->DeliverOrderInformation->OrderType);
    }

    public function testNewDeliverInvoiceOrderRow()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(TestUtil::createOrderRow());
        $request = $request->setOrderId("id")
            ->setNumberOfCreditDays(1)
            ->setCountryCode("SE")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals("Product: Specification", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Description);
        $this->assertEquals(100.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(2, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->DiscountPercent);
    }

    public function testDeliverOrderWithInvoiceFeeAndFixedDiscount()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(TestUtil::createOrderRow())
            ->addFee(WebPayItem::invoiceFee()
                ->setName('Svea fee')
                ->setDescription("Fee for invoice")
                ->setAmountExVat(50)
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setDiscountId("1")
                ->setAmountIncVat(100.00)
                ->setUnit("st")
                ->setDescription("FixedDiscount")
                ->setName("Fixed")
            );
        $request = $request->setOrderId("id")
            ->setNumberOfCreditDays(1)
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals("Product: Specification", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Description);
        $this->assertEquals(100.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(2, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->DiscountPercent);
        //invoicefee
        $this->assertEquals("", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals(50.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals("Svea fee: Fee for invoice", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->DiscountPercent);
        //fixeddiscount
        $this->assertEquals("1", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->ArticleNumber);
        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->NumberOfUnits);
        $this->assertEquals(-80.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals("Fixed: FixedDiscount", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->Description);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->DiscountPercent);
    }

    public function testDeliverOrderWithShippingFeeAndRelativeDiscount()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(TestUtil::createOrderRow())
            ->addFee(WebPayItem::shippingFee()
                ->setShippingId(1)
                ->setName('shipping')
                ->setDescription("Specification")
                ->setAmountExVat(50)
                ->setUnit("st")
                ->setVatPercent(25)
                ->setDiscountPercent(0)
            )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDiscountId("1")
                ->setDiscountPercent(50)
                ->setUnit("st")
                ->setName('Relative')
                ->setDescription("RelativeDiscount")
            );
        $request = $request->setOrderId("id")
            ->setNumberOfCreditDays(1)
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->ArticleNumber);
        $this->assertEquals("Product: Specification", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Description);
        $this->assertEquals(100.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(2, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->NumberOfUnits);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->DiscountPercent);
        //shipping
        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->ArticleNumber);
        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->NumberOfUnits);
        $this->assertEquals(50.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals("shipping: Specification", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->Description);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->DiscountPercent);
        //relative discount
        $this->assertEquals("1", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->ArticleNumber);
        $this->assertEquals(1, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->NumberOfUnits);
        $this->assertEquals(-100.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals("Relative: RelativeDiscount", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->Description);
        $this->assertEquals("st", $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->Unit);
        $this->assertEquals(25, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->DiscountPercent);
    }

    /**
     * Tests for rounding**
     */

    public function testDeliverOrderWithAmountExVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(80.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(80.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(80.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(80.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

    }

    public function testDeliverFeeSetAsExVatAndVatPercentWhenPriceSetAsExVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
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
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(80.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(80.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

        $this->assertEquals(80.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PriceIncludingVat);

    }

    public function testDeliverDiscountSetAsExVatWhenPriceSetAsExVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(80.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountExVat(8)
                ->setVatPercent(24)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(80.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(-8, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

    }

    public function testDeliverDiscountSetAsExVatAndVatPercentWhenPriceSetAsExVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(80.00)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountExVat(8)
                ->setVatPercent(0))
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(80.00, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(-8, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

    }

    public function testDeliverDiscountPercentAndVatPercentWhenPriceSetAsExVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDiscountPercent(10)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(-9.999, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

    }

    public function testDeliverOrderRowPriceSetAsInkVatAndVatPercentSetAmountAsIncVat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(123.9876, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertTrue($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

    }

    public function testDeliverFeeSetAsIncVatAndVatPercentWhenPriceSetAsIncVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountIncVat(100.00)
                ->setVatPercent(24)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountIncVat(100.00)
                ->setVatPercent(24)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(123.9876, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertTrue($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(100, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertTrue($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

        $this->assertEquals(100, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertTrue($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PriceIncludingVat);

    }

    public function testDeliverDiscountSetAsIncVatWhenPriceSetAsIncVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountIncVat(10)
                ->setVatPercent(0))
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(123.9876, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertTrue($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(-10, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(0, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertTrue($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

    }

    public function testDiscountPercentAndVatPercentWhenPriceSetAsIncVatAndVatPercent()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDiscountPercent(10)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(123.9876, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertTrue($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(-12.39876, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertTrue($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

    }

    public function testDeliverOrderSetAsIncVatAndExVat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setAmountExVat(99.99)
                    ->setQuantity(1)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(123.9876, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertTrue($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

    }

    public function testOrderAndFeesSetAsIncVatAndExVat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(1230.9876)
                    ->setAmountExVat(990.99)
                    ->setQuantity(1)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountIncVat(123.9876)->setAmountExVat(99.99)
            )
            ->addFee(WebPayItem::invoiceFee()
                ->setAmountIncVat(123.9876)->setAmountExVat(99.99)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(123.9876, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertTrue($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

        $this->assertEquals(123.9876, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertTrue($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PriceIncludingVat);

    }

    public function testDeliverOrderAndFixedDiscountSetAsIncVat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setAmountExVat(99.99)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountIncVat(12.39876)
                ->setVatPercent(24)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(123.9876, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertTrue($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(-12.39876, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertTrue($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

    }

    public function testDeliverOrderSetAsIncVatAndExVatAndRelativeDiscount()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setAmountExVat(99.99)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDiscountPercent(10)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(123.9876, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertTrue($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(-12.39876, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertTrue($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

    }

    public function testDeliverOrderSetWithMixedMethods1()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
                    ->setAmountIncVat(123.9876)
                    ->setQuantity(1)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PriceIncludingVat);

    }

    public function testDeliverOrderSetWithMixedMethods2()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setName('incvat')
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setName('exvat')
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setName('exvat')
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PriceIncludingVat);

    }

    public function testDeliverOrderSetWithMixedOrderRowAndFee()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addFee(
                WebPayItem::invoiceFee()
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountExVat(99.99)
                ->setVatPercent(24)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PriceIncludingVat);

    }

    public function testDeliverOrderSetWithMixedOrderRowAndFeeAndVatPercentSet()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addFee(
                WebPayItem::invoiceFee()
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
            )
            ->addFee(WebPayItem::shippingFee()
                ->setAmountIncVat(123.9876)
                ->setVatPercent(24)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PriceIncludingVat);

    }

    public function testDeliverOrderAndFixedDiscountSetWithMixedVat()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountExVat(9.999)
                ->setVatPercent(24)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(-9.999, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

    }

    public function testDeliverOrderAndFixedDiscountSetWithMixedVat2()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountIncVat(12.39876)
                ->setVatPercent(24)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(-9.999, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

    }

    public function testDeliverOrderAndFixedDiscountSetWithMixedVat3()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setAmountExVat(99.99)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::fixedDiscount()
                ->setAmountExVat(9.999)
                ->setVatPercent(24)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(-9.999, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

    }

    public function testDeliverOrderSetAsMixedVatAndRelativeDiscount()
    {
        $config = ConfigurationService::getDefaultConfig();
        $request = WebPay::deliverOrder($config);
        $request = $request
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountIncVat(123.9876)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addDiscount(WebPayItem::relativeDiscount()
                ->setDiscountPercent(5)
            )
            ->setOrderId("id")
            ->setInvoiceDistributionType(DistributionType::POST)//Post or Email
            ->setCreditInvoice("id")
            ->setCountryCode("SE")
            ->deliverInvoiceOrder()
            ->prepareRequest();

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][0]->PriceIncludingVat);

        $this->assertEquals(99.99, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][1]->PriceIncludingVat);

        $this->assertEquals(-9.999, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PricePerUnit);
        $this->assertEquals(24, $request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->VatPercent);
        $this->assertFalse($request->request->DeliverOrderInformation->DeliverInvoiceDetails->OrderRows['OrderRow'][2]->PriceIncludingVat);

    }

}
