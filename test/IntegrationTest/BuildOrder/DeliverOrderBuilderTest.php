<?php

namespace Svea\WebPay\Test\IntegrationTest\BuildOrder;

use PHPUnit_Framework_TestCase;
use Svea\WebPay\Config\ConfigurationService;
use Svea\WebPay\Constant\DistributionType;
use Svea\WebPay\WebPay;
use Svea\WebPay\WebPayItem;
use Svea\WebPay\Test\TestUtil;


/**
 * DeliverOrderBuilder test holds all tests for how to deliver orders using DeliverOrderBuilder
 *
 * @author Kristian Grossman-Madsen for Svea Svea\WebPay\WebPay
 */
class DeliverOrderBuilderIntegrationTest extends PHPUnit_Framework_TestCase
{

    public function test_deliverOrder_deliverInvoiceOrder_with_orderrows_use_DeliverOrderEU_and_is_accepted()
    {

        // create order, get orderid to deliver
        $createOrderBuilder = TestUtil::createOrder();
        $response = $createOrderBuilder->useInvoicePayment()->doRequest();
        $this->assertEquals(1, $response->accepted);

        $orderId = $response->sveaOrderId;

        $DeliverOrderBuilder = WebPay::deliverOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(TestUtil::createOrderRow())
            ->setCountryCode("SE")
            ->setOrderId($orderId)
            ->setInvoiceDistributionType(DistributionType::POST);

        $response = $DeliverOrderBuilder->deliverInvoiceOrder()->doRequest();

        ////print_r( $response );
        $this->assertEquals(1, $response->accepted);
        $this->assertInstanceOf("Svea\WebPay\WebService\WebServiceResponse\DeliverOrderResult", $response);    // deliverOrderResult => deliverOrderEU
    }

    public function test_deliverOrder_deliverInvoiceOrder_without_orderrows_use_admin_service_deliverOrders_and_is_accepted()
    {
        // create order, get orderid to deliver
        $createOrderBuilder = TestUtil::createOrder();
        $createResponse = $createOrderBuilder->useInvoicePayment()->doRequest();

        $this->assertEquals(1, $createResponse->accepted);

        $orderId = $createResponse->sveaOrderId;
        $DeliverOrderBuilder = WebPay::deliverOrder(ConfigurationService::getDefaultConfig())
            //->addOrderRow( Svea\WebPay\Test\TestUtil::createOrderRow() )
            ->setCountryCode("SE")
            ->setOrderId($orderId)
            ->setInvoiceDistributionType(DistributionType::POST);

        // example of raw deliver orders response to parse
        //
        //stdClass Object
        //(
        //    [ErrorMessage] => 
        //    [ResultCode] => 0
        //    [OrdersDelivered] => stdClass Object
        //        (
        //            [DeliverOrderResult] => stdClass Object
        //                (
        //                    [ClientId] => 79021
        //                    [DeliveredAmount] => 250.00
        //                    [DeliveryReferenceNumber] => 1033890
        //                    [OrderType] => Invoice
        //                    [SveaOrderId] => 414157
        //                )
        //
        //        )
        //
        //)        
        $deliverResponse = $DeliverOrderBuilder->deliverInvoiceOrder()->doRequest();

        ////print_r( $deliverResponse );        
        //Svea\WebPay\AdminService\AdminServiceResponse\DeliverOrdersResponse Object
        //(
        //    [clientId] => 79021
        //    [amount] => 250.00
        //    [invoiceId] => 
        //    [contractNumber] => 
        //    [orderType] => Invoice
        //    [orderId] => 414168
        //    [accepted] => 1
        //    [resultcode] => 0
        //    [errormessage] => 
        //)

        $this->assertInstanceOf("Svea\WebPay\AdminService\AdminServiceResponse\DeliverOrdersResponse", $deliverResponse);
        $this->assertEquals(1, $deliverResponse->accepted);
        $this->assertEquals(0, $deliverResponse->resultcode);
        $this->assertEquals(null, $deliverResponse->errormessage);

        $this->assertEquals(79021, $deliverResponse->clientId);
        $this->assertEquals(250.00, $deliverResponse->amount);
        $this->assertStringMatchesFormat("%d", $deliverResponse->invoiceId);   // %d => an unsigned integer value
        $this->assertEquals(null, $deliverResponse->contractNumber);
        $this->assertEquals("Invoice", $deliverResponse->orderType);
        $this->assertStringMatchesFormat("%d", $deliverResponse->orderId);   // %d => an unsigned integer value
    }

    // orderrows are ignored by the service for paymentplan orders
    public function test_deliverOrder_deliverPaymentPlanOrder_with_orderrows_use_DeliverOrderEU_and_is_accepted()
    {

        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(1000.00)
                ->setVatPercent(25)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate(date('c'));
        $response = $order->usePaymentPlanPayment(TestUtil::getGetPaymentPlanParamsForTesting())->doRequest();

        $this->assertEquals(1, $response->accepted);

        $orderId = $response->sveaOrderId;

        $DeliverOrderBuilder = WebPay::deliverOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(1000.00)
                ->setVatPercent(25)
            )
            ->setCountryCode("SE")
            ->setOrderId($orderId);

        $response = $DeliverOrderBuilder->deliverPaymentPlanOrder()->doRequest();

        ////print_r( $response );
        $this->assertEquals(1, $response->accepted);
        $this->assertInstanceOf("Svea\WebPay\WebService\WebServiceResponse\DeliverOrderResult", $response);
    }

    public function test_deliverOrder_deliverPaymentPlanOrder_without_orderrows_use_DeliverOrderEU_and_is_accepted()
    {

        $order = WebPay::createOrder(ConfigurationService::getDefaultConfig())
            ->addOrderRow(WebPayItem::orderRow()
                ->setQuantity(1)
                ->setAmountExVat(1000.00)
                ->setVatPercent(25)
            )
            ->addCustomerDetails(TestUtil::createIndividualCustomer("SE"))
            ->setCountryCode("SE")
            ->setCurrency("SEK")
            ->setOrderDate(date('c'));
        $response = $order->usePaymentPlanPayment(TestUtil::getGetPaymentPlanParamsForTesting())->doRequest();

        $this->assertEquals(1, $response->accepted);

        $orderId = $response->sveaOrderId;

        $DeliverOrderBuilder = WebPay::deliverOrder(ConfigurationService::getDefaultConfig())
            //->addOrderRow( Svea\WebPay\WebPayItem::orderRow()
            //    ->setQuantity(1)
            //    ->setAmountExVat(1000.00)
            //    ->setVatPercent(25)
            //)
            ->setCountryCode("SE")
            ->setOrderId($orderId);

        $response = $DeliverOrderBuilder->deliverPaymentPlanOrder()->doRequest();

        ////print_r( $response );
        $this->assertEquals(1, $response->accepted);
        $this->assertInstanceOf("Svea\WebPay\WebService\WebServiceResponse\DeliverOrderResult", $response);
    }

    public function test_manual_deliverOrder_deliverCardOrder_use_ConfirmTransaction_and_is_accepted()
    {
        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'skeleton for manual test, needs a pre-existing card transactionId with status AUTHORIZED'
        );

        // 1. remove (put in a comment) the above code to enable the test
        // 2. run the test, and check status of transaction in backoffice logs

        $orderId = 585714;  // pre-existing card transactionId with status AUTHORIZED  

        $DeliverOrderBuilder = WebPay::deliverOrder(ConfigurationService::getDefaultConfig())
            ->setCountryCode("SE")
            ->setOrderId($orderId);

        $response = $DeliverOrderBuilder->deliverCardOrder()->doRequest();

        ////print_r( $response );
        $this->assertEquals(1, $response->accepted);
        $this->assertInstanceOf("Svea\WebPay\HostedService\HostedResponse\HostedAdminResponse\ConfirmTransactionResponse", $response);
    }
}

?>