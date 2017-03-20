<?php
// Integration tests should not need to use the namespace

namespace Svea\WebPay\Test\IntegrationTest;

use PHPUnit_Framework_TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\DistributionType;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\WebPayItem;

/**
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class WebPayAdminIntegrationTest extends PHPUnit_Framework_TestCase
{

    /// cancelOrder()
    // TODO

    /// queryOrder()
    // invoice
    public function test_queryOrder_queryInvoiceOrder_is_accepted()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(99.99)// => 123.9876 inc
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $queryResponse = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryInvoiceOrder()->doRequest();
        $this->assertEquals(1, $queryResponse->accepted);
    }

    // paymentplan
    public function test_queryOrder_queryPaymentPlanOrder_is_accepted()
    {
        $config = ConfigurationService::getDefaultConfig();
        $orderResponse = WebPay::createOrder($config)
            ->addOrderRow(
                WebPayItem::orderRow()
                    ->setAmountExVat(999.9)// => 12398.76 inc
                    ->setVatPercent(24)
                    ->setQuantity(1)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setOrderDate("2012-12-12")
            ->usePaymentPlanPayment(TestUtil::getGetPaymentPlanParamsForTesting())->doRequest();
        $this->assertEquals(1, $orderResponse->accepted);

        $queryResponse = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode('SE')
            ->queryPaymentPlanOrder()->doRequest();
        $this->assertEquals(1, $queryResponse->accepted);
    }

    // card
    function test_queryOrder_queryCardOrder()
    {
        // Set the below to match the transaction, then run the test.
        $transactionId = 590177;

        $request = WebPayAdmin::queryOrder(
            ConfigurationService::getSingleCountryConfig(
                "SE",
                "foo", "bar", "123456", // invoice
                "foo", "bar", "123456", // paymentplan
                "foo", "bar", "123456", // accountCredit
                "1200", // merchantid, secret
                "27f18bfcbe4d7f39971cb3460fbe7234a82fb48f985cf22a068fa1a685fe7e6f93c7d0d92fee4e8fd7dc0c9f11e2507300e675220ee85679afa681407ee2416d",
                false // prod = false
            )
        )
            ->setTransactionId(strval($transactionId))
            ->setCountryCode("SE");
        $response = $request->queryCardOrder()->doRequest();

        $this->assertEquals(1, $response->accepted);

        $this->assertEquals($transactionId, $response->transactionId);
        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow", $response->numberedOrderRows[0]);
        $this->assertEquals("Soft213s", $response->numberedOrderRows[0]->articleNumber);
        $this->assertEquals("1.0", $response->numberedOrderRows[0]->quantity);
        $this->assertEquals("st", $response->numberedOrderRows[0]->unit);
        $this->assertEquals(3212.00, $response->numberedOrderRows[0]->amountExVat);   // amount = 401500, vat = 80300 => 3212.00 @25%
        $this->assertEquals(25, $response->numberedOrderRows[0]->vatPercent);
        $this->assertEquals("Soft", $response->numberedOrderRows[0]->name);
//        $this->assertEquals( "Specification", $response->numberedOrderRows[1]->description );
        $this->assertEquals(0, $response->numberedOrderRows[0]->vatDiscount);

        $this->assertInstanceOf("Svea\WebPay\BuildOrder\RowBuilders\NumberedOrderRow", $response->numberedOrderRows[1]);
        $this->assertEquals("07", $response->numberedOrderRows[1]->articleNumber);
        $this->assertEquals("1.0", $response->numberedOrderRows[1]->quantity);
        $this->assertEquals("st", $response->numberedOrderRows[1]->unit);
        $this->assertEquals(0, $response->numberedOrderRows[1]->amountExVat);   // amount = 401500, vat = 80300 => 3212.00 @25%
        $this->assertEquals(0, $response->numberedOrderRows[1]->vatPercent);
        $this->assertEquals("Sits: Hatfield Beige 6", $response->numberedOrderRows[1]->name);
//        $this->assertEquals( "Specification", $response->numberedOrderRows[1]->description );
        $this->assertEquals(0, $response->numberedOrderRows[1]->vatDiscount);
    }


    /// creditOrderRows()
    // invoice
    public function test_creditOrderRows_creditInvoiceOrderRows_returns_CreditOrderRowsRequest()
    {
        $creditOrderRowsBuilder = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig());
        $request = $creditOrderRowsBuilder->creditInvoiceOrderRows();
        $this->assertInstanceOf("Svea\WebPay\AdminService\CreditInvoiceRowsRequest", $request);
    }

    // card
    public function test_creditOrderRows_creditCardOrderRows_returns_CreditTransaction()
    {
        $creditOrderRowsBuilder = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            ->setCountryCode("SE")
            ->setOrderId(123456)
            ->addNumberedOrderRow(TestUtil::createNumberedOrderRow(100.00, 1, 1))
            ->setRowToCredit(1);
        $request = $creditOrderRowsBuilder->creditCardOrderRows();
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedAdminRequest\CreditTransaction", $request);
    }

    // direct bank
    public function test_creditOrderRows_creditDirectBankOrderRows_returns_CreditTransaction()
    {
        $creditOrderRowsBuilder = WebPayAdmin::creditOrderRows(ConfigurationService::getDefaultConfig())
            ->setCountryCode("SE")
            ->setOrderId(123456)
            ->addNumberedOrderRow(TestUtil::createNumberedOrderRow(100.00, 1, 1))
            ->setRowToCredit(1);
        $request = $creditOrderRowsBuilder->creditDirectBankOrderRows();
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedAdminRequest\CreditTransaction", $request);
    }

    /// addOrderRows()
    // invoice
    // paymentplan
    public function test_addOrderRows_addInvoiceOrderRows_returns_AddOrderRowsRequest()
    {
        $addOrderRowsBuilder = WebPayAdmin::addOrderRows(ConfigurationService::getDefaultConfig());
        $request = $addOrderRowsBuilder->addInvoiceOrderRows();
        $this->assertInstanceOf("Svea\WebPay\AdminService\AddOrderRowsRequest", $request);
    }

    public function test_addOrderRows_addPaymentPlanOrderRows_returns_AddOrderRowsRequest()
    {
        $addOrderRowsBuilder = WebPayAdmin::addOrderRows(ConfigurationService::getDefaultConfig());
        $request = $addOrderRowsBuilder->addPaymentPlanOrderRows();
        $this->assertInstanceOf("Svea\WebPay\AdminService\AddOrderRowsRequest", $request);
    }

    /// updateOrderRows()
    // invoice
    public function test_updateOrderRows_updateInvoiceOrderRows_returns_UpdateOrderRowsRequest()
    {
        $updateOrderRowsBuilder = WebPayAdmin::updateOrderRows(ConfigurationService::getDefaultConfig());
        $request = $updateOrderRowsBuilder->updateInvoiceOrderRows();
        $this->assertInstanceOf("Svea\WebPay\AdminService\UpdateOrderRowsRequest", $request);
    }

    // paymentplan
    public function test_updateOrderRows_updatePaymentPlanOrderRows_returns_UpdateOrderRowsRequest()
    {
        $updateOrderRowsBuilder = WebPayAdmin::updateOrderRows(ConfigurationService::getDefaultConfig());
        $request = $updateOrderRowsBuilder->updatePaymentPlanOrderRows();
        $this->assertInstanceOf("Svea\WebPay\AdminService\UpdateOrderRowsRequest", $request);
    }

    // deliverOrderRows()
    // invoice
    public function test_deliverOrderRows_deliverInvoiceOrderRows_returns_DeliverOrderRowsRequest()
    {
        $request = WebPayAdmin::deliverOrderRows(ConfigurationService::getDefaultConfig())
            ->setCountryCode("SE")
            ->setOrderId(123456)
            ->setInvoiceDistributionType(DistributionType::POST)
            ->setRowTodeliver(1)
            ->deliverInvoiceOrderRows();
        $this->assertInstanceOf("Svea\WebPay\AdminService\DeliverOrderRowsRequest", $request);
    }

    //Svea\WebPay\WebPayAdmin::queryOrder()
    //.queryInvoiceOrder
    public function test_queryOrder_queryInvoiceOrder_single_order_row_with_invoice_fee_and_shipping_fee()
    {

        // create order using order row specified with ->setName() and ->setDescription
        $specifiedOrderRow = WebPayItem::orderRow()
            ->setAmountExVat(100.00)
            ->setVatPercent(25)
            ->setQuantity(1)
            ->setName("orderrow 1")
            ->setDescription("description 1");

        $invoiceFeeOrderRow = WebPayItem::invoiceFee()
            ->setAmountExVat(20.00)
            ->setVatPercent(25)
            ->setName("invoicefee 1")
            ->setDescription("invoicefee description 1");

        $shippingFeeOrderRow = WebPayItem::shippingFee()
            ->setAmountExVat(40.00)
            ->setVatPercent(25)
            ->setName("shippingfee 1")
            ->setDescription("shippingfee description 1");

        $order = TestUtil::createOrderWithoutOrderRows()
            ->addOrderRow($specifiedOrderRow)
            ->addOrderRow($invoiceFeeOrderRow)
            ->addOrderRow($shippingFeeOrderRow);

        $createOrderResponse = $order->useInvoicePayment()->doRequest();

        //print_r( $createOrderResponse );
        $this->assertInstanceOf("Svea\WebPay\WebService\WebServiceResponse\CreateOrderResponse", $createOrderResponse);
        $this->assertTrue($createOrderResponse->accepted);

        $createdOrderId = $createOrderResponse->sveaOrderId;

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode("SE");

        $queryResponse = $queryOrderBuilder->queryInvoiceOrder()->doRequest();

        //print_r( $queryResponse);
        $this->assertEquals(1, $queryResponse->accepted);

        $this->assertEquals(1, $queryResponse->numberedOrderRows[0]->rowNumber);
        $this->assertEquals(1.00, $queryResponse->numberedOrderRows[0]->quantity);
        $this->assertEquals(100.00, $queryResponse->numberedOrderRows[0]->amountExVat);
        $this->assertEquals(25, $queryResponse->numberedOrderRows[0]->vatPercent);
        $this->assertEquals(null, $queryResponse->numberedOrderRows[0]->name);
        $this->assertEquals("orderrow 1: description 1", $queryResponse->numberedOrderRows[0]->description);

        $this->assertEquals(2, $queryResponse->numberedOrderRows[1]->rowNumber);
        $this->assertEquals(1.00, $queryResponse->numberedOrderRows[1]->quantity);
        $this->assertEquals(20.00, $queryResponse->numberedOrderRows[1]->amountExVat);
        $this->assertEquals(25, $queryResponse->numberedOrderRows[1]->vatPercent);
        $this->assertEquals(null, $queryResponse->numberedOrderRows[1]->name);
        $this->assertEquals("invoicefee 1: invoicefee description 1", $queryResponse->numberedOrderRows[1]->description);

        $this->assertEquals(3, $queryResponse->numberedOrderRows[2]->rowNumber);
        $this->assertEquals(1.00, $queryResponse->numberedOrderRows[2]->quantity);
        $this->assertEquals(40.00, $queryResponse->numberedOrderRows[2]->amountExVat);
        $this->assertEquals(25, $queryResponse->numberedOrderRows[2]->vatPercent);
        $this->assertEquals(null, $queryResponse->numberedOrderRows[2]->name);
        $this->assertEquals("shippingfee 1: shippingfee description 1", $queryResponse->numberedOrderRows[2]->description);
    }

    public function test_queryOrder_queryInvoiceOrder_multiple_order_rows()
    {

        // create order using order row specified with ->setName() and ->setDescription
        $specifiedOrderRow = WebPayItem::orderRow()
            ->setAmountExVat(100.00)// recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)// recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)// required
            ->setName("orderrow 1")// optional
            ->setDescription("description 1")       // optional
        ;

        // create order using order row specified with ->setName() and ->setDescription
        $specifiedOrderRow2 = WebPayItem::orderRow()
            ->setAmountExVat(100.00)// recommended to specify price using AmountExVat & VatPercent
            ->setVatPercent(25)// recommended to specify price using AmountExVat & VatPercent
            ->setQuantity(1)// required
            ->setName("orderrow 2")// optional
            ->setDescription("description 2")       // optional
        ;

        $order = TestUtil::createOrderWithoutOrderRows()
            ->addOrderRow($specifiedOrderRow)
            ->addOrderRow($specifiedOrderRow2);

        $createOrderResponse = $order->useInvoicePayment()->doRequest();

        ////print_r( $createOrderResponse );
        $this->assertInstanceOf("Svea\WebPay\WebService\WebServiceResponse\CreateOrderResponse", $createOrderResponse);
        $this->assertTrue($createOrderResponse->accepted);

        $createdOrderId = $createOrderResponse->sveaOrderId;

        // WPA::queryOrder()
        // ->queryInvoiceOrder()
        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode("SE");

        $queryResponse = $queryOrderBuilder->queryInvoiceOrder()->doRequest();

        //print_r( $queryResponse);
        $this->assertEquals(1, $queryResponse->accepted);

        // assert that order rows are the same
        $this->assertEquals(1, $queryResponse->accepted);

        $this->assertEquals(1, $queryResponse->numberedOrderRows[0]->rowNumber);
        $this->assertEquals(1.00, $queryResponse->numberedOrderRows[0]->quantity);
        $this->assertEquals(100.00, $queryResponse->numberedOrderRows[0]->amountExVat);
        $this->assertEquals(25, $queryResponse->numberedOrderRows[0]->vatPercent);
        $this->assertEquals(null, $queryResponse->numberedOrderRows[0]->name);
        $this->assertEquals("orderrow 1: description 1", $queryResponse->numberedOrderRows[0]->description);

        $this->assertEquals(2, $queryResponse->numberedOrderRows[1]->rowNumber);
        $this->assertEquals(1.00, $queryResponse->numberedOrderRows[1]->quantity);
        $this->assertEquals(100.00, $queryResponse->numberedOrderRows[1]->amountExVat);
        $this->assertEquals(25, $queryResponse->numberedOrderRows[1]->vatPercent);
        $this->assertEquals(null, $queryResponse->numberedOrderRows[1]->name);
        $this->assertEquals("orderrow 2: description 2", $queryResponse->numberedOrderRows[1]->description);
    }

    //-queryPaymentPlanOrder
    // TODO

    //.queryCardOrder
    public function test_queryOrder_queryCardOrder_single_order_row()
    {

        // created w/java package TODO make self-contained using webdriver to create card order
        $createdOrderId = 587673;

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode("SE");

        $queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest();

        //print_r( $queryResponse);
        $this->assertEquals(1, $queryResponse->accepted);

        $this->assertEquals(1, $queryResponse->numberedOrderRows[0]->rowNumber);
        $this->assertEquals(1.00, $queryResponse->numberedOrderRows[0]->quantity);
        $this->assertEquals(100.00, $queryResponse->numberedOrderRows[0]->amountExVat);
        $this->assertEquals(25, $queryResponse->numberedOrderRows[0]->vatPercent);
        $this->assertEquals("orderrow 1", $queryResponse->numberedOrderRows[0]->name);
        $this->assertEquals("description 1", $queryResponse->numberedOrderRows[0]->description);
    }

    public function test_queryOrder_queryCardOrder_multiple_order_rows()
    {

        // created w/java package TODO make self-contained using webdriver to create card order
        $createdOrderId = 587679;

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode("SE");

        $queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest();

        //print_r( $queryResponse);
        $this->assertEquals(1, $queryResponse->accepted);

        $this->assertEquals(1, $queryResponse->numberedOrderRows[0]->rowNumber);
        $this->assertEquals(1.00, $queryResponse->numberedOrderRows[0]->quantity);
        $this->assertEquals(100.00, $queryResponse->numberedOrderRows[0]->amountExVat);
        $this->assertEquals(25, $queryResponse->numberedOrderRows[0]->vatPercent);
        $this->assertEquals("orderrow 1", $queryResponse->numberedOrderRows[0]->name);
        $this->assertEquals("description 1", $queryResponse->numberedOrderRows[0]->description);

        $this->assertEquals(2, $queryResponse->numberedOrderRows[1]->rowNumber);
        $this->assertEquals(1.00, $queryResponse->numberedOrderRows[1]->quantity);
        $this->assertEquals(100.00, $queryResponse->numberedOrderRows[1]->amountExVat);
        $this->assertEquals(25, $queryResponse->numberedOrderRows[1]->vatPercent);
        $this->assertEquals("orderrow 2", $queryResponse->numberedOrderRows[1]->name);
        $this->assertEquals("description 2", $queryResponse->numberedOrderRows[1]->description);
    }

    //-queryDirectBankOrder
    // TODO
}
