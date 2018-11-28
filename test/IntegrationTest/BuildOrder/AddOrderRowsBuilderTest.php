<?php

namespace Svea\WebPay\Test\IntegrationTest\BuildOrder;

use \PHPUnit\Framework\TestCase;
use Svea\WebPay\BuildOrder\AddOrderRowsBuilder;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\WebPayItem;

/**
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class AddOrderRowsBuilderIntegrationTest extends \PHPUnit\Framework\TestCase
{

    protected $invoiceIdToTest;
    protected $country;

    protected function setUp()
    {
        $this->country = "SE";
        $this->invoiceIdToTest = 123456;   // set this to the approved invoice set up by test_manual_setup_CreditOrderRows_testdata()
    }

    function test_AddOrderRows_addInvoiceOrderRows_single_row_success()
    {
        $country = "SE";
        $order = TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer($country));
        $order->addOrderRow(TestUtil::createOrderRow(1.00));
        $orderResponse = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $b_quantity = 1;
        $b_amountExVat = 100.00;
        $b_vatPercent = 12;
        $b_articleNumber = "1071e";
        $b_unit = "pcs.";
        $b_name = "B Name";
        $b_description = "B Description";
        $b_discount = 0;

        $addOrderRowsBuilder = new AddOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $addOrderRowsResponse = $addOrderRowsBuilder
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode($country)
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity($b_quantity)
                ->setAmountExVat($b_amountExVat)
                ->setVatPercent($b_vatPercent)
                ->setArticleNumber($b_articleNumber)
                ->setUnit($b_unit)
                ->setName($b_name)
                ->setDescription($b_description)
                ->setDiscountPercent($b_discount)
            )
            ->addInvoiceOrderRows()
            ->doRequest();

        $this->assertEquals(1, $addOrderRowsResponse->accepted);
        $createdOrderId = $orderResponse->sveaOrderId;
        ////print_r("test_AddOrderRows_addInvoiceOrderRows_single_row_success: "); //print_r( $createdOrderId );

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode($country);

        $queryResponse = $queryOrderBuilder->queryInvoiceOrder()->doRequest();

        ////print_r( $queryResponse);
        $this->assertEquals(1, $queryResponse->accepted);
        // assert that order rows are the same
        $this->assertEquals($b_quantity, $queryResponse->numberedOrderRows[1]->quantity);
        $this->assertEquals($b_amountExVat, $queryResponse->numberedOrderRows[1]->amountExVat);
        $this->assertEquals($b_vatPercent, $queryResponse->numberedOrderRows[1]->vatPercent);
        $this->assertEquals($b_articleNumber, $queryResponse->numberedOrderRows[1]->articleNumber);
        $this->assertEquals($b_unit, $queryResponse->numberedOrderRows[1]->unit);
        $this->assertStringStartsWith($b_name, $queryResponse->numberedOrderRows[1]->description);
        $this->assertStringEndsWith($b_description, $queryResponse->numberedOrderRows[1]->description);
        $this->assertEquals($b_discount, $queryResponse->numberedOrderRows[1]->discountPercent);
        $this->assertEquals(\Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow::ORDERROWSTATUS_NOTDELIVERED, $queryResponse->numberedOrderRows[1]->status);
        $this->assertEquals(2, $queryResponse->numberedOrderRows[1]->rowNumber);
    }

    function test_AddOrderRows_addInvoiceOrderRows_multiple_rows_success()
    {
        $country = "SE";
        $order = TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer($country));
        $order->addOrderRow(TestUtil::createOrderRow(1.00, 1));
        $orderResponse = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $b_quantity = 1;
        $b_amountExVat = 100.00;
        $b_vatPercent = 12;
        $b_articleNumber = "1071e";
        $b_unit = "pcs.";
        $b_name = "B Name";
        $b_description = "B Description";
        $b_discount = 0;

        $addOrderRowsBuilder = new AddOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $addOrderRowsResponse = $addOrderRowsBuilder
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode($country)
            ->addOrderRow(TestUtil::createOrderRow(2.00, 1))
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity($b_quantity)
                ->setAmountExVat($b_amountExVat)
                ->setVatPercent($b_vatPercent)
                ->setArticleNumber($b_articleNumber)
                ->setUnit($b_unit)
                ->setName($b_name)
                ->setDescription($b_description)
                ->setDiscountPercent($b_discount)
            )
            ->addInvoiceOrderRows()
            ->doRequest();

        $this->assertEquals(1, $addOrderRowsResponse->accepted);
        $createdOrderId = $orderResponse->sveaOrderId;
        ////print_r("test_AddOrderRows_addInvoiceOrderRows_multiple_rows_success: "); //print_r( $createdOrderId );

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode($country);

        $queryResponse = $queryOrderBuilder->queryInvoiceOrder()->doRequest();

        ////print_r( $queryResponse);
        $this->assertEquals(1, $queryResponse->accepted);
        // assert that order rows are the same
        $this->assertEquals($b_quantity, $queryResponse->numberedOrderRows[2]->quantity);
        $this->assertEquals($b_amountExVat, $queryResponse->numberedOrderRows[2]->amountExVat);
        $this->assertEquals($b_vatPercent, $queryResponse->numberedOrderRows[2]->vatPercent);
        $this->assertEquals($b_articleNumber, $queryResponse->numberedOrderRows[2]->articleNumber);
        $this->assertEquals($b_unit, $queryResponse->numberedOrderRows[2]->unit);
        $this->assertStringStartsWith($b_name, $queryResponse->numberedOrderRows[2]->description);
        $this->assertStringEndsWith($b_description, $queryResponse->numberedOrderRows[2]->description);
        $this->assertEquals($b_discount, $queryResponse->numberedOrderRows[2]->discountPercent);
        $this->assertEquals("NotDelivered", $queryResponse->numberedOrderRows[2]->status);
        $this->assertEquals(3, $queryResponse->numberedOrderRows[2]->rowNumber);
    }

    function test_AddOrderRows_addPaymentPlanOrderRows_multiple_rows_success()
    {
        $country = "SE";
        $order = TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer($country));
        $order->addOrderRow(TestUtil::createOrderRow(1000.00, 1));
        $orderResponse = $order->usePaymentPlanPayment(TestUtil::getGetPaymentPlanParamsForTesting())->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $b_quantity = 1;
        $b_amountExVat = 100.00;
        $b_vatPercent = 12;
        $b_articleNumber = "1071e";
        $b_unit = "pcs.";
        $b_name = "B Name";
        $b_description = "B Description";
        $b_discount = 0;

        $addOrderRowsBuilder = new AddOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $addOrderRowsResponse = $addOrderRowsBuilder
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode($country)
            ->addOrderRow(TestUtil::createOrderRow(2.00, 1))
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity($b_quantity)
                ->setAmountExVat($b_amountExVat)
                ->setVatPercent($b_vatPercent)
                ->setArticleNumber($b_articleNumber)
                ->setUnit($b_unit)
                ->setName($b_name)
                ->setDescription($b_description)
                ->setDiscountPercent($b_discount)
            )
            ->addPaymentPlanOrderRows()
            ->doRequest();

        $this->assertEquals(1, $addOrderRowsResponse->accepted);
        $createdOrderId = $orderResponse->sveaOrderId;
        ////print_r("test_AddOrderRows_addPaymentPlanOrderRows_multiple_rows_success: "); //print_r( $createdOrderId );

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode($country);

        $queryResponse = $queryOrderBuilder->queryPaymentPlanOrder()->doRequest();

        ////print_r( $queryResponse);
        $this->assertEquals(1, $queryResponse->accepted);
        // assert that order rows are the same
        $this->assertEquals($b_quantity, $queryResponse->numberedOrderRows[2]->quantity);
        $this->assertEquals($b_amountExVat, $queryResponse->numberedOrderRows[2]->amountExVat);
        $this->assertEquals($b_vatPercent, $queryResponse->numberedOrderRows[2]->vatPercent);
        $this->assertEquals($b_articleNumber, $queryResponse->numberedOrderRows[2]->articleNumber);
        $this->assertEquals($b_unit, $queryResponse->numberedOrderRows[2]->unit);
        $this->assertStringStartsWith($b_name, $queryResponse->numberedOrderRows[2]->description);
        $this->assertStringEndsWith($b_description, $queryResponse->numberedOrderRows[2]->description);
        $this->assertEquals($b_discount, $queryResponse->numberedOrderRows[2]->discountPercent);
        $this->assertEquals("NotDelivered", $queryResponse->numberedOrderRows[2]->status);
        $this->assertEquals(3, $queryResponse->numberedOrderRows[2]->rowNumber);
    }

    function test_AddOrderRows_addInvoiceOrderRows_specified_with_price_specified_using_inc_vat_and_ex_vat()
    {
        $country = "SE";
        $order = TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer($country));
        $order->addOrderRow(WebPayItem::orderRow()
            ->setArticleNumber("1")
            ->setQuantity(1)
            ->setAmountExVat(100.00)
            ->setVatPercent(25)
            ->setDescription("Specification")
            ->setName('Product')
            ->setUnit("st")
            ->setDiscountPercent(0)
        );
        $orderResponse = $order->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $addOrderRowsBuilder = new AddOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $addOrderRowsResponse = $addOrderRowsBuilder
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode($country)
            ->addOrderRow(WebPayItem::orderRow()
                ->setArticleNumber("1")
                ->setQuantity(1)
                //->setAmountExVat( 1.00 )
                ->setAmountIncVat(1.00 * 1.25)
                ->setVatPercent(25)
                ->setDescription("Specification")
                ->setName('Product')
                ->setUnit("st")
                ->setDiscountPercent(0)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setArticleNumber("1")
                ->setQuantity(1)
                ->setAmountExVat(4.00)
                ->setAmountIncVat(4.00 * 1.25)
                //->setVatPercent(25)
                ->setDescription("Specification")
                ->setName('Product')
                ->setUnit("st")
                ->setDiscountPercent(0)
            )
            ->addInvoiceOrderRows()
            ->doRequest();

        $this->assertEquals(1, $addOrderRowsResponse->accepted);
    }
}

?>