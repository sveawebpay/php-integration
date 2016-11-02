<?php

namespace Svea\WebPay\Test\IntegrationTest\BuildOrder;

use PHPUnit_Framework_TestCase;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\WebPayAdmin;
use Svea\WebPay\Test\TestUtil;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\BuildOrder\CancelOrderRowsBuilder;


/**
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class CancelOrderRowsBuilderIntegrationTest extends PHPUnit_Framework_TestCase
{

    protected $invoiceIdToTest;
    protected $country;

    protected function setUp()
    {
        $this->country = "SE";
        $this->invoiceIdToTest = 583004;   // set this to the approved invoice set up by test_manual_setup_CreditOrderRows_testdata()
    }

    // CancelOrderRowsBuilder endpoints: cancelInvoiceOrderRows(), cancelPaymentPlanOrderRows(), cancelCardOrderRows()
    function test_CancelOrderBuilderRows_Invoice_single_row_success()
    {
        $country = "SE";
        $order = TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer($country));
        $order->addOrderRow(TestUtil::createOrderRow(1.00));
        $order->addOrderRow(TestUtil::createOrderRow(2.00));
        $orderResponse = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $orderResponse->accepted);

        $cancelBuilder = new CancelOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $cancelResponse = $cancelBuilder
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode($country)
            ->setRowToCancel(1)
            ->cancelInvoiceOrderRows()
            ->doRequest();

        $this->assertEquals(1, $cancelResponse->accepted);
    }

    // CancelOrderRowsBuilder endpoints: cancelInvoiceOrderRows(), cancelPaymentPlanOrderRows(), cancelCardOrderRows()
    function test_CancelOrderBuilderRows_Invoice_multiple_rows_success()
    {
        $country = "SE";
        $order = TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer($country));
        $order->addOrderRow(TestUtil::createOrderRow(1.00));
        $order->addOrderRow(TestUtil::createOrderRow(2.00));
        $order->addOrderRow(TestUtil::createOrderRow(3.00));
        $orderResponse = $order->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $orderResponse->accepted);

        $cancelBuilder = new CancelOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $cancelResponse = $cancelBuilder
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode($country)
            ->setRowsToCancel(array(1, 2))
            ->setRowToCancel(3)
            ->cancelInvoiceOrderRows()
            ->doRequest();

        $this->assertEquals(1, $cancelResponse->accepted);
    }

    // CancelOrderRowsBuilder endpoints: cancelInvoiceOrderRows(), cancelPaymentPlanOrderRows(), cancelCardOrderRows()
    function test_CancelOrderBuilderRows_PaymentPlan_single_row_success()
    {
        $country = "SE";
        $order = TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer($country));
        $order->addOrderRow(TestUtil::createOrderRow(1000.00));
        $order->addOrderRow(TestUtil::createOrderRow(2000.00));
        $orderResponse = $order->usePaymentPlanPayment(TestUtil::getGetPaymentPlanParamsForTesting($country))->doRequest();

        $this->assertEquals(1, $orderResponse->accepted);

        $cancelBuilder = new CancelOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $cancelResponse = $cancelBuilder
            ->setOrderId($orderResponse->sveaOrderId)
            ->setCountryCode($country)
            ->setRowToCancel(2)
            ->cancelPaymentPlanOrderRows()
            ->doRequest();

        $this->assertEquals(1, $cancelResponse->accepted);
    }

    /**
     * test_manual_CancelOrderBuilderRows_Card_single_row_success_step_1
     *
     */
    function test_manual_CancelOrderBuilderRows_Card_single_row_success_step_1()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for test_manual_CancelOrderBuilderRows_Card_single_row_success_step_1, step 1'
        );

        // 1. remove (put in a comment) the above code to enable the test
        // 2. run the test, and get the paymenturl from the output
        // 3. go to the paymenturl and complete the transaction manually, making note of the response transactionid
        // 4. enter the transactionid into test_manual_queryOrder_queryCard_order_step_2() below and run the test

        $orderLanguage = "sv";
        $returnUrl = "https://test.sveaekonomi.se/webpay-admin/admin/merchantresponsetest.xhtml";
        $ipAddress = "127.0.0.1";

        // create order w/three rows
        $order = \Svea\WebPay\Test\TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer("SE")->setIpAddress($ipAddress));

        // 2x100 @25 = 25000 (5000)
        // amount = 25000, vat = 5000
        $country = "SE";

        $order
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity(2)
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
            );

        $response = $order
            ->setClientOrderNumber("foo" . date('c'))
            ->usePayPageCardOnly()
            ->setPayPageLanguage($orderLanguage)
            ->setReturnUrl($returnUrl)
            ->getPaymentUrl();

        // check that request was accepted
        $this->assertEquals(1, $response->accepted);

        // print the url to use to confirm the transaction
        print_r("\n test_manual_CancelOrderBuilderRows_Card_single_row_success_step_1(): " . $response->testurl . "\n ");
    }

    /**
     * test_manual_CancelOrderBuilderRows_Card_single_row_success_step_2
     *
     * run this test manually after you've performed a direct bank transaction and have gotten the transaction details needed
     */
    function test_manual_CancelOrderBuilderRows_Card_single_row_success_step_2()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'test_manual_CancelOrderBuilderRows_Card_single_row_success_step_2, step 2'
        );

        // 1. remove (put in a comment) the above code to enable the test
        // 2. set $createdOrderId to the transactionid from the transaction log of the request done by following the url from step 1 above.
        // 3. below is an example of the xml generated by paypage for the request in step 1 above, along with the transaction id, for reference.

        $createdOrderId = 590634;
        $country = "SE";

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode($country);
        $queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest();

        //print_r( $queryResponse);
        // 2x100 @25 = 25000 (5000)
        // amount = 25000, vat = 5000
        $this->assertEquals(1, $queryResponse->accepted);
        $this->assertEquals(25000, $queryResponse->amount);
        $this->assertEquals(5000, $queryResponse->vat);
        $this->assertEquals(25000, $queryResponse->authorizedamount);

        // cancel first order row
        $cancelBuilder = new CancelOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $cancelOrderRowsBuilder = $cancelBuilder
            ->setOrderId($createdOrderId)
            ->setCountryCode($country)
            ->setRowToCancel(1)
            ->addNumberedOrderRows($queryResponse->numberedOrderRows);
        $cancelOrderRowsResponse = $cancelOrderRowsBuilder->cancelCardOrderRows()->doRequest();
        $this->assertEquals(1, $cancelOrderRowsResponse->accepted);

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode($country);

        $query2Response = $queryOrderBuilder->queryCardOrder()->doRequest();

        //print_r( $query2Response);
        // 2x100 @25 = 25000 (5000)     <- credited
        // amount = 25000 -25000, vat = 5000 
        $this->assertEquals(1, $query2Response->accepted);
        $this->assertEquals(1, $query2Response->accepted);
        $this->assertEquals(25000, $query2Response->amount);
        $this->assertEquals(5000, $query2Response->vat);
        $this->assertEquals(00000, $query2Response->authorizedamount);
        $this->assertEquals("ANNULLED", $query2Response->status);
    }

    /**
     * test_manual_CancelOrderBuilderRows_Card_single_row_success_step_1
     *
     */
    function test_manual_CancelOrderBuilderRows_Card_multiple_rows_success_step_1()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for test_manual_CancelOrderBuilderRows_Card_single_row_success_step_1, step 1'
        );

        // 1. remove (put in a comment) the above code to enable the test
        // 2. run the test, and get the paymenturl from the output
        // 3. go to the paymenturl and complete the transaction manually, making note of the response transactionid
        // 4. enter the transactionid into test_manual_queryOrder_queryCard_order_step_2() below and run the test

        $orderLanguage = "sv";
        $returnUrl = "http://foo.bar.com";
        $ipAddress = "127.0.0.1";

        // create order w/three rows
        $order = \Svea\WebPay\Test\TestUtil::createOrderWithoutOrderRows(TestUtil::createIndividualCustomer("SE")->setIpAddress($ipAddress));

        // 2x100 @25 = 25000 (5000)
        // 1x100 @25 = 12500 (2500)
        // 1x100 @12 = 11200 (1200)
        // amount = 48700, vat = 8700
        $country = "SE";

        $order
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity(2)
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(100.00)
                ->setVatPercent(25)
            )
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(100.00)
                ->setVatPercent(12)
            );

        $response = $order
            ->usePayPageCardOnly()
            ->setPayPageLanguage($orderLanguage)
            ->setReturnUrl($returnUrl)
            ->getPaymentUrl();

        // check that request was accepted
        $this->assertEquals(1, $response->accepted);

        // print the url to use to confirm the transaction
        //print_r( "\n test_manual_CancelOrderBuilderRows_Card_single_row_success_step_1(): " . $response->testurl ."\n ");
    }

    /**
     * test_manual_CancelOrderBuilderRows_Card_single_row_success_step_2
     *
     * run this test manually after you've performed a direct bank transaction and have gotten the transaction details needed
     */
    function test_manual_CancelOrderBuilderRows_Card_multiple_rows_success_step_2()
    {

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'test_manual_CancelOrderBuilderRows_Card_single_row_success_step_2, step 2'
        );

        // 1. remove (put in a comment) the above code to enable the test
        // 2. set $createdOrderId to the transactionid from the transaction log of the request done by following the url from step 1 above.
        // 3. below is an example of the xml generated by paypage for the request in step 1 above, along with the transaction id, for reference.

        $createdOrderId = 583620;
        $country = "SE";

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode($country);
        $queryResponse = $queryOrderBuilder->queryCardOrder()->doRequest();

        //print_r( $queryResponse);
        // 2x100 @25 = 25000 (5000)
        // 1x100 @25 = 12500 (2500)
        // 1x100 @12 = 11200 (1200)
        // amount = 48700, vat = 8700
        $this->assertEquals(1, $queryResponse->accepted);
        $this->assertEquals(48700, $queryResponse->amount);
        $this->assertEquals(8700, $queryResponse->vat);
        $this->assertEquals(48700, $queryResponse->authorizedamount);

        // cancel second, third order row
        $cancelBuilder = new CancelOrderRowsBuilder(ConfigurationService::getDefaultConfig());
        $cancelOrderRowsBuilder = $cancelBuilder
            ->setOrderId($createdOrderId)
            ->setCountryCode($country)
            ->setRowsToCancel(array(2, 3))
            ->addNumberedOrderRows($queryResponse->numberedOrderRows);
        $cancelOrderRowsResponse = $cancelOrderRowsBuilder->cancelCardOrderRows()->doRequest();
        $this->assertEquals(1, $cancelOrderRowsResponse->accepted);

        // query orderrows
        $queryOrderBuilder = WebPayAdmin::queryOrder(ConfigurationService::getDefaultConfig())
            ->setOrderId($createdOrderId)
            ->setCountryCode($country);

        $query2Response = $queryOrderBuilder->queryCardOrder()->doRequest();

        //print_r( $query2Response);
        // 2x100 @25 = 25000 (5000)
        // 1x100 @25 = 12500 (2500)     <- credited
        // 1x100 @12 = 11200 (1200)     <- credited
        // amount = 48700-12500-11200, vat = 8700
        $this->assertEquals(1, $query2Response->accepted);
        $this->assertEquals(1, $query2Response->accepted);
        $this->assertEquals(48700, $query2Response->amount);
        $this->assertEquals(8700, $query2Response->vat);
        $this->assertEquals(25000, $query2Response->authorizedamount);
        $this->assertEquals("AUTHORIZED", $query2Response->status);
    }

}

?>